@extends('layouts.app')
@section('title', 'Create Booking')
@section('page-title', 'New Booking')

@section('content')
<div class="max-w-2xl">
    <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6">
        @if($errors->any())
        <div class="mb-4 bg-red-50 border border-red-200 text-red-700 px-4 py-3 rounded-lg text-sm">
            <ul class="list-disc list-inside">@foreach($errors->all() as $e)<li>{{ $e }}</li>@endforeach</ul>
        </div>
        @endif

        <form method="POST" action="{{ route('admin.bookings.store') }}" class="space-y-4">
            @csrf
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Patient *</label>
                <select name="patient_id" required class="w-full px-4 py-3 border border-gray-300 rounded-lg">
                    <option value="">Select patient</option>
                    @foreach($patients as $p)
                    <option value="{{ $p->id }}" {{ old('patient_id') == $p->id ? 'selected' : '' }}>{{ $p->name }} ({{ $p->whatsapp_number }})</option>
                    @endforeach
                </select>
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Service *</label>
                <select name="service_id" required class="w-full px-4 py-3 border border-gray-300 rounded-lg">
                    <option value="">Select service</option>
                    @foreach($services as $service)
                    <option value="{{ $service->id }}" {{ old('service_id') == $service->id ? 'selected' : '' }}>{{ $service->name }} - RM{{ number_format($service->price, 2) }} ({{ $service->duration_minutes }}min)</option>
                    @endforeach
                </select>
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Staff/Doctor</label>
                <select name="staff_id" class="w-full px-4 py-3 border border-gray-300 rounded-lg">
                    <option value="">Unassigned</option>
                    @foreach($staffMembers as $staff)
                    <option value="{{ $staff->id }}" {{ old('staff_id') == $staff->id ? 'selected' : '' }}>{{ $staff->name }}</option>
                    @endforeach
                </select>
            </div>
            <div class="grid sm:grid-cols-2 gap-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Date *</label>
                    <input type="date" name="booking_date" value="{{ old('booking_date') }}" required min="{{ date('Y-m-d') }}" class="w-full px-4 py-3 border border-gray-300 rounded-lg">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Start Time *</label>
                    <input type="time" name="start_time" value="{{ old('start_time') }}" required class="w-full px-4 py-3 border border-gray-300 rounded-lg">
                </div>
            </div>
            <div class="grid sm:grid-cols-2 gap-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Payment Method</label>
                    <select name="payment_method" class="w-full px-4 py-3 border border-gray-300 rounded-lg">
                        <option value="">-</option>
                        <option value="cash">Cash</option><option value="card">Card</option><option value="online_transfer">Online Transfer</option><option value="ewallet">E-Wallet</option>
                    </select>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Deposit Amount</label>
                    <input type="number" step="0.01" name="deposit_amount" value="{{ old('deposit_amount') }}" class="w-full px-4 py-3 border border-gray-300 rounded-lg">
                </div>
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Customer Note</label>
                <textarea name="customer_note" rows="2" class="w-full px-4 py-3 border border-gray-300 rounded-lg">{{ old('customer_note') }}</textarea>
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Internal Note</label>
                <textarea name="internal_note" rows="2" class="w-full px-4 py-3 border border-gray-300 rounded-lg">{{ old('internal_note') }}</textarea>
            </div>
            <div class="flex space-x-3">
                <button type="submit" class="bg-blue-600 text-white px-6 py-3 rounded-lg font-medium hover:bg-blue-700">Create Booking</button>
                <a href="{{ route('admin.bookings.index') }}" class="px-6 py-3 border border-gray-300 rounded-lg text-gray-700 hover:bg-gray-50">Cancel</a>
            </div>
        </form>
    </div>
</div>
@endsection
