<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="icon" type="image/png" href="{{ url('website-image/Group 11525.png') }}" />
    @wireUiScripts
    @livewireStyles
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <title>{{ $title ?? 'Edyone LMS' }}</title>

    {{-- Rich Text --}}
    <link rel="stylesheet" type="text/css" href="https://unpkg.com/trix@2.0.8/dist/trix.css">
    <script type="text/javascript" src="https://unpkg.com/trix@2.0.8/dist/trix.umd.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/js/all.min.js"></script>

    <style>
        /* Prevent any horizontal scroll at root level */
        html,
        body {
            overflow: hidden;
            height: 100%;
            width: 100%;
        }
    </style>
</head>

<body class="bg-gray-50 h-screen w-screen overflow-hidden">

    <x-notifications position="top-end" />
    <x-dialog z-index="z-50" blur="md" align="center" />

    @if (Auth::user())
        {{-- ─── SIDEBAR (fixed, never scrolls horizontally) ─── --}}
        <aside class="fixed inset-y-0 left-0 w-64 shadow-md z-50 overflow-y-auto overflow-x-hidden">
            @if (in_array(Auth::user()->role, ['super-admin', 'sub-super-admin']))
                @include('admin-components.super-admin-sidebar')
            @elseif (in_array(Auth::user()->role, ['admin', 'sub-admin']))
                @include('admin-components.admin-sidebar')
            @elseif (Auth::user()->role === 'accounts')
                @include('admin-components.accounts-sidebar')
            @endif
        </aside>
    @endif

    {{-- ─── NAVBAR (fixed, sits above everything) ─── --}}
    <header class="fixed top-0 left-0 right-0 z-40 md:pl-64">
        @livewire('components.nav-bar')
    </header>

    {{-- ─── MAIN CONTENT WRAPPER ─── --}}
    {{-- Offset left by sidebar width, offset top by navbar height --}}
    {{-- Only THIS div scrolls --}}
    <div class="fixed inset-0 md:left-64 top-16 overflow-y-auto overflow-x-hidden">

        {{-- Background decorative blobs (contained inside this wrapper) --}}
        <div class="relative min-h-full w-full">
            <div
                class="pointer-events-none absolute w-[400px] h-[400px] bg-pink-200 rounded-full opacity-30 blur-3xl top-[-50px] left-[-100px]">
            </div>
            <div
                class="pointer-events-none absolute w-[500px] h-[500px] bg-purple-200 rounded-full opacity-30 blur-3xl top-0 right-[50px]">
            </div>
            <div
                class="pointer-events-none absolute w-[300px] h-[300px] bg-orange-200 rounded-full opacity-30 blur-3xl top-[100px] right-[150px]">
            </div>

            {{-- Slot content --}}
            <div class="relative z-10">
                {{ $slot }}
            </div>
        </div>

    </div>

    @livewireScripts
    @livewireCalendarScripts

    {{-- ─── Web Push (FCM) registration — super-admin only ─── --}}
    @if (Auth::user() && in_array(Auth::user()->role, ['super-admin', 'sub-super-admin']))
        <script src="https://www.gstatic.com/firebasejs/10.12.2/firebase-app-compat.js"></script>
        <script src="https://www.gstatic.com/firebasejs/10.12.2/firebase-messaging-compat.js"></script>
        <script>
            (function () {
                if (typeof firebase === 'undefined' || !('serviceWorker' in navigator)) return;
                try {
                    firebase.initializeApp({
                        apiKey: 'AIzaSyBRZcETdNS1gcdedGB_IW8KwOSyUUXTa6w',
                        authDomain: 'edyone-lms-57e8c.firebaseapp.com',
                        projectId: 'edyone-lms-57e8c',
                        storageBucket: 'edyone-lms-57e8c.firebasestorage.app',
                        messagingSenderId: '682389969874',
                        appId: '1:682389969874:web:f9e4948399cdc52cc5c60b',
                    });

                    const messaging = firebase.messaging();
                    const VAPID_KEY = 'BGrupAUEMUzVBLV9lPd4DGYo5_9AKltHbcTOKWiFMiHlpixwoP9_qfHu_OnVBqyGbUFduqdOuCADp7sjRmPSqhY';

                    navigator.serviceWorker.register('/firebase-messaging-sw.js').then(function (registration) {
                        Notification.requestPermission().then(function (permission) {
                            if (permission !== 'granted') return;
                            messaging.getToken({ vapidKey: VAPID_KEY, serviceWorkerRegistration: registration })
                                .then(function (token) {
                                    if (!token) return;
                                    fetch('{{ route('super-admin.fcm-token') }}', {
                                        method: 'POST',
                                        headers: {
                                            'Content-Type': 'application/json',
                                            'Accept': 'application/json',
                                            'X-CSRF-TOKEN': '{{ csrf_token() }}',
                                        },
                                        body: JSON.stringify({ token: token }),
                                    }).catch(function () {});
                                })
                                .catch(function (e) { console.warn('FCM getToken failed', e); });
                        });
                    }).catch(function (e) { console.warn('FCM SW registration failed', e); });

                    // Foreground messages → show a native notification.
                    messaging.onMessage(function (payload) {
                        const n = payload.notification || {};
                        try {
                            new Notification(n.title || 'Edyone LMS', { body: n.body || '' });
                        } catch (e) {}
                    });
                } catch (e) {
                    console.warn('FCM init failed', e);
                }
            })();
        </script>
    @endif
</body>

</html>
