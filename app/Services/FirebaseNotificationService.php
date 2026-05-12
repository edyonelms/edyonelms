<?php

namespace App\Services;

use App\Models\User;
use App\Models\UserFcmToken;
use Kreait\Firebase\Contract\Messaging;
use Kreait\Firebase\Messaging\CloudMessage;
use Kreait\Firebase\Messaging\Notification as FirebaseNotification;
use Kreait\Firebase\Messaging\MulticastSendReport;

class FirebaseNotificationService
{
    protected $messaging;

    public function __construct(Messaging $messaging)
    {
        $this->messaging = $messaging;
    }

    public function sendToUser(User $user, string $title, string $body, array $data = []): bool
    {
        $tokens = $user->fcmTokens()->pluck('token')->toArray();

        if (empty($tokens)) {
            logger()->warning('No FCM tokens found for user', ['user_id' => $user->id]);
            return false;
        }

        $message = CloudMessage::new()
            ->withNotification(FirebaseNotification::create($title, $body))
            ->withData($data);

        try {
            $sendReport = $this->messaging->sendMulticast($message, $tokens);
            $this->handleFailedTokens($sendReport, $user->id);

            return true;
        } catch (\Exception $e) {
            report($e);
            return false;
        }
    }

    protected function handleFailedTokens(MulticastSendReport $sendReport, int $userId): void
    {
        if ($sendReport->hasFailures()) {
            foreach ($sendReport->failures() as $failure) {
                $target = $failure->target();
                if ($target && $target->value()) {
                    $token = $target->value();
                    logger()->error('Failed to send FCM notification', [
                        'token' => $token,
                        'reason' => $failure->error()->getMessage(),
                        'user_id' => $userId
                    ]);

                    // Remove invalid token from database
                    UserFcmToken::where('token', $token)->delete();
                }
            }
        }
    }

    public function saveFcmToken($user, $token): bool
    {
        $existingToken = UserFcmToken::where('token', $token)->first();

        if ($existingToken) {
            if ($existingToken->user_id !== $user->id) {
                $existingToken->delete();
            } else {
                return true; // Token already exists for this user
            }
        }

        // Delete any other tokens for this user
        UserFcmToken::where('user_id', $user->id)->delete();

        // Save the new token
        UserFcmToken::create([
            'user_id' => $user->id,
            'token' => $token
        ]);

        return true;
    }
}
