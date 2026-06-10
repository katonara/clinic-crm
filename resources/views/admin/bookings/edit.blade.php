@extends('layouts.app')
@section('title', 'Edit Booking')
@section('page-title', 'Edit Booking')

@section('content')
<div class="max-w-2xl">
    <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6">
        @if($errors->any())
        <div class="mb-4 bg-red-50 border border-red-200 text-red-700 px-4 py-3 rounded-lg text-sm">
            <ul class="list-disc list-inside">@foreach($errors->all() as $e)<li>{{ $e }}</li>@endforeach</ul>
        </div>
        @endif

        <form method="POST" action="{{ route('admin.bookings.update', $booking) }}" class="space-y-4">
            @csrf @method('PUT')
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Patient *</label>
                <select name="patient_id" required class="w-full px-4 py-3 border border-gray-300 rounded-lg">
                    @foreach($patients as $p)
                    <option value="{{ $p->id }}" {{ old('patient_id', $booking->patient_id) == $p->id ? 'selected' : '' }}>{{ $p->name }}</option>
                    @endforeach
                </select>
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Service *</label>
                <select name="service_id" required class="w-full px-4 py-3 border border-gray-300 rounded-lg">
                    @foreach($services as $service)
                    <option value="{{ $service->id }}" {{ old('service_id', $booking->service_id) == $service->id ? 'selected' : '' }}>{{ $service->name }} - RM{{ number_format($service->price, 2) }}</option>
                    @endforeach
                </select>
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Staff/Doctor</label>
                <select name="staff_id" class="w-full px-4 py-3 border border-gray-300 rounded-lg">
                    <option value="">Unassigned</option>
                    @foreach($staffMembers as $staff)
                    <option value="{{ $staff->id }}" {{ old('staff_id', $booking->staff_id) == $staff->id ? 'selected' : '' }}>{{ $staff->name }}</option>
                    @endforeach
                </select>
            </div>
            <div class="grid sm:grid-cols-2 gap-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Date *</label>
                    <input type="date" name="booking_date" value="{{ old('booking_date', $booking->booking_date->format('Y-m-d')) }}" required class="w-full px-4 py-3 border border-gray-300 rounded-lg">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Start Time *</label>
                    <input type="time" name="start_time" value="{{ old('start_time', $booking->start_time) }}" required class="w-full px-4 py-3 border border-gray-300 rounded-lg">
                </div>
            </div>
            <div class="grid sm:grid-cols-2 gap-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Payment Method</label>
                    <select name="payment_method" class="w-full px-4 py-3 border border-gray-300 rounded-lg">
                        <option value="">-</option>
                        @foreach(['cash','card','online_transfer','ewallet'] as $pm)
                        <option value="{{ $pm }}" {{ old('payment_method', $booking->payment_method) === $pm ? 'selected' : '' }}>{{ ucfirst(str_replace('_',' ',$pm)) }}</option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Deposit Amount</label>
                    <input type="number" step="0.01" name="deposit_amount" value="{{ old('deposit_amount', $booking->deposit_amount) }}" class="w-full px-4 py-3 border border-gray-300 rounded-lg">
                </div>
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Customer Note</label>
                <textarea name="customer_note" rows="2" class="w-full px-4 py-3 border border-gray-300 rounded-lg">{{ old('customer_note', $booking->customer_note) }}</textarea>
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Internal Note</label>
                <textarea name="internal_note" rows="2" class="w-full px-4 py-3 border border-gray-300 rounded-lg">{{ old('internal_note', $booking->internal_note) }}</textarea>
            </div>
            <div class="flex space-x-3">
                <button type="submit" class="bg-blue-600 text-white px-6 py-3 rounded-lg font-medium hover:bg-blue-700">Update Booking</button>
                <a href="{{ route('admin.bookings.show', $booking) }}" class="px-6 py-3 border border-gray-300 rounded-lg text-gray-700 hover:bg-gray-50">Cancel</a>
            </div>
        </form>
    </div>
</div>
@endsection
