@extends('layouts.customer')
@section('title', 'Booking History')

@section('content')
<h1 class="text-2xl font-bold text-gray-800 mb-6">Booking History</h1>

<div class="space-y-3">
    @forelse($bookings as $booking)
    <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-4">
        <div class="flex justify-between items-start mb-2">
            <div>
                <p class="font-medium text-gray-800">{{ $booking->service->name }}</p>
                <p class="text-xs text-gray-400">{{ $booking->booking_number }}</p>
            </div>
            <x-badge :color="$booking->statusBadgeColor()">{{ ucfirst($booking->status) }}</x-badge>
        </div>
        <div class="flex justify-between items-end">
            <div>
                <p class="text-sm text-gray-500">{{ $booking->booking_date->format('D, d M Y') }}</p>
                <p class="text-sm text-gray-500">{{ $booking->start_time }} - {{ $booking->end_time }}</p>
                <p class="text-sm text-gray-400">{{ $booking->staffMember?->name ?? 'Staff TBD' }}</p>
            </div>
            <div class="text-right">
                <p class="font-semibold text-gray-800">RM {{ number_format($booking->total_amount, 2) }}</p>
                <x-badge :color="$booking->paymentBadgeColor()">{{ ucfirst(str_replace('_', ' ', $booking->payment_status)) }}</x-badge>
            </div>
        </div>

        @if(in_array($booking->status, ['pending', 'confirmed']))
        <div class="mt-3 pt-3 border-t border-gray-100 flex space-x-2">
            <form method="POST" action="{{ route('customer.bookings.cancel', $booking) }}"
                  onsubmit="return confirm('Cancel this booking?')">
                @csrf @method('PATCH')
                <button type="submit" class="px-4 py-2 text-sm border border-red-200 text-red-600 rounded-lg hover:bg-red-50">Cancel</button>
            </form>
            <form method="POST" action="{{ route('customer.bookings.reschedule', $booking) }}"
                  onsubmit="return confirm('Request reschedule?')">
                @csrf @method('PATCH')
                <input type="hidden" name="reason" value="Customer requested reschedule">
                <button type="submit" class="px-4 py-2 text-sm border border-purple-200 text-purple-600 rounded-lg hover:bg-purple-50">Reschedule</button>
            </form>
        </div>
        @endif
    </div>
    @empty
    <x-empty-state message="No bookings yet." action="Book Now" :actionUrl="route('customer.bookings.create')" />
    @endforelse
</div>

<div class="mt-4">{{ $bookings->links() }}</div>
@endsection
