@extends('layouts.app')
@section('title', 'Edit Treatment Room')
@section('page-title', 'Edit Treatment Room')

@section('content')
<div class="max-w-lg">
    <form method="POST" action="{{ route('admin.treatment-rooms.update', $treatmentRoom) }}" class="bg-white rounded-xl shadow-sm border border-gray-100 p-6 space-y-5">
        @csrf @method('PUT')

        <div>
            <label class="block text-sm font-medium text-gray-700 mb-1">Room Name *</label>
            <input type="text" name="name" value="{{ old('name', $treatmentRoom->name) }}" required
                   class="w-full border border-gray-300 rounded-lg px-3 py-2.5 text-sm focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
            @error('name')<p class="text-red-500 text-xs mt-1">{{ $message }}</p>@enderror
        </div>

        <div>
            <label class="block text-sm font-medium text-gray-700 mb-1">Room Code</label>
            <input type="text" name="room_code" value="{{ old('room_code', $treatmentRoom->room_code) }}" placeholder="e.g. R01"
                   class="w-full border border-gray-300 rounded-lg px-3 py-2.5 text-sm focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
        </div>

        <div>
            <label class="block text-sm font-medium text-gray-700 mb-1">Description</label>
            <textarea name="description" rows="3" class="w-full border border-gray-300 rounded-lg px-3 py-2.5 text-sm focus:ring-2 focus:ring-blue-500 focus:border-blue-500">{{ old('description', $treatmentRoom->description) }}</textarea>
        </div>

        <div>
            <label class="block text-sm font-medium text-gray-700 mb-1">Status *</label>
            <select name="status" class="w-full border border-gray-300 rounded-lg px-3 py-2.5 text-sm focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                <option value="active" {{ old('status', $treatmentRoom->status) === 'active' ? 'selected' : '' }}>Active</option>
                <option value="inactive" {{ old('status', $treatmentRoom->status) === 'inactive' ? 'selected' : '' }}>Inactive</option>
                <option value="closed" {{ old('status', $treatmentRoom->status) === 'closed' ? 'selected' : '' }}>Closed</option>
            </select>
        </div>

        <div class="flex justify-end space-x-3 pt-4 border-t border-gray-100">
            <a href="{{ route('admin.treatment-rooms.index') }}" class="px-4 py-2.5 border border-gray-300 rounded-lg text-sm text-gray-700 hover:bg-gray-50">Cancel</a>
            <button type="submit" class="px-6 py-2.5 bg-blue-600 text-white rounded-lg text-sm font-medium hover:bg-blue-700">Update Room</button>
        </div>
    </form>
</div>
@endsection
