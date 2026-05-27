<div class="p-4">
    @php
        $typeColors = [
            'about_app'      => 'bg-blue-100 text-blue-600',
            'privacy_policy' => 'bg-indigo-100 text-indigo-600',
            'terms_condition'=> 'bg-purple-100 text-purple-600',
            'terms_of_use'   => 'bg-violet-100 text-violet-600',
            'rating'         => 'bg-amber-100 text-amber-600',
            'enquiry'        => 'bg-cyan-100 text-cyan-600',
            'support'        => 'bg-rose-100 text-rose-600',
            'credit'         => 'bg-emerald-100 text-emerald-600',
        ];
    @endphp

    <div class="flex items-center justify-between mb-4">
        <h2 class="text-lg font-semibold text-gray-800">Notifications</h2>
        @if ($items->isNotEmpty())
            <button wire:click="markAllAsRead"
                class="text-blue-500 hover:text-blue-700 text-sm font-medium">
                Mark all as read
            </button>
        @endif
    </div>

    <div class="space-y-3">
        @forelse ($items as $item)
            @php
                $data    = $item->data ?? [];
                $type    = $data['type'] ?? 'activity';
                $color   = $typeColors[$type] ?? 'bg-gray-100 text-gray-500';
                $unread  = is_null($item->read_at);
            @endphp
            <div class="p-3 border rounded-lg transition-colors {{ $unread ? 'bg-blue-50/40 border-blue-100' : 'border-gray-200 hover:bg-gray-50' }}">
                <div class="flex items-start space-x-3">
                    <div class="flex-shrink-0">
                        <div class="w-10 h-10 rounded-full flex items-center justify-center {{ $color }}">
                            <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9" />
                            </svg>
                        </div>
                    </div>
                    <div class="flex-1 min-w-0">
                        <p class="text-sm font-medium text-gray-800">{{ $data['title'] ?? 'Notification' }}</p>
                        <p class="text-xs text-gray-500">{{ $data['body'] ?? '' }}</p>
                        <p class="text-xs text-gray-400 mt-1">{{ $item->created_at?->diffForHumans() }}</p>
                    </div>
                    @if ($unread)
                        <div class="flex-shrink-0">
                            <span class="w-2 h-2 bg-blue-500 rounded-full inline-block"></span>
                        </div>
                    @endif
                </div>
            </div>
        @empty
            <div class="py-12 text-center">
                <div class="w-12 h-12 mx-auto rounded-full bg-gray-100 flex items-center justify-center mb-3">
                    <svg class="w-6 h-6 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9" />
                    </svg>
                </div>
                <p class="text-sm text-gray-400">No notifications yet</p>
            </div>
        @endforelse
    </div>
</div>
