<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'My Account - Clinic CRM')</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>
    <style>[x-cloak] { display: none !important; }</style>
</head>
<body class="bg-gray-50 min-h-screen pb-20 lg:pb-0">
    @if(session('success'))
    <div x-data="{ show: true }" x-show="show" x-init="setTimeout(() => show = false, 4000)"
         x-transition class="fixed top-4 right-4 z-50 bg-green-500 text-white px-6 py-3 rounded-lg shadow-lg">
        {{ session('success') }}
    </div>
    @endif

    {{-- Top navbar --}}
    <nav class="bg-white border-b border-gray-200 sticky top-0 z-30">
        <div class="max-w-5xl mx-auto px-4 h-16 flex items-center justify-between">
            <a href="{{ route('customer.dashboard') }}" class="text-xl font-bold text-blue-600">Clinic CRM</a>
            <div class="flex items-center space-x-3">
                <span class="text-sm text-gray-600 hidden sm:block">{{ auth()->user()->name }}</span>

                {{-- Notification bell --}}
                <div x-data="customerNotifications()" x-init="start()" class="relative">
                    <button @click="open = !open" class="relative p-2 text-gray-500 hover:text-gray-700 rounded-lg hover:bg-gray-100">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9"/></svg>
                        <span x-show="pendingCount > 0" x-cloak
                              class="absolute -top-0.5 -right-0.5 bg-red-500 text-white text-xs rounded-full min-w-[18px] h-[18px] flex items-center justify-center px-1 font-bold"
                              x-text="pendingCount"></span>
                    </button>

                    <div x-show="open" x-cloak @click.outside="open = false" x-transition
                         class="absolute right-0 mt-2 w-72 bg-white rounded-xl shadow-lg border border-gray-200 z-50 overflow-hidden">
                        <div class="p-3 border-b border-gray-100">
                            <h4 class="font-semibold text-sm text-gray-800">Notifications</h4>
                        </div>

                        <template x-for="n in notifications" :key="n.id">
                            <div class="px-3 py-2.5 border-b border-gray-50">
                                <div class="flex items-start space-x-2">
                                    <span class="mt-0.5 w-2 h-2 rounded-full flex-shrink-0"
                                          :class="n.status === 'confirmed' ? 'bg-green-500' : n.status === 'cancelled' ? 'bg-red-500' : 'bg-blue-500'"></span>
                                    <div>
                                        <p class="text-sm text-gray-700" x-text="n.message"></p>
                                        <p class="text-xs text-gray-400 mt-0.5" x-text="n.time"></p>
                                    </div>
                                </div>
                            </div>
                        </template>

                        <template x-if="notifications.length === 0">
                            <p class="px-3 py-4 text-sm text-gray-400 text-center">No new notifications</p>
                        </template>

                        <template x-if="pendingCount > 0">
                            <div class="px-3 py-2.5 bg-yellow-50 text-xs text-yellow-700 text-center">
                                <span x-text="pendingCount + ' booking(s) pending review'"></span>
                            </div>
                        </template>
                    </div>
                </div>

                <form method="POST" action="{{ route('logout') }}">
                    @csrf
                    <button type="submit" class="text-gray-400 hover:text-red-500">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"/></svg>
                    </button>
                </form>
            </div>
        </div>
    </nav>

    <main class="max-w-5xl mx-auto px-4 py-6">
        @yield('content')
    </main>

    {{-- Bottom navigation for mobile --}}
    <nav class="fixed bottom-0 left-0 right-0 bg-white border-t border-gray-200 lg:hidden z-30">
        <div class="flex justify-around py-2">
            <a href="{{ route('customer.dashboard') }}" class="flex flex-col items-center p-2 {{ request()->routeIs('customer.dashboard') ? 'text-blue-600' : 'text-gray-500' }}">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"/></svg>
                <span class="text-xs mt-1">Home</span>
            </a>
            <a href="{{ route('customer.bookings.create') }}" class="flex flex-col items-center p-2 {{ request()->routeIs('customer.bookings.create') ? 'text-blue-600' : 'text-gray-500' }}">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"/></svg>
                <span class="text-xs mt-1">Book</span>
            </a>
            <a href="{{ route('customer.bookings.history') }}" class="flex flex-col items-center p-2 {{ request()->routeIs('customer.bookings.history') ? 'text-blue-600' : 'text-gray-500' }}">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/></svg>
                <span class="text-xs mt-1">History</span>
            </a>
            <a href="{{ route('customer.profile') }}" class="flex flex-col items-center p-2 {{ request()->routeIs('customer.profile') ? 'text-blue-600' : 'text-gray-500' }}">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/></svg>
                <span class="text-xs mt-1">Profile</span>
            </a>
        </div>
    </nav>

    <script>
    function customerNotifications() {
        return {
            open: false,
            notifications: [],
            pendingCount: 0,
            interval: null,

            start() {
                this.fetch();
                this.interval = setInterval(() => this.fetch(), 10000);
            },

            async fetch() {
                try {
                    const res = await window.fetch('{{ route("customer.live.notifications") }}', {
                        headers: { 'Accept': 'application/json', 'X-Requested-With': 'XMLHttpRequest' }
                    });
                    if (!res.ok) return;
                    const data = await res.json();
                    this.notifications = data.notifications;
                    this.pendingCount = data.pending_count;
                } catch (e) {}
            },

            destroy() { clearInterval(this.interval); }
        }
    }
    </script>

    @stack('scripts')
</body>
</html>
