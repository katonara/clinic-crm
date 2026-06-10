@extends('layouts.app')
@section('title', 'Bookings')
@section('page-title', 'Bookings')
@section('header-actions')
<a href="{{ route('admin.bookings.create') }}" class="bg-blue-600 text-white px-4 py-2 rounded-lg text-sm hover:bg-blue-700">+ New Booking</a>
@endsection

@section('content')
<div class="bg-white rounded-xl shadow-sm border border-gray-100">
    {{-- Filters --}}
    <div class="p-4 border-b border-gray-100">
        <form method="GET" class="grid sm:grid-cols-2 lg:grid-cols-5 gap-3">
            <input type="text" name="search" value="{{ request('search') }}" placeholder="Search..." class="px-3 py-2 border border-gray-300 rounded-lg text-sm">
            <input type="date" name="date" value="{{ request('date') }}" class="px-3 py-2 border border-gray-300 rounded-lg text-sm">
            <select name="status" class="px-3 py-2 border border-gray-300 rounded-lg text-sm">
                <option value="">All Status</option>
                @foreach(['pending','confirmed','rescheduled','completed','cancelled','no_show'] as $s)
                <option value="{{ $s }}" {{ request('status') === $s ? 'selected' : '' }}>{{ ucfirst(str_replace('_',' ',$s)) }}</option>
                @endforeach
            </select>
            <select name="payment_status" class="px-3 py-2 border border-gray-300 rounded-lg text-sm">
                <option value="">All Payments</option>
                @foreach(['unpaid','deposit_paid','paid','refunded'] as $ps)
                <option value="{{ $ps }}" {{ request('payment_status') === $ps ? 'selected' : '' }}>{{ ucfirst(str_replace('_',' ',$ps)) }}</option>
                @endforeach
            </select>
            <button type="submit" class="bg-blue-600 text-white px-4 py-2 rounded-lg text-sm">Filter</button>
        </form>
    </div>

    {{-- Desktop table --}}
    <div class="hidden md:block overflow-x-auto">
        <table class="w-full">
            <thead class="bg-gray-50">
                <tr>
                    <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Booking #</th>
                    <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Patient</th>
                    <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Service</th>
                    <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Date & Time</th>
                    <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Staff</th>
                    <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Status</th>
                    <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Payment</th>
                    <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Actions</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-100">
                @forelse($bookings as $booking)
                <tr class="hover:bg-gray-50">
                    <td class="px-4 py-3 text-sm"><a href="{{ route('admin.bookings.show', $booking) }}" class="text-blue-600 hover:underline font-medium">{{ $booking->booking_number }}</a></td>
                    <td class="px-4 py-3 text-sm text-gray-700">{{ $booking->patient->name }}</td>
                    <td class="px-4 py-3 text-sm text-gray-700">{{ $booking->service->name }}</td>
                    <td class="px-4 py-3 text-sm text-gray-700">{{ $booking->booking_date->format('d M Y') }}<br><span class="text-gray-400">{{ $booking->start_time }} - {{ $booking->end_time }}</span></td>
                    <td class="px-4 py-3 text-sm text-gray-700">{{ $booking->staffMember?->name ?? '-' }}</td>
                    <td class="px-4 py-3"><x-badge :color="$booking->statusBadgeColor()">{{ ucfirst(str_replace('_',' ',$booking->status)) }}</x-badge></td>
                    <td class="px-4 py-3"><x-badge :color="$booking->paymentBadgeColor()">{{ ucfirst(str_replace('_',' ',$booking->payment_status)) }}</x-badge></td>
                    <td class="px-4 py-3 text-sm">
                        <a href="{{ route('admin.bookings.show', $booking) }}" class="text-blue-600 hover:underline">View</a>
                    </td>
                </tr>
                @empty
                <tr><td colspan="8"><x-empty-state message="No bookings found." action="New Booking" :actionUrl="route('admin.bookings.create')" /></td></tr>
                @endforelse
            </tbody>
        </table>
    </div>

    {{-- Mobile cards --}}
    <div class="md:hidden divide-y divide-gray-100">
        @forelse($bookings as $booking)
        <a href="{{ route('admin.bookings.show', $booking) }}" class="block p-4 hover:bg-gray-50">
            <div class="flex justify-between items-start mb-2">
                <span class="font-medium text-gray-800">{{ $booking->patient->name }}</span>
                <x-badge :color="$booking->statusBadgeColor()">{{ ucfirst(str_replace('_',' ',$booking->status)) }}</x-badge>
            </div>
            <p class="text-sm text-gray-600">{{ $booking->service->name }}</p>
            <p class="text-sm text-gray-500">{{ $booking->booking_date->format('d M Y') }} at {{ $booking->start_time }}</p>
            <div class="flex justify-between items-center mt-2">
                <span class="text-xs text-gray-400">{{ $booking->booking_number }}</span>
                <x-badge :color="$booking->paymentBadgeColor()">{{ ucfirst(str_replace('_',' ',$booking->payment_status)) }}</x-badge>
            </div>
        </a>
        @empty
        <x-empty-state message="No bookings found." action="New Booking" :actionUrl="route('admin.bookings.create')" />
        @endforelse
    </div>

    <div class="p-4">{{ $bookings->links() }}</div>
</div>
@endsection
