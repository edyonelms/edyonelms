<?php

namespace App\Livewire\Components;

use Illuminate\Support\Facades\Auth;
use Livewire\Component;

class Notification extends Component
{
    /** Recent notifications for the logged-in user (guarded — empty if table absent). */
    public function getItemsProperty()
    {
        $user = Auth::user();
        if (!$user) {
            return collect();
        }

        try {
            return $user->notifications()->latest()->limit(20)->get();
        } catch (\Throwable $e) {
            return collect();
        }
    }

    public function markAllAsRead(): void
    {
        try {
            Auth::user()?->unreadNotifications->markAsRead();
        } catch (\Throwable $e) {
            // ignore — table may not exist yet
        }

        $this->dispatch('notifications-updated');
    }

    public function render()
    {
        return view('livewire.components.notification', [
            'items' => $this->items,
        ]);
    }
}
