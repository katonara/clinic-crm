@extends('layouts.customer')
@section('title', 'Book Appointment')

@section('content')
<h1 class="text-2xl font-bold text-gray-800 mb-6">Book an Appointment</h1>

<div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6">
    @if($errors->any())
    <div class="mb-4 bg-red-50 border border-red-200 text-red-700 px-4 py-3 rounded-lg text-sm">
        <ul class="list-disc list-inside">@foreach($errors->all() as $e)<li>{{ $e }}</li>@endforeach</ul>
    </div>
    @endif

    <form method="POST" action="{{ route('customer.bookings.store') }}" class="space-y-4">
        @csrf
        <div>
            <label class="block text-sm font-medium text-gray-700 mb-1">Service *</label>
            <select name="service_id" required class="w-full px-4 py-3 border border-gray-300 rounded-lg" x-data x-on:change="$dispatch('service-changed', $el.value)">
                <option value="">Select a service</option>
                @foreach($services as $service)
                <option value="{{ $service->id }}" {{ old('service_id') == $service->id ? 'selected' : '' }}>
                    {{ $service->name }} ({{ $service->duration_minutes }} min - RM {{ number_format($service->price, 2) }})
                </option>
                @endforeach
            </select>
        </div>

        <div>
            <label class="block text-sm font-medium text-gray-700 mb-1">Preferred Staff</label>
            <select name="staff_id" class="w-full px-4 py-3 border border-gray-300 rounded-lg">
                <option value="">Any available</option>
                @foreach($staffMembers as $member)
                <option value="{{ $member->id }}" {{ old('staff_id') == $member->id ? 'selected' : '' }}>{{ $member->name }}</option>
                @endforeach
            </select>
        </div>

        <div>
            <label class="block text-sm font-medium text-gray-700 mb-1">Preferred Date *</label>
            <input type="date" name="booking_date" value="{{ old('booking_date') }}" required min="{{ date('Y-m-d') }}" class="w-full px-4 py-3 border border-gray-300 rounded-lg">
        </div>

        <div>
            <label class="block text-sm font-medium text-gray-700 mb-1">Preferred Time *</label>
            <input type="time" name="start_time" value="{{ old('start_time') }}" required class="w-full px-4 py-3 border border-gray-300 rounded-lg">
        </div>

        <div>
            <label class="block text-sm font-medium text-gray-700 mb-1">Notes</label>
            <textarea name="notes" rows="3" class="w-full px-4 py-3 border border-gray-300 rounded-lg" placeholder="Any special requests or concerns...">{{ old('notes') }}</textarea>
        </div>

        <div class="bg-blue-50 border border-blue-200 rounded-lg px-4 py-3 text-sm text-blue-700">
            <svg class="w-4 h-4 inline mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
            Your booking will be reviewed by our clinic staff before confirmation. You'll receive a notification once approved.
        </div>

        <button type="submit" class="w-full bg-blue-600 text-white py-3 rounded-lg font-medium hover:bg-blue-700 transition-colors">Submit Booking for Review</button>
    </form>
</div>
@endsection
