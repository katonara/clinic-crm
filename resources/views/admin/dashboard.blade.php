@extends('layouts.app')
@section('title', 'Dashboard')
@section('page-title', 'Dashboard')

@section('content')
<div x-data="liveDashboard()" x-init="start()">

    {{-- Live update banner --}}
    <div class="mb-4 flex items-center justify-between">
        <div class="flex items-center space-x-2 text-xs text-gray-400">
            <span class="relative flex h-2 w-2">
                <span class="animate-ping absolute inline-flex h-full w-full rounded-full bg-green-400 opacity-75"></span>
                <span class="relative inline-flex rounded-full h-2 w-2 bg-green-500"></span>
            </span>
            <span>Auto-refreshing every 10s</span>
            <span>&middot;</span>
            <span x-text="'Updated ' + lastUpdate"></span>
        </div>
        <button @click="refresh()" class="text-xs text-blue-600 hover:text-blue-700 font-medium">Refresh Now</button>
    </div>

    {{-- Stats Cards --}}
    <div class="grid grid-cols-2 sm:grid-cols-2 lg:grid-cols-4 gap-4 mb-8">
        <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-4 sm:p-6 transition-all duration-300" :class="flash.today ? 'ring-2 ring-blue-300' : ''">
            <div class="flex items-center justify-between mb-2">
                <span class="text-xs sm:text-sm text-gray-500">Today's Bookings</span>
                <div class="p-2 rounded-lg bg-blue-50"><svg class="w-5 h-5 sm:w-6 sm:h-6 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg></div>
            </div>
            <p class="text-2xl sm:text-3xl font-bold text-gray-800" x-text="stats.today">{{ $stats['today'] }}</p>
        </div>
        <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-4 sm:p-6 transition-all duration-300" :class="flash.this_week ? 'ring-2 ring-purple-300' : ''">
            <div class="flex items-center justify-between mb-2">
                <span class="text-xs sm:text-sm text-gray-500">This Week</span>
                <div class="p-2 rounded-lg bg-purple-50"><svg class="w-5 h-5 sm:w-6 sm:h-6 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"/></svg></div>
            </div>
            <p class="text-2xl sm:text-3xl font-bold text-gray-800" x-text="stats.this_week">{{ $stats['this_week'] }}</p>
        </div>
        <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-4 sm:p-6 transition-all duration-300" :class="flash.pending ? 'ring-2 ring-yellow-300' : ''">
            <div class="flex items-center justify-between mb-2">
                <span class="text-xs sm:text-sm text-gray-500">Pending</span>
                <div class="p-2 rounded-lg bg-yellow-50"><svg class="w-5 h-5 sm:w-6 sm:h-6 text-yellow-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/></svg></div>
            </div>
            <p class="text-2xl sm:text-3xl font-bold text-gray-800" x-text="stats.pending">{{ $stats['pending'] }}</p>
        </div>
        <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-4 sm:p-6 transition-all duration-300" :class="flash.revenue_month ? 'ring-2 ring-green-300' : ''">
            <div class="flex items-center justify-between mb-2">
                <span class="text-xs sm:text-sm text-gray-500">Revenue (Month)</span>
                <div class="p-2 rounded-lg bg-green-50"><svg class="w-5 h-5 sm:w-6 sm:h-6 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg></div>
            </div>
            <p class="text-xl sm:text-2xl font-bold text-gray-800">RM <span x-text="Number(stats.revenue_month).toLocaleString('en', {minimumFractionDigits:2})">{{ number_format($stats['revenue_month'], 2) }}</span></p>
        </div>
    </div>

    <div class="grid grid-cols-2 sm:grid-cols-2 lg:grid-cols-4 gap-4 mb-8">
        <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-4 sm:p-6">
            <span class="text-xs sm:text-sm text-gray-500">Confirmed</span>
            <p class="text-2xl sm:text-3xl font-bold text-blue-600 mt-1" x-text="stats.confirmed">{{ $stats['confirmed'] }}</p>
        </div>
        <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-4 sm:p-6">
            <span class="text-xs sm:text-sm text-gray-500">Completed</span>
            <p class="text-2xl sm:text-3xl font-bold text-green-600 mt-1" x-text="stats.completed">{{ $stats['completed'] }}</p>
        </div>
        <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-4 sm:p-6">
            <span class="text-xs sm:text-sm text-gray-500">Cancelled</span>
            <p class="text-2xl sm:text-3xl font-bold text-red-600 mt-1" x-text="stats.cancelled">{{ $stats['cancelled'] }}</p>
        </div>
        <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-4 sm:p-6">
            <span class="text-xs sm:text-sm text-gray-500">Total Bookings</span>
            <p class="text-2xl sm:text-3xl font-bold text-gray-800 mt-1" x-text="stats.today + stats.this_week">{{ $stats['today'] + $stats['this_week'] }}</p>
        </div>
    </div>

    {{-- Charts --}}
    <div class="grid lg:grid-cols-2 gap-6 mb-8">
        <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6">
            <h3 class="font-semibold text-gray-800 mb-4">Bookings by Status</h3>
            <div class="space-y-3">
                @php
                    $statusColors = ['pending'=>'bg-yellow-400','confirmed'=>'bg-blue-400','completed'=>'bg-green-400','cancelled'=>'bg-red-400','rescheduled'=>'bg-purple-400','no_show'=>'bg-gray-400'];
                    $total = max($bookingsByStatus->sum(), 1);
                @endphp
                @foreach($bookingsByStatus as $status => $count)
                <div>
                    <div class="flex justify-between text-sm mb-1">
                        <span class="capitalize text-gray-600">{{ str_replace('_', ' ', $status) }}</span>
                        <span class="font-medium">{{ $count }}</span>
                    </div>
                    <div class="w-full bg-gray-100 rounded-full h-2">
                        <div class="{{ $statusColors[$status] ?? 'bg-gray-400' }} h-2 rounded-full" style="width: {{ ($count / $total) * 100 }}%"></div>
                    </div>
                </div>
                @endforeach
                @if($bookingsByStatus->isEmpty())
                <p class="text-sm text-gray-400">No bookings yet.</p>
                @endif
            </div>
        </div>

        <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6">
            <h3 class="font-semibold text-gray-800 mb-4">Bookings by Service</h3>
            <div class="space-y-3">
                @php $serviceTotal = max($bookingsByService->sum(), 1); @endphp
                @foreach($bookingsByService as $name => $count)
                <div>
                    <div class="flex justify-between text-sm mb-1">
                        <span class="text-gray-600">{{ $name }}</span>
                        <span class="font-medium">{{ $count }}</span>
                    </div>
                    <div class="w-full bg-gray-100 rounded-full h-2">
                        <div class="bg-blue-400 h-2 rounded-full" style="width: {{ ($count / $serviceTotal) * 100 }}%"></div>
                    </div>
                </div>
                @endforeach
                @if($bookingsByService->isEmpty())
                <p class="text-sm text-gray-400">No bookings yet.</p>
                @endif
            </div>
        </div>
    </div>

    {{-- Upcoming Appointments (live) --}}
    <div class="bg-white rounded-xl shadow-sm border border-gray-100">
        <div class="p-6 border-b border-gray-100 flex items-center justify-between">
            <h3 class="font-semibold text-gray-800">Upcoming Appointments</h3>
            <span class="text-xs text-gray-400" x-text="bookings.length + ' upcoming'"></span>
        </div>

        {{-- Desktop table --}}
        <div class="hidden md:block overflow-x-auto">
            <table class="w-full">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Booking #</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Patient</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Service</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Date & Time</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Staff</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Status</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Action</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100">
                    <template x-for="b in bookings" :key="b.id">
                        <tr class="hover:bg-gray-50">
                            <td class="px-6 py-4 text-sm"><a :href="b.url" class="text-blue-600 hover:underline" x-text="b.booking_number"></a></td>
                            <td class="px-6 py-4 text-sm text-gray-700" x-text="b.patient_name"></td>
                            <td class="px-6 py-4 text-sm text-gray-700" x-text="b.service_name"></td>
                            <td class="px-6 py-4 text-sm text-gray-700" x-text="b.date + ' ' + b.time"></td>
                            <td class="px-6 py-4 text-sm text-gray-700" x-text="b.staff_name"></td>
                            <td class="px-6 py-4">
                                <span class="px-2 py-1 text-xs font-medium rounded-full"
                                      :class="b.status === 'pending' ? 'bg-yellow-100 text-yellow-700' : 'bg-blue-100 text-blue-700'"
                                      x-text="b.status.charAt(0).toUpperCase() + b.status.slice(1)"></span>
                            </td>
                            <td class="px-6 py-4">
                                <template x-if="b.status === 'pending'">
                                    <div class="flex space-x-1">
                                        <button @click="approveBooking(b.id)" class="px-2 py-1 text-xs bg-green-100 text-green-700 rounded hover:bg-green-200">Approve</button>
                                        <button @click="rejectBooking(b.id)" class="px-2 py-1 text-xs bg-red-100 text-red-700 rounded hover:bg-red-200">Reject</button>
                                    </div>
                                </template>
                            </td>
                        </tr>
                    </template>
                    <template x-if="bookings.length === 0">
                        <tr><td colspan="7" class="px-6 py-8 text-center text-sm text-gray-400">No upcoming appointments.</td></tr>
                    </template>
                </tbody>
            </table>
        </div>

        {{-- Mobile cards --}}
        <div class="md:hidden divide-y divide-gray-100">
            <template x-for="b in bookings" :key="b.id">
                <div class="p-4">
                    <div class="flex justify-between items-start mb-2">
                        <a :href="b.url" class="font-medium text-gray-800" x-text="b.patient_name"></a>
                        <span class="px-2 py-1 text-xs font-medium rounded-full"
                              :class="b.status === 'pending' ? 'bg-yellow-100 text-yellow-700' : 'bg-blue-100 text-blue-700'"
                              x-text="b.status === 'pending' ? 'Pending Review' : b.status.charAt(0).toUpperCase() + b.status.slice(1)"></span>
                    </div>
                    <p class="text-sm text-gray-500" x-text="b.service_name"></p>
                    <p class="text-sm text-gray-500" x-text="b.date + ' at ' + b.time"></p>
                    <template x-if="b.status === 'pending'">
                        <div class="flex space-x-2 mt-3">
                            <button @click="approveBooking(b.id)" class="flex-1 py-2 text-xs bg-green-600 text-white rounded-lg hover:bg-green-700 font-medium">Approve</button>
                            <button @click="rejectBooking(b.id)" class="flex-1 py-2 text-xs bg-red-600 text-white rounded-lg hover:bg-red-700 font-medium">Reject</button>
                        </div>
                    </template>
                </div>
            </template>
            <template x-if="bookings.length === 0">
                <div class="p-8 text-center text-sm text-gray-400">No upcoming appointments.</div>
            </template>
        </div>
    </div>
</div>

@push('scripts')
<script>
function liveDashboard() {
    return {
        stats: {
            today: {{ $stats['today'] }},
            this_week: {{ $stats['this_week'] }},
            pending: {{ $stats['pending'] }},
            confirmed: {{ $stats['confirmed'] }},
            completed: {{ $stats['completed'] }},
            cancelled: {{ $stats['cancelled'] }},
            revenue_month: {{ $stats['revenue_month'] }},
        },
        bookings: @json($upcomingBookingsJson),
        flash: {},
        lastUpdate: 'just now',
        interval: null,
        prevStats: null,

        start() {
            this.prevStats = { ...this.stats };
            this.interval = setInterval(() => this.refresh(), 10000);
        },

        async refresh() {
            try {
                const [statsRes, bookingsRes] = await Promise.all([
                    window.fetch('{{ route("admin.live.stats") }}', { headers: { 'Accept': 'application/json' } }),
                    window.fetch('{{ route("admin.live.upcoming-bookings") }}', { headers: { 'Accept': 'application/json' } }),
                ]);

                if (statsRes.ok) {
                    const newStats = await statsRes.json();
                    // Flash changed values
                    this.flash = {};
                    for (const key of Object.keys(newStats)) {
                        if (this.stats[key] !== undefined && this.stats[key] !== newStats[key]) {
                            this.flash[key] = true;
                            setTimeout(() => { this.flash[key] = false; }, 2000);
                        }
                    }
                    // Show toast for new pending
                    if (this.prevStats && newStats.pending > this.prevStats.pending) {
                        const diff = newStats.pending - this.prevStats.pending;
                        if (window.liveToastInstance) {
                            window.liveToastInstance.add(diff + ' new pending booking' + (diff > 1 ? 's' : ''), 'warning');
                        }
                    }
                    this.prevStats = { ...newStats };
                    this.stats = newStats;
                }

                if (bookingsRes.ok) {
                    this.bookings = await bookingsRes.json();
                }

                this.lastUpdate = 'just now';
                setTimeout(() => { this.lastUpdate = 'a few seconds ago'; }, 5000);
            } catch (e) {}
        },

        async approveBooking(id) {
            if (!confirm('Approve this booking?')) return;
            const csrf = document.querySelector('meta[name="csrf-token"]').content;
            try {
                const res = await window.fetch('/admin/bookings/' + id + '/status', {
                    method: 'PATCH',
                    headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': csrf, 'Accept': 'application/json' },
                    body: JSON.stringify({ status: 'confirmed' })
                });
                if (res.ok) {
                    if (window.liveToastInstance) window.liveToastInstance.add('Booking approved!', 'success');
                    this.refresh();
                }
            } catch (e) {}
        },

        async rejectBooking(id) {
            const reason = prompt('Reason for rejection (optional):');
            if (reason === null) return;
            const csrf = document.querySelector('meta[name="csrf-token"]').content;
            try {
                const res = await window.fetch('/admin/bookings/' + id + '/status', {
                    method: 'PATCH',
                    headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': csrf, 'Accept': 'application/json' },
                    body: JSON.stringify({ status: 'cancelled', reason: reason || 'Rejected by admin' })
                });
                if (res.ok) {
                    if (window.liveToastInstance) window.liveToastInstance.add('Booking rejected.', 'info');
                    this.refresh();
                }
            } catch (e) {}
        },

        destroy() { clearInterval(this.interval); }
    }
}
</script>
@endpush
@endsection
