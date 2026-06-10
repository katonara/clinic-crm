@extends('layouts.app')
@section('title', 'Add Staff')
@section('page-title', 'Add Staff Member')

@section('content')
<div class="max-w-2xl">
    <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6">
        @if($errors->any())
        <div class="mb-4 bg-red-50 border border-red-200 text-red-700 px-4 py-3 rounded-lg text-sm">
            <ul class="list-disc list-inside">@foreach($errors->all() as $e)<li>{{ $e }}</li>@endforeach</ul>
        </div>
        @endif

        <form method="POST" action="{{ route('admin.staff.store') }}" class="space-y-4">
            @csrf
            <div class="grid sm:grid-cols-2 gap-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Full Name *</label>
                    <input type="text" name="name" value="{{ old('name') }}" required class="w-full px-4 py-3 border border-gray-300 rounded-lg">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Email *</label>
                    <input type="email" name="email" value="{{ old('email') }}" required class="w-full px-4 py-3 border border-gray-300 rounded-lg">
                </div>
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Password *</label>
                <input type="password" name="password" required class="w-full px-4 py-3 border border-gray-300 rounded-lg">
            </div>
            <div class="grid sm:grid-cols-2 gap-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Role *</label>
                    <select name="role" class="w-full px-4 py-3 border border-gray-300 rounded-lg">
                        <option value="staff">Staff</option><option value="doctor">Doctor</option>
                    </select>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Status *</label>
                    <select name="status" class="w-full px-4 py-3 border border-gray-300 rounded-lg">
                        <option value="active">Active</option><option value="inactive">Inactive</option>
                    </select>
                </div>
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">WhatsApp</label>
                <div class="flex space-x-2">
                    <select name="country_code" class="w-28 px-3 py-3 border border-gray-300 rounded-lg text-sm">
                        <option value="+60" selected>+60</option><option value="+65">+65</option><option value="+62">+62</option><option value="+1">+1</option><option value="+44">+44</option>
                    </select>
                    <input type="text" name="whatsapp_number" value="{{ old('whatsapp_number') }}" class="flex-1 px-4 py-3 border border-gray-300 rounded-lg">
                </div>
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Position</label>
                <input type="text" name="position" value="{{ old('position') }}" class="w-full px-4 py-3 border border-gray-300 rounded-lg">
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Bio</label>
                <textarea name="bio" rows="3" class="w-full px-4 py-3 border border-gray-300 rounded-lg">{{ old('bio') }}</textarea>
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Assign Services</label>
                <div class="space-y-2 max-h-40 overflow-y-auto border border-gray-200 rounded-lg p-3">
                    @foreach($services as $service)
                    <label class="flex items-center">
                        <input type="checkbox" name="service_ids[]" value="{{ $service->id }}" class="rounded border-gray-300 text-blue-600">
                        <span class="ml-2 text-sm text-gray-700">{{ $service->name }}</span>
                    </label>
                    @endforeach
                    @if($services->isEmpty())<p class="text-sm text-gray-400">No services available.</p>@endif
                </div>
            </div>
            <div class="flex space-x-3">
                <button type="submit" class="bg-blue-600 text-white px-6 py-3 rounded-lg font-medium hover:bg-blue-700">Save</button>
                <a href="{{ route('admin.staff.index') }}" class="px-6 py-3 border border-gray-300 rounded-lg text-gray-700 hover:bg-gray-50">Cancel</a>
            </div>
        </form>
    </div>
</div>
@endsection
