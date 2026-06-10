@extends('layouts.app')
@section('title', 'Edit Staff')
@section('page-title', 'Edit Staff Member')

@section('content')
<div class="max-w-2xl">
    <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6">
        @if($errors->any())
        <div class="mb-4 bg-red-50 border border-red-200 text-red-700 px-4 py-3 rounded-lg text-sm">
            <ul class="list-disc list-inside">@foreach($errors->all() as $e)<li>{{ $e }}</li>@endforeach</ul>
        </div>
        @endif

        <form method="POST" action="{{ route('admin.staff.update', $staff) }}" class="space-y-4">
            @csrf @method('PUT')
            <div class="grid sm:grid-cols-2 gap-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Full Name *</label>
                    <input type="text" name="name" value="{{ old('name', $staff->name) }}" required class="w-full px-4 py-3 border border-gray-300 rounded-lg">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Email *</label>
                    <input type="email" name="email" value="{{ old('email', $staff->email) }}" required class="w-full px-4 py-3 border border-gray-300 rounded-lg">
                </div>
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Password (leave blank to keep)</label>
                <input type="password" name="password" class="w-full px-4 py-3 border border-gray-300 rounded-lg">
            </div>
            <div class="grid sm:grid-cols-2 gap-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Role *</label>
                    <select name="role" class="w-full px-4 py-3 border border-gray-300 rounded-lg">
                        <option value="staff" {{ $staff->role === 'staff' ? 'selected' : '' }}>Staff</option>
                        <option value="doctor" {{ $staff->role === 'doctor' ? 'selected' : '' }}>Doctor</option>
                    </select>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Status *</label>
                    <select name="status" class="w-full px-4 py-3 border border-gray-300 rounded-lg">
                        <option value="active" {{ $staff->status === 'active' ? 'selected' : '' }}>Active</option>
                        <option value="inactive" {{ $staff->status === 'inactive' ? 'selected' : '' }}>Inactive</option>
                    </select>
                </div>
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">WhatsApp</label>
                <div class="flex space-x-2">
                    <select name="country_code" class="w-28 px-3 py-3 border border-gray-300 rounded-lg text-sm">
                        @foreach(['+60','+65','+62','+1','+44'] as $cc)
                        <option value="{{ $cc }}" {{ old('country_code', $staff->country_code) == $cc ? 'selected' : '' }}>{{ $cc }}</option>
                        @endforeach
                    </select>
                    <input type="text" name="whatsapp_number" value="{{ old('whatsapp_number', $staff->whatsapp_number) }}" class="flex-1 px-4 py-3 border border-gray-300 rounded-lg">
                </div>
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Position</label>
                <input type="text" name="position" value="{{ old('position', $staff->staffProfile?->position) }}" class="w-full px-4 py-3 border border-gray-300 rounded-lg">
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Bio</label>
                <textarea name="bio" rows="3" class="w-full px-4 py-3 border border-gray-300 rounded-lg">{{ old('bio', $staff->staffProfile?->bio) }}</textarea>
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Assign Services</label>
                <div class="space-y-2 max-h-40 overflow-y-auto border border-gray-200 rounded-lg p-3">
                    @foreach($services as $service)
                    <label class="flex items-center">
                        <input type="checkbox" name="service_ids[]" value="{{ $service->id }}" {{ $staff->services->contains($service->id) ? 'checked' : '' }} class="rounded border-gray-300 text-blue-600">
                        <span class="ml-2 text-sm text-gray-700">{{ $service->name }}</span>
                    </label>
                    @endforeach
                </div>
            </div>
            <div class="flex space-x-3">
                <button type="submit" class="bg-blue-600 text-white px-6 py-3 rounded-lg font-medium hover:bg-blue-700">Update</button>
                <a href="{{ route('admin.staff.index') }}" class="px-6 py-3 border border-gray-300 rounded-lg text-gray-700 hover:bg-gray-50">Cancel</a>
            </div>
        </form>
    </div>
</div>
@endsection
