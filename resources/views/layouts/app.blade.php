<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'Clinic CRM')</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>
    <style>[x-cloak] { display: none !important; }</style>
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    colors: {
                        primary: { 50:'#eff6ff',100:'#dbeafe',200:'#bfdbfe',300:'#93c5fd',400:'#60a5fa',500:'#3b82f6',600:'#2563eb',700:'#1d4ed8',800:'#1e40af',900:'#1e3a8a' }
                    }
                }
            }
        }
    </script>
    @stack('styles')
</head>
<body class="bg-gray-50 min-h-screen" x-data="{ sidebarOpen: false }">

    {{-- Toast notifications --}}
    @if(session('success'))
    <div x-data="{ show: true }" x-show="show" x-init="setTimeout(() => show = false, 4000)"
         x-transition class="fixed top-4 right-4 z-50 bg-green-500 text-white px-6 py-3 rounded-lg shadow-lg">
        {{ session('success') }}
    </div>
    @endif

    @if(session('error'))
    <div x-data="{ show: true }" x-show="show" x-init="setTimeout(() => show = false, 4000)"
         x-transition class="fixed top-4 right-4 z-50 bg-red-500 text-white px-6 py-3 rounded-lg shadow-lg">
        {{ session('error') }}
    </div>
    @endif

    <div class="flex h-screen overflow-hidden">
        {{-- Sidebar overlay for mobile --}}
        <div x-show="sidebarOpen" x-transition:enter="transition-opacity ease-linear duration-300"
             x-transition:enter-start="opacity-0" x-transition:enter-end="opacity-100"
             x-transition:leave="transition-opacity ease-linear duration-300"
             x-transition:leave-start="opacity-100" x-transition:leave-end="opacity-0"
             class="fixed inset-0 bg-gray-900/50 z-40 lg:hidden" @click="sidebarOpen = false"></div>

        {{-- Sidebar --}}
        <aside :class="sidebarOpen ? 'translate-x-0' : '-translate-x-full'"
               class="fixed inset-y-0 left-0 z-50 w-64 bg-white border-r border-gray-200 transform transition-transform duration-300 ease-in-out lg:translate-x-0 lg:static lg:inset-0 flex flex-col">
            <div class="flex items-center justify-between h-16 px-6 border-b border-gray-200">
                <a href="{{ route('admin.dashboard') }}" class="text-xl font-bold text-primary-600">Clinic CRM</a>
                <button @click="sidebarOpen = false" class="lg:hidden text-gray-500">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                </button>
            </div>

            <nav class="flex-1 px-4 py-4 space-y-1 overflow-y-auto">
                @php $role = auth()->user()->role; @endphp

                <x-nav-link href="{{ route('admin.dashboard') }}" :active="request()->routeIs('admin.dashboard')">
                    <x-slot:icon><svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"/></svg></x-slot:icon>
                    Dashboard
                </x-nav-link>

                @if(in_array($role, ['admin', 'staff']))
                <x-nav-link href="{{ route('admin.patients.index') }}" :active="request()->routeIs('admin.patients.*')">
                    <x-slot:icon><svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0z"/></svg></x-slot:icon>
                    Patients
                </x-nav-link>
                @endif

                <x-nav-link href="{{ route('admin.bookings.index') }}" :active="request()->routeIs('admin.bookings.*')">
                    <x-slot:icon><svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg></x-slot:icon>
                    Bookings
                </x-nav-link>

                <x-nav-link href="{{ route('admin.calendar') }}" :active="request()->routeIs('admin.calendar')">
                    <x-slot:icon><svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/></svg></x-slot:icon>
                    Calendar
                </x-nav-link>

                <x-nav-link href="{{ route('admin.services.index') }}" :active="request()->routeIs('admin.services.*')">
                    <x-slot:icon><svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19.428 15.428a2 2 0 00-1.022-.547l-2.387-.477a6 6 0 00-3.86.517l-.318.158a6 6 0 01-3.86.517L6.05 15.21a2 2 0 00-1.806.547M8 4h8l-1 1v5.172a2 2 0 00.586 1.414l5 5c1.26 1.26.367 3.414-1.415 3.414H4.828c-1.782 0-2.674-2.154-1.414-3.414l5-5A2 2 0 009 10.172V5L8 4z"/></svg></x-slot:icon>
                    Services
                </x-nav-link>

                @if($role === 'admin')
                <x-nav-link href="{{ route('admin.staff.index') }}" :active="request()->routeIs('admin.staff.*')">
                    <x-slot:icon><svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/></svg></x-slot:icon>
                    Staff
                </x-nav-link>

                <x-nav-link href="{{ route('admin.doctors.index') }}" :active="request()->routeIs('admin.doctors.*')">
                    <x-slot:icon><svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5.121 17.804A13.937 13.937 0 0112 16c2.5 0 4.847.655 6.879 1.804M15 10a3 3 0 11-6 0 3 3 0 016 0zm6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg></x-slot:icon>
                    Doctors
                </x-nav-link>

                <x-nav-link href="{{ route('admin.schedules.index') }}" :active="request()->routeIs('admin.schedules.*')">
                    <x-slot:icon><svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/></svg></x-slot:icon>
                    Schedules
                </x-nav-link>
                @endif

                @if(in_array($role, ['admin', 'staff', 'doctor']))
                <x-nav-link href="{{ route('admin.treatment-rooms.board') }}" :active="request()->routeIs('admin.treatment-rooms.*')">
                    <x-slot:icon><svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/></svg></x-slot:icon>
                    Treatment Rooms
                </x-nav-link>
                @endif

                @if(in_array($role, ['admin', 'staff']))
                <x-nav-link href="{{ route('admin.patient-packages.index') }}" :active="request()->routeIs('admin.patient-packages.*')">
                    <x-slot:icon><svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"/></svg></x-slot:icon>
                    Packages
                </x-nav-link>
                @endif

                @if(in_array($role, ['admin', 'staff']))
                <x-nav-link href="{{ route('admin.follow-ups.index') }}" :active="request()->routeIs('admin.follow-ups.*')">
                    <x-slot:icon><svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z"/></svg></x-slot:icon>
                    Follow-ups
                </x-nav-link>
                @endif

                @if($role === 'admin')
                <x-nav-link href="{{ route('admin.whatsapp-templates.index') }}" :active="request()->routeIs('admin.whatsapp-templates.*')">
                    <x-slot:icon><svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 10h.01M12 10h.01M16 10h.01M9 16H5a2 2 0 01-2-2V6a2 2 0 012-2h14a2 2 0 012 2v8a2 2 0 01-2 2h-5l-5 5v-5z"/></svg></x-slot:icon>
                    WhatsApp Templates
                </x-nav-link>

                <x-nav-link href="{{ route('admin.settings.edit') }}" :active="request()->routeIs('admin.settings.*')">
                    <x-slot:icon><svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.066 2.573c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.573 1.066c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.066-2.573c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/></svg></x-slot:icon>
                    Settings
                </x-nav-link>
                @endif
            </nav>

            <div class="p-4 border-t border-gray-200">
                <div class="flex items-center">
                    <div class="w-8 h-8 rounded-full bg-primary-100 flex items-center justify-center text-primary-600 font-semibold text-sm">
                        {{ strtoupper(substr(auth()->user()->name, 0, 1)) }}
                    </div>
                    <div class="ml-3 flex-1 min-w-0">
                        <p class="text-sm font-medium text-gray-700 truncate">{{ auth()->user()->name }}</p>
                        <p class="text-xs text-gray-500 capitalize">{{ auth()->user()->role }}</p>
                    </div>
                    <form method="POST" action="{{ route('logout') }}">
                        @csrf
                        <button type="submit" class="text-gray-400 hover:text-red-500" title="Logout">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"/></svg>
                        </button>
                    </form>
                </div>
            </div>
        </aside>

        {{-- Main content --}}
        <div class="flex-1 flex flex-col overflow-hidden">
            {{-- Top bar --}}
            <header class="bg-white border-b border-gray-200 h-16 flex items-center justify-between px-4 lg:px-6">
                <div class="flex items-center space-x-3">
                    <button @click="sidebarOpen = true" class="lg:hidden text-gray-500 hover:text-gray-700">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"/></svg>
                    </button>
                    <h1 class="text-lg font-semibold text-gray-800">@yield('page-title', 'Dashboard')</h1>
                </div>
                <div class="flex items-center space-x-3">
                    {{-- Live indicator --}}
                    <div x-data="liveIndicator()" x-init="start()" class="flex items-center space-x-2">
                        <span class="relative flex h-2 w-2">
                            <span class="animate-ping absolute inline-flex h-full w-full rounded-full bg-green-400 opacity-75"></span>
                            <span class="relative inline-flex rounded-full h-2 w-2 bg-green-500"></span>
                        </span>
                        <span class="text-xs text-gray-400 hidden sm:inline">Live</span>
                    </div>

                    {{-- Notification bell --}}
                    <div x-data="notificationBell()" x-init="start()" class="relative">
                        <button @click="open = !open" class="relative p-2 text-gray-500 hover:text-gray-700 rounded-lg hover:bg-gray-100">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9"/></svg>
                            <span x-show="pendingCount > 0" x-cloak
                                  class="absolute -top-0.5 -right-0.5 bg-red-500 text-white text-xs rounded-full min-w-[18px] h-[18px] flex items-center justify-center px-1 font-bold"
                                  x-text="pendingCount"></span>
                        </button>

                        {{-- Dropdown --}}
                        <div x-show="open" x-cloak @click.outside="open = false"
                             x-transition
                             class="absolute right-0 mt-2 w-80 bg-white rounded-xl shadow-lg border border-gray-200 z-50 overflow-hidden">
                            <div class="p-3 border-b border-gray-100 flex items-center justify-between">
                                <h4 class="font-semibold text-sm text-gray-800">Notifications</h4>
                                <span class="text-xs text-gray-500" x-text="'Today: ' + todayCount + ' bookings'"></span>
                            </div>

                            {{-- Room alerts --}}
                            <template x-for="alert in roomAlerts" :key="alert.room">
                                <div class="px-3 py-2 bg-yellow-50 border-b border-yellow-100 flex items-start space-x-2">
                                    <svg class="w-4 h-4 text-yellow-500 mt-0.5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/></svg>
                                    <div>
                                        <p class="text-xs font-medium text-yellow-800" x-text="alert.room + ' — ' + alert.patient"></p>
                                        <p class="text-xs text-yellow-600" x-text="alert.minutes_left + ' min remaining'"></p>
                                    </div>
                                </div>
                            </template>

                            {{-- Pending bookings --}}
                            <template x-for="item in recentPending" :key="item.id">
                                <a :href="item.url" class="block px-3 py-2.5 hover:bg-gray-50 border-b border-gray-50">
                                    <div class="flex items-center justify-between">
                                        <p class="text-sm font-medium text-gray-800" x-text="item.message"></p>
                                        <span class="text-xs bg-yellow-100 text-yellow-700 px-2 py-0.5 rounded-full">Pending</span>
                                    </div>
                                    <p class="text-xs text-gray-500 mt-0.5"><span x-text="item.date"></span> at <span x-text="item.time"></span> &middot; <span x-text="item.created"></span></p>
                                </a>
                            </template>

                            <template x-if="recentPending.length === 0 && roomAlerts.length === 0">
                                <p class="px-3 py-4 text-sm text-gray-400 text-center">No new notifications</p>
                            </template>

                            <a href="{{ route('admin.bookings.index') }}?status=pending" class="block px-3 py-2.5 text-center text-sm text-blue-600 hover:bg-blue-50 border-t border-gray-100 font-medium">
                                View all pending bookings
                            </a>
                        </div>
                    </div>

                    @yield('header-actions')
                </div>
            </header>

            <main class="flex-1 overflow-y-auto p-4 lg:p-6">
                @yield('content')
            </main>
        </div>
    </div>

    {{-- Live update toast container --}}
    <div id="live-toast-container" class="fixed bottom-4 right-4 z-50 space-y-2" x-data="liveToast()">
        <template x-for="toast in toasts" :key="toast.id">
            <div x-show="toast.show" x-transition
                 :class="toast.type === 'success' ? 'bg-green-500' : toast.type === 'warning' ? 'bg-yellow-500' : 'bg-blue-500'"
                 class="text-white px-4 py-3 rounded-lg shadow-lg max-w-sm text-sm flex items-center space-x-2">
                <svg class="w-4 h-4 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                <span x-text="toast.message"></span>
            </div>
        </template>
    </div>

    <script>
    function liveIndicator() {
        return {
            start() {}
        }
    }

    function notificationBell() {
        return {
            open: false,
            pendingCount: 0,
            todayCount: 0,
            recentPending: [],
            roomAlerts: [],
            interval: null,

            start() {
                this.fetch();
                this.interval = setInterval(() => this.fetch(), 10000);
            },

            async fetch() {
                try {
                    const res = await window.fetch('{{ route("admin.live.notifications") }}', {
                        headers: { 'Accept': 'application/json', 'X-Requested-With': 'XMLHttpRequest' }
                    });
                    if (!res.ok) return;
                    const data = await res.json();
                    this.pendingCount = data.pending_count;
                    this.todayCount = data.today_count;
                    this.recentPending = data.recent_pending;
                    this.roomAlerts = data.room_alerts || [];
                } catch (e) {}
            },

            destroy() { clearInterval(this.interval); }
        }
    }

    function liveToast() {
        return {
            toasts: [],
            add(message, type = 'info') {
                const id = Date.now();
                this.toasts.push({ id, message, type, show: true });
                setTimeout(() => {
                    const t = this.toasts.find(t => t.id === id);
                    if (t) t.show = false;
                    setTimeout(() => this.toasts = this.toasts.filter(t => t.id !== id), 300);
                }, 4000);
            }
        }
    }

    window.liveToastInstance = null;
    document.addEventListener('alpine:init', () => {
        Alpine.effect(() => {
            const el = document.getElementById('live-toast-container');
            if (el && el._x_dataStack) window.liveToastInstance = el._x_dataStack[0];
        });
    });
    </script>

    @stack('scripts')
</body>
</html>
