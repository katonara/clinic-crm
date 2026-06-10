@extends('layouts.app')
@section('title', 'Booking Detail')
@section('page-title', $booking->booking_number)
@section('header-actions')
<a href="{{ route('admin.bookings.edit', $booking) }}" class="bg-blue-600 text-white px-4 py-2 rounded-lg text-sm hover:bg-blue-700">Edit</a>
@endsection

@section('content')
<div class="grid lg:grid-cols-3 gap-6">
    {{-- Booking Info --}}
    <div class="lg:col-span-2 space-y-6">
        <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6">
            <div class="flex flex-wrap gap-2 mb-4">
                <x-badge :color="$booking->statusBadgeColor()">{{ ucfirst(str_replace('_',' ',$booking->status)) }}</x-badge>
                <x-badge :color="$booking->paymentBadgeColor()">{{ ucfirst(str_replace('_',' ',$booking->payment_status)) }}</x-badge>
            </div>

            <dl class="grid sm:grid-cols-2 gap-4 text-sm">
                <div><dt class="text-gray-500">Patient</dt><dd class="font-medium text-gray-800"><a href="{{ route('admin.patients.show', $booking->patient) }}" class="text-blue-600 hover:underline">{{ $booking->patient->name }}</a></dd></div>
                <div><dt class="text-gray-500">Service</dt><dd class="font-medium text-gray-800">{{ $booking->service->name }}</dd></div>
                <div><dt class="text-gray-500">Date</dt><dd class="text-gray-800">{{ $booking->booking_date->format('d M Y') }}</dd></div>
                <div><dt class="text-gray-500">Time</dt><dd class="text-gray-800">{{ $booking->start_time }} - {{ $booking->end_time }}</dd></div>
                <div><dt class="text-gray-500">Staff/Doctor</dt><dd class="text-gray-800">{{ $booking->staffMember?->name ?? 'Unassigned' }}</dd></div>
                <div><dt class="text-gray-500">Total Amount</dt><dd class="font-bold text-gray-800">RM {{ number_format($booking->total_amount, 2) }}</dd></div>
                <div><dt class="text-gray-500">Deposit</dt><dd class="text-gray-800">{{ $booking->deposit_amount ? 'RM ' . number_format($booking->deposit_amount, 2) : '-' }}</dd></div>
                <div><dt class="text-gray-500">Payment Method</dt><dd class="text-gray-800 capitalize">{{ $booking->payment_method ? str_replace('_',' ',$booking->payment_method) : '-' }}</dd></div>
            </dl>

            @if($booking->customer_note)
            <div class="mt-4 p-3 bg-blue-50 rounded-lg"><p class="text-sm text-gray-700"><strong>Customer Note:</strong> {{ $booking->customer_note }}</p></div>
            @endif
            @if($booking->internal_note)
            <div class="mt-2 p-3 bg-yellow-50 rounded-lg"><p class="text-sm text-gray-700"><strong>Internal Note:</strong> {{ $booking->internal_note }}</p></div>
            @endif
            @if($booking->cancellation_reason)
            <div class="mt-2 p-3 bg-red-50 rounded-lg"><p class="text-sm text-gray-700"><strong>Cancellation:</strong> {{ $booking->cancellation_reason }}</p></div>
            @endif
            @if($booking->reschedule_reason)
            <div class="mt-2 p-3 bg-purple-50 rounded-lg"><p class="text-sm text-gray-700"><strong>Reschedule:</strong> {{ $booking->reschedule_reason }}</p></div>
            @endif
        </div>

        {{-- WhatsApp Quick Action --}}
        @if($booking->patient->whatsapp_number)
        <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6">
            <h3 class="font-semibold text-gray-800 mb-3">WhatsApp</h3>
            @php
                $waNumber = ltrim($booking->patient->country_code, '+') . $booking->patient->whatsapp_number;
                $waMessage = urlencode("Hi {$booking->patient->name}, this is regarding your booking {$booking->booking_number} on {$booking->booking_date->format('d M Y')} at {$booking->start_time}.");
            @endphp
            <a href="https://wa.me/{{ $waNumber }}?text={{ $waMessage }}" target="_blank"
               class="inline-flex items-center px-4 py-2 bg-green-500 text-white rounded-lg text-sm hover:bg-green-600">
                <svg class="w-5 h-5 mr-2" fill="currentColor" viewBox="0 0 24 24"><path d="M17.472 14.382c-.297-.149-1.758-.867-2.03-.967-.273-.099-.471-.148-.67.15-.197.297-.767.966-.94 1.164-.173.199-.347.223-.644.075-.297-.15-1.255-.463-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.298-.347.446-.52.149-.174.198-.298.298-.497.099-.198.05-.371-.025-.52-.075-.149-.669-1.612-.916-2.207-.242-.579-.487-.5-.669-.51-.173-.008-.371-.01-.57-.01-.198 0-.52.074-.792.372-.272.297-1.04 1.016-1.04 2.479 0 1.462 1.065 2.875 1.213 3.074.149.198 2.096 3.2 5.077 4.487.709.306 1.262.489 1.694.625.712.227 1.36.195 1.871.118.571-.085 1.758-.719 2.006-1.413.248-.694.248-1.289.173-1.413-.074-.124-.272-.198-.57-.347z"/><path d="M12 0C5.373 0 0 5.373 0 12c0 2.625.846 5.059 2.284 7.034L.789 23.492a.5.5 0 00.612.638l4.702-1.229A11.95 11.95 0 0012 24c6.627 0 12-5.373 12-12S18.627 0 12 0zm0 22c-2.166 0-4.17-.639-5.857-1.738l-.42-.263-3.073.803.83-3.024-.28-.441A9.96 9.96 0 012 12C2 6.477 6.477 2 12 2s10 4.477 10 10-4.477 10-10 10z"/></svg>
                Send WhatsApp
            </a>
        </div>
        @endif
    </div>

    {{-- Actions sidebar --}}
    <div class="space-y-6">
        {{-- Update Status --}}
        <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6" x-data="{ showCancel: false, showReschedule: false }">
            <h3 class="font-semibold text-gray-800 mb-3">Update Status</h3>
            <div class="grid grid-cols-2 gap-2">
                @foreach(['pending','confirmed','completed','no_show'] as $s)
                <form method="POST" action="{{ route('admin.bookings.update-status', $booking) }}">
                    @csrf @method('PATCH')
                    <input type="hidden" name="status" value="{{ $s }}">
                    <button type="submit" class="w-full px-3 py-2 text-sm rounded-lg border {{ $booking->status === $s ? 'bg-blue-100 border-blue-300 text-blue-700' : 'border-gray-200 text-gray-600 hover:bg-gray-50' }}">
                        {{ ucfirst(str_replace('_',' ',$s)) }}
                    </button>
                </form>
                @endforeach
            </div>

            <button @click="showCancel = !showCancel" class="w-full mt-2 px-3 py-2 text-sm rounded-lg border border-red-200 text-red-600 hover:bg-red-50">Cancel Booking</button>
            <div x-show="showCancel" x-transition class="mt-2">
                <form method="POST" action="{{ route('admin.bookings.update-status', $booking) }}">
                    @csrf @method('PATCH')
                    <input type="hidden" name="status" value="cancelled">
                    <textarea name="cancellation_reason" placeholder="Reason..." rows="2" class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm mb-2"></textarea>
                    <button type="submit" class="w-full bg-red-600 text-white px-3 py-2 rounded-lg text-sm">Confirm Cancel</button>
                </form>
            </div>

            <button @click="showReschedule = !showReschedule" class="w-full mt-2 px-3 py-2 text-sm rounded-lg border border-purple-200 text-purple-600 hover:bg-purple-50">Reschedule</button>
            <div x-show="showReschedule" x-transition class="mt-2">
                <form method="POST" action="{{ route('admin.bookings.update-status', $booking) }}">
                    @csrf @method('PATCH')
                    <input type="hidden" name="status" value="rescheduled">
                    <textarea name="reschedule_reason" placeholder="Reason..." rows="2" class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm mb-2"></textarea>
                    <button type="submit" class="w-full bg-purple-600 text-white px-3 py-2 rounded-lg text-sm">Confirm Reschedule</button>
                </form>
            </div>
        </div>

        {{-- Update Payment --}}
        <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6">
            <h3 class="font-semibold text-gray-800 mb-3">Payment</h3>
            <form method="POST" action="{{ route('admin.bookings.update-payment', $booking) }}" class="space-y-3">
                @csrf @method('PATCH')
                <select name="payment_status" class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm">
                    @foreach(['unpaid','deposit_paid','paid','refunded'] as $ps)
                    <option value="{{ $ps }}" {{ $booking->payment_status === $ps ? 'selected' : '' }}>{{ ucfirst(str_replace('_',' ',$ps)) }}</option>
                    @endforeach
                </select>
                <select name="payment_method" class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm">
                    <option value="">-</option>
                    @foreach(['cash','card','online_transfer','ewallet'] as $pm)
                    <option value="{{ $pm }}" {{ $booking->payment_method === $pm ? 'selected' : '' }}>{{ ucfirst(str_replace('_',' ',$pm)) }}</option>
                    @endforeach
                </select>
                <button type="submit" class="w-full bg-green-600 text-white px-3 py-2 rounded-lg text-sm hover:bg-green-700">Update Payment</button>
            </form>
        </div>

        {{-- Follow-up --}}
        <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6">
            <h3 class="font-semibold text-gray-800 mb-3">Follow-ups</h3>
            @forelse($booking->followUps as $fu)
            <div class="mb-2 p-2 bg-gray-50 rounded text-sm">
                <div class="flex justify-between"><span>{{ $fu->follow_up_date->format('d M Y') }}</span><x-badge :color="$fu->statusBadgeColor()">{{ ucfirst(str_replace('_',' ',$fu->status)) }}</x-badge></div>
                @if($fu->notes)<p class="text-gray-500 text-xs mt-1">{{ $fu->notes }}</p>@endif
            </div>
            @empty
            <p class="text-sm text-gray-400">No follow-ups.</p>
            @endforelse
            <a href="{{ route('admin.follow-ups.create', ['booking_id' => $booking->id, 'patient_id' => $booking->patient_id]) }}" class="block mt-3 text-center px-3 py-2 border border-gray-200 rounded-lg text-sm text-blue-600 hover:bg-blue-50">+ Add Follow-up</a>
        </div>
    </div>
</div>
@endsection
