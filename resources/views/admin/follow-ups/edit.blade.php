@extends('layouts.app')
@section('title', 'Edit Follow-up')
@section('page-title', 'Edit Follow-up')

@section('content')
<div class="max-w-2xl">
    <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6">
        @if($errors->any())
        <div class="mb-4 bg-red-50 border border-red-200 text-red-700 px-4 py-3 rounded-lg text-sm">
            <ul class="list-disc list-inside">@foreach($errors->all() as $e)<li>{{ $e }}</li>@endforeach</ul>
        </div>
        @endif

        <form method="POST" action="{{ route('admin.follow-ups.update', $followUp) }}" class="space-y-4">
            @csrf @method('PUT')
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Patient *</label>
                <select name="patient_id" required class="w-full px-4 py-3 border border-gray-300 rounded-lg">
                    @foreach($patients as $p)
                    <option value="{{ $p->id }}" {{ old('patient_id', $followUp->patient_id) == $p->id ? 'selected' : '' }}>{{ $p->name }}</option>
                    @endforeach
                </select>
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Related Booking</label>
                <select name="booking_id" class="w-full px-4 py-3 border border-gray-300 rounded-lg">
                    <option value="">None</option>
                    @foreach($bookings as $b)
                    <option value="{{ $b->id }}" {{ old('booking_id', $followUp->booking_id) == $b->id ? 'selected' : '' }}>{{ $b->booking_number }}</option>
                    @endforeach
                </select>
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Assign To</label>
                <select name="assigned_to" class="w-full px-4 py-3 border border-gray-300 rounded-lg">
                    <option value="">Unassigned</option>
                    @foreach($staffMembers as $s)
                    <option value="{{ $s->id }}" {{ old('assigned_to', $followUp->assigned_to) == $s->id ? 'selected' : '' }}>{{ $s->name }}</option>
                    @endforeach
                </select>
            </div>
            <div class="grid sm:grid-cols-2 gap-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Follow-up Date *</label>
                    <input type="date" name="follow_up_date" value="{{ old('follow_up_date', $followUp->follow_up_date->format('Y-m-d')) }}" required class="w-full px-4 py-3 border border-gray-300 rounded-lg">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Status *</label>
                    <select name="status" required class="w-full px-4 py-3 border border-gray-300 rounded-lg">
                        @foreach(['not_contacted','contacted','interested','not_interested','booked','closed'] as $s)
                        <option value="{{ $s }}" {{ old('status', $followUp->status) === $s ? 'selected' : '' }}>{{ ucfirst(str_replace('_',' ',$s)) }}</option>
                        @endforeach
                    </select>
                </div>
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Notes</label>
                <textarea name="notes" rows="3" class="w-full px-4 py-3 border border-gray-300 rounded-lg">{{ old('notes', $followUp->notes) }}</textarea>
            </div>
            <div class="flex space-x-3">
                <button type="submit" class="bg-blue-600 text-white px-6 py-3 rounded-lg font-medium hover:bg-blue-700">Update</button>
                <a href="{{ route('admin.follow-ups.index') }}" class="px-6 py-3 border border-gray-300 rounded-lg text-gray-700 hover:bg-gray-50">Cancel</a>
            </div>
        </form>
    </div>
</div>
@endsection
