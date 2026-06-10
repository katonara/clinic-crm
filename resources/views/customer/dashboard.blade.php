@extends('layouts.customer')
@section('title', 'My Dashboard')

@section('content')
<div x-data="customerDashboard()" x-init="start()">

    <div class="flex items-center justify-between mb-6">
        <h1 class="text-2xl font-bold text-gray-800">Welcome, {{ auth()->user()->name }}!</h1>
        <div class="flex items-center space-x-2 text-xs text-gray-400">
            <span class="relative flex h-2 w-2">
                <span class="animate-ping absolute inline-flex h-full w-full rounded-full bg-green-400 opacity-75"></span>
                <span class="relative inline-flex rounded-full h-2 w-2 bg-green-500"></span>
            </span>
            <span class="hidden sm:inline">Live</span>
        </div>
    </div>

    {{-- Quick actions --}}
    <div class="grid grid-cols-2 gap-4 mb-8">
        <a href="{{ route('customer.bookings.create') }}" class="bg-blue-600 text-white rounded-xl p-4 text-center hover:bg-blue-700 transition-colors">
            <svg class="w-8 h-8 mx-auto mb-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"/></svg>
            <span class="font-medium">New Booking</span>
        </a>
        <a href="{{ route('customer.bookings.history') }}" class="bg-white border border-gray-200 text-gray-700 rounded-xl p-4 text-center hover:bg-gray-50 transition-colors">
            <svg class="w-8 h-8 mx-auto mb-2 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/></svg>
            <span class="font-medium">Booking History</span>
        </a>
    </div>

    {{-- Active Packages --}}
    <template x-if="packages.length > 0">
        <div class="mb-8">
            <h2 class="text-lg font-semibold text-gray-800 mb-4">My Active Packages</h2>
            <div class="grid sm:grid-cols-2 gap-3">
                <template x-for="pkg in packages" :key="pkg.id">
                    <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-4">
                        <p class="font-medium text-gray-800" x-text="pkg.name"></p>
                        <p class="text-xs text-gray-500 mt-0.5" x-text="pkg.service"></p>
                        <div class="mt-3">
                            <div class="flex justify-between text-xs text-gray-500 mb-1">
                                <span x-text="pkg.used + '/' + pkg.total + ' sessions used'"></span>
                                <span x-text="pkg.remaining + ' remaining'" class="font-medium text-blue-600"></span>
                            </div>
                            <div class="w-full bg-gray-100 rounded-full h-2">
                                <div class="bg-blue-500 h-2 rounded-full transition-all duration-500" :style="'width:' + pkg.progress + '%'"></div>
                            </div>
                        </div>
                    </div>
                </template>
            </div>
        </div>
    </template>

    {{-- Upcoming bookings (live) --}}
    <h2 class="text-lg font-semibold text-gray-800 mb-4">Upcoming Appointments</h2>
    <div class="space-y-3 mb-8">
        <template x-for="b in upcoming" :key="b.id">
            <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-4 transition-all duration-300">
                <div class="flex justify-between items-start">
                    <div>
                        <p class="font-medium text-gray-800" x-text="b.service_name"></p>
                        <p class="text-sm text-gray-500" x-text="b.date + ' at ' + b.time"></p>
                        <p class="text-sm text-gray-400" x-text="b.staff_name"></p>
                    </div>
                    <span class="px-2 py-1 text-xs font-medium rounded-full"
                          :class="{
                              'bg-yellow-100 text-yellow-700': b.status === 'pending',
                              'bg-green-100 text-green-700': b.status === 'confirmed',
                              'bg-purple-100 text-purple-700': b.status === 'rescheduled',
                          }"
                          x-text="b.status_label"></span>
                </div>

                {{-- Status explanation --}}
                <template x-if="b.status === 'pending'">
                    <div class="mt-2 bg-yellow-50 border border-yellow-100 rounded-lg px-3 py-2">
                        <p class="text-xs text-yellow-700">
                            <svg class="w-3.5 h-3.5 inline mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                            Your booking is being reviewed by the clinic. You'll be notified once approved.
                        </p>
                    </div>
                </template>

                <template x-if="b.can_cancel">
                    <div class="mt-3 flex space-x-2">
                        <form method="POST" :action="'/customer/bookings/' + b.id + '/cancel'" onsubmit="return confirm('Are you sure you want to cancel this booking?')">
                            @csrf @method('PATCH')
                            <button type="submit" class="px-4 py-2 text-sm border border-red-200 text-red-600 rounded-lg hover:bg-red-50">Cancel</button>
                        </form>
                        <form method="POST" :action="'/customer/bookings/' + b.id + '/reschedule'" onsubmit="return confirm('Request reschedule for this booking?')">
                            @csrf @method('PATCH')
                            <input type="hidden" name="reason" value="Customer requested reschedule">
                            <button type="submit" class="px-4 py-2 text-sm border border-purple-200 text-purple-600 rounded-lg hover:bg-purple-50">Reschedule</button>
                        </form>
                    </div>
                </template>
            </div>
        </template>

        <template x-if="upcoming.length === 0">
            <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-8 text-center">
                <p class="text-gray-400 mb-4">No upcoming appointments.</p>
                <a href="{{ route('customer.bookings.create') }}" class="inline-block bg-blue-600 text-white px-6 py-3 rounded-lg font-medium hover:bg-blue-700">Book Now</a>
            </div>
        </template>
    </div>

    {{-- Past bookings --}}
    @if($pastBookings->isNotEmpty())
    <h2 class="text-lg font-semibold text-gray-800 mb-4">Recent History</h2>
    <div class="space-y-3">
        @foreach($pastBookings as $booking)
        <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-4">
            <div class="flex justify-between items-start">
                <div>
                    <p class="font-medium text-gray-800">{{ $booking->service->name }}</p>
                    <p class="text-sm text-gray-500">{{ $booking->booking_date->format('d M Y') }}</p>
                </div>
                <x-badge :color="$booking->statusBadgeColor()">{{ ucfirst($booking->status) }}</x-badge>
            </div>
        </div>
        @endforeach
    </div>
    @endif
</div>

{{-- Status change toast --}}
<div id="customer-toast" class="fixed bottom-20 lg:bottom-4 right-4 z-50 space-y-2" x-data="{ toasts: [] }">
    <template x-for="t in toasts" :key="t.id">
        <div x-show="t.show" x-transition
             :class="t.status === 'confirmed' ? 'bg-green-500' : t.status === 'cancelled' ? 'bg-red-500' : 'bg-blue-500'"
             class="text-white px-4 py-3 rounded-lg shadow-lg max-w-sm text-sm">
            <span x-text="t.message"></span>
        </div>
    </template>
</div>

@push('scripts')
<script>
function customerDashboard() {
    return {
        upcoming: @json($upcomingBookingsJson),
        packages: [],
        prevStatuses: {},
        interval: null,

        start() {
            this.upcoming.forEach(b => this.prevStatuses[b.id] = b.status);
            this.fetchLive();
            this.interval = setInterval(() => this.fetchLive(), 10000);
        },

        async fetchLive() {
            try {
                const res = await window.fetch('{{ route("customer.live.dashboard") }}', {
                    headers: { 'Accept': 'application/json' }
                });
                if (!res.ok) return;
                const data = await res.json();

                // Detect status changes and show toast
                data.upcoming.forEach(b => {
                    const prev = this.prevStatuses[b.id];
                    if (prev && prev !== b.status) {
                        this.showToast(b);
                    }
                    this.prevStatuses[b.id] = b.status;
                });

                this.upcoming = data.upcoming;
                this.packages = data.packages;
            } catch (e) {}
        },

        showToast(booking) {
            const el = document.getElementById('customer-toast');
            if (el && el._x_dataStack) {
                const data = el._x_dataStack[0];
                const id = Date.now();
                let msg = '';
                if (booking.status === 'confirmed') msg = 'Your ' + booking.service_name + ' booking has been approved!';
                else if (booking.status === 'cancelled') msg = 'Your ' + booking.service_name + ' booking was rejected.';
                else msg = 'Your ' + booking.service_name + ' booking status changed to ' + booking.status;

                data.toasts.push({ id, message: msg, status: booking.status, show: true });
                setTimeout(() => {
                    const t = data.toasts.find(t => t.id === id);
                    if (t) t.show = false;
                    setTimeout(() => data.toasts = data.toasts.filter(t => t.id !== id), 300);
                }, 5000);
            }
        },

        destroy() { clearInterval(this.interval); }
    }
}
</script>
@endpush
@endsection
