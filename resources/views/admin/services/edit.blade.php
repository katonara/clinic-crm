@extends('layouts.app')
@section('title', 'Edit Service')
@section('page-title', 'Edit Service')

@section('content')
<div class="max-w-2xl">
    <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6">
        @if($errors->any())
        <div class="mb-4 bg-red-50 border border-red-200 text-red-700 px-4 py-3 rounded-lg text-sm">
            <ul class="list-disc list-inside">@foreach($errors->all() as $e)<li>{{ $e }}</li>@endforeach</ul>
        </div>
        @endif

        <form method="POST" action="{{ route('admin.services.update', $service) }}" class="space-y-4">
            @csrf @method('PUT')
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Service Name *</label>
                <input type="text" name="name" value="{{ old('name', $service->name) }}" required class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent">
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Description</label>
                <textarea name="description" rows="3" class="w-full px-4 py-3 border border-gray-300 rounded-lg">{{ old('description', $service->description) }}</textarea>
            </div>
            <div class="grid sm:grid-cols-2 gap-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Price (RM) *</label>
                    <input type="number" step="0.01" name="price" value="{{ old('price', $service->price) }}" required class="w-full px-4 py-3 border border-gray-300 rounded-lg">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Duration (minutes) *</label>
                    <input type="number" name="duration_minutes" value="{{ old('duration_minutes', $service->duration_minutes) }}" required class="w-full px-4 py-3 border border-gray-300 rounded-lg">
                </div>
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Status</label>
                <select name="status" class="w-full px-4 py-3 border border-gray-300 rounded-lg">
                    <option value="active" {{ $service->status === 'active' ? 'selected' : '' }}>Active</option>
                    <option value="inactive" {{ $service->status === 'inactive' ? 'selected' : '' }}>Inactive</option>
                </select>
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Assign Staff/Doctor</label>
                <div class="space-y-2 max-h-40 overflow-y-auto border border-gray-200 rounded-lg p-3">
                    @foreach($staffMembers as $staff)
                    <label class="flex items-center">
                        <input type="checkbox" name="staff_ids[]" value="{{ $staff->id }}" {{ $service->staff->contains($staff->id) ? 'checked' : '' }} class="rounded border-gray-300 text-blue-600">
                        <span class="ml-2 text-sm text-gray-700">{{ $staff->name }} ({{ ucfirst($staff->role) }})</span>
                    </label>
                    @endforeach
                </div>
            </div>
            <div class="flex space-x-3">
                <button type="submit" class="bg-blue-600 text-white px-6 py-3 rounded-lg font-medium hover:bg-blue-700">Update</button>
                <a href="{{ route('admin.services.index') }}" class="px-6 py-3 border border-gray-300 rounded-lg text-gray-700 hover:bg-gray-50">Cancel</a>
            </div>
        </form>
    </div>
</div>
@endsection
