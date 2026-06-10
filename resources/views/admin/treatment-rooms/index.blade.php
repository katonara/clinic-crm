@extends('layouts.app')
@section('title', 'Treatment Rooms')
@section('page-title', 'Treatment Rooms')
@section('header-actions')
<a href="{{ route('admin.treatment-rooms.board') }}" class="bg-purple-600 text-white px-4 py-2 rounded-lg text-sm hover:bg-purple-700">Room Board</a>
@if(auth()->user()->isAdmin())
<a href="{{ route('admin.treatment-rooms.create') }}" class="bg-blue-600 text-white px-4 py-2 rounded-lg text-sm hover:bg-blue-700">+ Add Room</a>
@endif
@endsection

@section('content')
{{-- Desktop table --}}
<div class="hidden md:block bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden">
    <table class="min-w-full divide-y divide-gray-200">
        <thead class="bg-gray-50">
            <tr>
                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Room Name</th>
                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Code</th>
                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Description</th>
                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Today's Sessions</th>
                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Status</th>
                <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase">Actions</th>
            </tr>
        </thead>
        <tbody class="divide-y divide-gray-100">
            @forelse($rooms as $room)
            <tr class="hover:bg-gray-50">
                <td class="px-6 py-4 text-sm font-medium text-gray-800">{{ $room->name }}</td>
                <td class="px-6 py-4 text-sm text-gray-600">{{ $room->room_code ?? '-' }}</td>
                <td class="px-6 py-4 text-sm text-gray-600 max-w-xs truncate">{{ $room->description ?? '-' }}</td>
                <td class="px-6 py-4 text-sm text-gray-600">{{ $room->assignments_count }}</td>
                <td class="px-6 py-4">
                    <x-badge :color="$room->status === 'active' ? 'green' : ($room->status === 'closed' ? 'red' : 'gray')">{{ ucfirst($room->status) }}</x-badge>
                </td>
                <td class="px-6 py-4 text-right space-x-2">
                    @if(auth()->user()->isAdmin())
                    <a href="{{ route('admin.treatment-rooms.edit', $room) }}" class="text-yellow-600 hover:underline text-sm">Edit</a>
                    <form method="POST" action="{{ route('admin.treatment-rooms.destroy', $room) }}" class="inline" x-data
                          @submit.prevent="if(confirm('Delete this room?')) $el.submit()">
                        @csrf @method('DELETE')
                        <button type="submit" class="text-red-600 hover:underline text-sm">Delete</button>
                    </form>
                    @endif
                </td>
            </tr>
            @empty
            <tr><td colspan="6"><x-empty-state message="No treatment rooms found." action="+ Add Room" :actionUrl="route('admin.treatment-rooms.create')" /></td></tr>
            @endforelse
        </tbody>
    </table>
</div>

{{-- Mobile cards --}}
<div class="md:hidden space-y-3">
    @forelse($rooms as $room)
    <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-4">
        <div class="flex justify-between items-start mb-2">
            <div>
                <p class="font-semibold text-gray-800">{{ $room->name }}</p>
                <p class="text-sm text-gray-500">{{ $room->room_code ?? 'No code' }}</p>
            </div>
            <x-badge :color="$room->status === 'active' ? 'green' : ($room->status === 'closed' ? 'red' : 'gray')">{{ ucfirst($room->status) }}</x-badge>
        </div>
        <p class="text-sm text-gray-600 mb-2">{{ $room->description ?? 'No description' }}</p>
        <p class="text-xs text-gray-500 mb-3">Today's sessions: {{ $room->assignments_count }}</p>
        @if(auth()->user()->isAdmin())
        <div class="flex space-x-2">
            <a href="{{ route('admin.treatment-rooms.edit', $room) }}" class="flex-1 text-center bg-yellow-50 text-yellow-700 py-2 rounded-lg text-sm font-medium">Edit</a>
        </div>
        @endif
    </div>
    @empty
    <x-empty-state message="No treatment rooms found." action="+ Add Room" :actionUrl="route('admin.treatment-rooms.create')" />
    @endforelse
</div>

<div class="mt-4">{{ $rooms->links() }}</div>
@endsection
