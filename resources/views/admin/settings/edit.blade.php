@extends('layouts.app')
@section('title', 'Settings')
@section('page-title', 'Clinic Settings')

@section('content')
<div class="max-w-2xl">
    <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6">
        @if($errors->any())
        <div class="mb-4 bg-red-50 border border-red-200 text-red-700 px-4 py-3 rounded-lg text-sm">
            <ul class="list-disc list-inside">@foreach($errors->all() as $e)<li>{{ $e }}</li>@endforeach</ul>
        </div>
        @endif

        <form method="POST" action="{{ route('admin.settings.update') }}" enctype="multipart/form-data" class="space-y-4">
            @csrf @method('PUT')
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Clinic Name *</label>
                <input type="text" name="clinic_name" value="{{ old('clinic_name', $settings->clinic_name) }}" required class="w-full px-4 py-3 border border-gray-300 rounded-lg">
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Logo</label>
                @if($settings->logo)
                <img src="{{ asset('storage/' . $settings->logo) }}" alt="Logo" class="h-16 mb-2 rounded">
                @endif
                <input type="file" name="logo" accept="image/*" class="w-full px-4 py-2 border border-gray-300 rounded-lg text-sm">
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Email</label>
                <input type="email" name="email" value="{{ old('email', $settings->email) }}" class="w-full px-4 py-3 border border-gray-300 rounded-lg">
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">WhatsApp</label>
                <div class="flex space-x-2">
                    <select name="whatsapp_country_code" class="w-28 px-3 py-3 border border-gray-300 rounded-lg text-sm">
                        @foreach(['+60','+65','+62','+1','+44'] as $cc)
                        <option value="{{ $cc }}" {{ old('whatsapp_country_code', $settings->whatsapp_country_code) == $cc ? 'selected' : '' }}>{{ $cc }}</option>
                        @endforeach
                    </select>
                    <input type="text" name="whatsapp_number" value="{{ old('whatsapp_number', $settings->whatsapp_number) }}" class="flex-1 px-4 py-3 border border-gray-300 rounded-lg">
                </div>
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Address</label>
                <textarea name="address" rows="3" class="w-full px-4 py-3 border border-gray-300 rounded-lg">{{ old('address', $settings->address) }}</textarea>
            </div>
            <div class="grid sm:grid-cols-3 gap-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Slot Duration (min)</label>
                    <input type="number" name="default_slot_duration" value="{{ old('default_slot_duration', $settings->default_slot_duration) }}" required class="w-full px-4 py-3 border border-gray-300 rounded-lg">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Opening Time</label>
                    <input type="time" name="opening_time" value="{{ old('opening_time', $settings->opening_time) }}" required class="w-full px-4 py-3 border border-gray-300 rounded-lg">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Closing Time</label>
                    <input type="time" name="closing_time" value="{{ old('closing_time', $settings->closing_time) }}" required class="w-full px-4 py-3 border border-gray-300 rounded-lg">
                </div>
            </div>
            <button type="submit" class="bg-blue-600 text-white px-6 py-3 rounded-lg font-medium hover:bg-blue-700">Save Settings</button>
        </form>
    </div>
</div>
@endsection
