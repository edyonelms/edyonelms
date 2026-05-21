<?php

namespace App\Livewire\Chat;

use App\Models\Chat\Conversation;
use App\Models\Chat\Message;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Livewire\Component;
use Livewire\WithFileUploads;
use WireUi\Traits\WireUiActions;

/**
 * Near-real-time messaging between the School panel (admin / sub-admin) and the
 * Accounts panel of the SAME organization. Uses Livewire polling for live
 * updates (no third-party / websocket infra). One-to-one conversations; the
 * schema also supports groups.
 */
class Messenger extends Component
{
    use WithFileUploads, WireUiActions;

    public ?int   $conversationId = null;
    public ?int   $selectedUserId = null;
    public string $body           = '';
    public        $attachment     = null;
    public string $contactSearch  = '';

    /** Roles allowed to use this panel-to-panel chat. */
    protected const CHAT_ROLES = ['admin', 'sub-admin', 'accounts'];

    public function mount(): void
    {
        if (!in_array(Auth::user()->role, self::CHAT_ROLES, true)) {
            abort(403);
        }
    }

    protected function meId(): int
    {
        return (int) Auth::id();
    }

    protected function orgId(): int
    {
        return (int) Auth::user()->organization_id;
    }

    /**
     * Users on the *other* panel within the same organization — the people the
     * current user is allowed to chat with.
     */
    protected function candidateQuery()
    {
        $query = User::where('organization_id', $this->orgId())
            ->where('is_active', 1);

        if (Auth::user()->role === 'accounts') {
            // Accounts user talks to the school's admins.
            $query->whereIn('role', ['admin', 'sub-admin']);
        } else {
            // Admin / sub-admin talk to the accounts team.
            $query->where('role', 'accounts');
        }

        return $query;
    }

    public function openChat(int $userId): void
    {
        $other = $this->candidateQuery()->where('id', $userId)->first();
        if (!$other) {
            return; // not a valid counterpart — ignore
        }

        $conversation = $this->findOrCreateConversation($other->id);

        $this->conversationId = $conversation->id;
        $this->selectedUserId = $other->id;
        $this->reset(['body']);
        $this->attachment = null;
        $this->resetValidation();
        $this->markRead();
    }

    protected function findOrCreateConversation(int $otherId): Conversation
    {
        $meId  = $this->meId();
        $orgId = $this->orgId();

        $existing = Conversation::where('organization_id', $orgId)
            ->whereHas('participants', fn($q) => $q->where('user_id', $meId))
            ->whereHas('participants', fn($q) => $q->where('user_id', $otherId))
            ->withCount('participants')
            ->get()
            ->firstWhere('participants_count', 2);

        if ($existing) {
            return $existing;
        }

        $conversation = Conversation::create([
            'organization_id' => $orgId,
            'last_message_at' => now(),
        ]);
        $conversation->participants()->attach([$meId, $otherId]);

        return $conversation;
    }

    /** Only conversations the current user actually belongs to. */
    protected function currentConversation(): ?Conversation
    {
        if ($this->conversationId === null) {
            return null;
        }

        return Conversation::where('organization_id', $this->orgId())
            ->whereHas('participants', fn($q) => $q->where('user_id', $this->meId()))
            ->find($this->conversationId);
    }

    public function sendMessage(?string $body = null): void
    {
        // The composer keeps its draft in Alpine (poll-safe) and passes it in.
        if ($body !== null) {
            $this->body = $body;
        }

        $conversation = $this->currentConversation();
        if (!$conversation) {
            return;
        }

        $this->validate([
            'body'       => 'nullable|string|max:5000',
            'attachment' => 'nullable|file|max:10240|mimes:jpg,jpeg,png,webp,gif,pdf,doc,docx,xls,xlsx,ppt,pptx,txt',
        ], [
            'attachment.max'   => 'Attachment must not exceed 10 MB.',
            'attachment.mimes' => 'Only images and common documents are allowed.',
        ]);

        if (trim($this->body) === '' && !$this->attachment) {
            $this->addError('body', 'Type a message or attach a file.');
            return;
        }

        $data = [
            'conversation_id' => $conversation->id,
            'sender_id'       => $this->meId(),
            'body'            => trim($this->body) ?: null,
        ];

        if ($this->attachment) {
            $path = $this->attachment->store('chat/attachments', 's3');
            Storage::disk('s3')->setVisibility($path, 'public');

            $mime = (string) $this->attachment->getMimeType();
            $data['attachment_url']  = Storage::disk('s3')->url($path);
            $data['attachment_name'] = $this->attachment->getClientOriginalName();
            $data['attachment_type'] = str_starts_with($mime, 'image/') ? 'image' : 'file';
        }

        Message::create($data);
        $conversation->update(['last_message_at' => now()]);

        $this->reset(['body']);
        $this->attachment = null;
        $this->resetValidation();
    }

    /** Mark all messages from the other party in the open conversation as read. */
    public function markRead(): void
    {
        if ($this->conversationId === null) {
            return;
        }

        Message::where('conversation_id', $this->conversationId)
            ->where('sender_id', '!=', $this->meId())
            ->whereNull('read_at')
            ->update(['read_at' => now()]);

        $conversation = $this->currentConversation();
        $conversation?->participants()->updateExistingPivot($this->meId(), ['last_read_at' => now()]);
    }

    public function render()
    {
        $meId = $this->meId();

        // Contact list (other-panel users) with their conversation, last message and unread count.
        $contacts = $this->candidateQuery()
            ->when($this->contactSearch, fn($q) => $q->where(fn($q) => $q
                ->where('name', 'like', "%{$this->contactSearch}%")
                ->orWhere('email', 'like', "%{$this->contactSearch}%")))
            ->orderBy('name')
            ->get()
            ->map(function (User $u) use ($meId) {
                $conversation = Conversation::where('organization_id', $this->orgId())
                    ->whereHas('participants', fn($q) => $q->where('user_id', $meId))
                    ->whereHas('participants', fn($q) => $q->where('user_id', $u->id))
                    ->withCount('participants')
                    ->get()
                    ->firstWhere('participants_count', 2);

                $unread = 0;
                $last   = null;
                if ($conversation) {
                    $unread = Message::where('conversation_id', $conversation->id)
                        ->where('sender_id', '!=', $meId)
                        ->whereNull('read_at')
                        ->count();
                    $last = Message::where('conversation_id', $conversation->id)->latest('id')->first();
                }

                return [
                    'user'            => $u,
                    'conversation_id' => $conversation?->id,
                    'unread'          => $unread,
                    'last'            => $last,
                    'last_sort'       => $last?->id ?? 0,
                ];
            })
            ->sortByDesc('last_sort')
            ->values();

        // Active conversation messages (and keep them marked read while open).
        $messages = collect();
        if ($this->conversationId) {
            $this->markRead();
            $messages = Message::where('conversation_id', $this->conversationId)
                ->orderBy('id')
                ->get();
        }

        $other = $this->selectedUserId ? User::find($this->selectedUserId) : null;

        return view('livewire.chat.messenger', [
            'contacts'    => $contacts,
            'messages'    => $messages,
            'otherUser'   => $other,
            'myId'        => $meId,
            'panelLabel'  => Auth::user()->role === 'accounts' ? 'School Admins' : 'Accounts Team',
        ]);
    }
}
