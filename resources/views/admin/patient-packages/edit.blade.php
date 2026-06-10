@extends('layouts.app')
@section('title', 'Edit Package')
@section('page-title', 'Edit Package')

@section('content')
<div class="max-w-2xl">
    <form method="POST" action="{{ route('admin.patient-packages.update', $patientPackage) }}" class="bg-white rounded-xl shadow-sm border border-gray-100 p-6 space-y-5">
        @csrf @method('PUT')

        <div>
            <label class="block text-sm font-medium text-gray-700 mb-1">Patient *</label>
            <select name="patient_id" required class="w-full border border-gray-300 rounded-lg px-3 py-2.5 text-sm focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                @foreach($patients as $patient)
                <option value="{{ $patient->id }}" {{ old('patient_id', $patientPackage->patient_id) == $patient->id ? 'selected' : '' }}>{{ $patient->name }}</option>
                @endforeach
            </select>
        </div>

        <div class="grid sm:grid-cols-2 gap-4">
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Package Name *</label>
                <input type="text" name="package_name" value="{{ old('package_name', $patientPackage->package_name) }}" required
                       class="w-full border border-gray-300 rounded-lg px-3 py-2.5 text-sm focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Service</label>
                <select name="service_id" class="w-full border border-gray-300 rounded-lg px-3 py-2.5 text-sm focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                    <option value="">None</option>
                    @foreach($services as $service)
                    <option value="{{ $service->id }}" {{ old('service_id', $patientPackage->service_id) == $service->id ? 'selected' : '' }}>{{ $service->name }}</option>
                    @endforeach
                </select>
            </div>
        </div>

        <div class="grid sm:grid-cols-3 gap-4">
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Total Sessions *</label>
                <input type="number" name="total_sessions" value="{{ old('total_sessions', $patientPackage->total_sessions) }}" required min="1"
                       class="w-full border border-gray-300 rounded-lg px-3 py-2.5 text-sm focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Start Date *</label>
                <input type="date" name="start_date" value="{{ old('start_date', $patientPackage->start_date->format('Y-m-d')) }}" required
                       class="w-full border border-gray-300 rounded-lg px-3 py-2.5 text-sm focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">End Date</label>
                <input type="date" name="end_date" value="{{ old('end_date', $patientPackage->end_date?->format('Y-m-d')) }}"
                       class="w-full border border-gray-300 rounded-lg px-3 py-2.5 text-sm focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
            </div>
        </div>

        {{-- Usage info (read-only) --}}
        <div class="bg-gray-50 rounded-lg p-4">
            <p class="text-sm text-gray-600">Used: <strong>{{ $patientPackage->used_sessions }}</strong> / {{ $patientPackage->total_sessions }} sessions</p>
            <p class="text-sm text-gray-600">Remaining: <strong class="{{ $patientPackage->remaining_sessions <= 1 ? 'text-red-600' : '' }}">{{ $patientPackage->remaining_sessions }}</strong></p>
        </div>

        <div>
            <label class="block text-sm font-medium text-gray-700 mb-1">Status *</label>
            <select name="status" class="w-full border border-gray-300 rounded-lg px-3 py-2.5 text-sm focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                @foreach(['active', 'completed', 'expired', 'cancelled'] as $s)
                <option value="{{ $s }}" {{ old('status', $patientPackage->status) === $s ? 'selected' : '' }}>{{ ucfirst($s) }}</option>
                @endforeach
            </select>
        </div>

        <div>
            <label class="block text-sm font-medium text-gray-700 mb-1">Notes</label>
            <textarea name="notes" rows="3" class="w-full border border-gray-300 rounded-lg px-3 py-2.5 text-sm focus:ring-2 focus:ring-blue-500 focus:border-blue-500">{{ old('notes', $patientPackage->notes) }}</textarea>
        </div>

        <div class="flex justify-end space-x-3 pt-4 border-t border-gray-100">
            <a href="{{ route('admin.patient-packages.index') }}" class="px-4 py-2.5 border border-gray-300 rounded-lg text-sm text-gray-700 hover:bg-gray-50">Cancel</a>
            <button type="submit" class="px-6 py-2.5 bg-blue-600 text-white rounded-lg text-sm font-medium hover:bg-blue-700">Update Package</button>
        </div>
    </form>
</div>
@endsection
