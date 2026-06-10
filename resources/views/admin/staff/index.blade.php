@extends('layouts.app')
@section('title', 'Staff')
@section('page-title', 'Staff Management')
@section('header-actions')
<a href="{{ route('admin.staff.create') }}" class="bg-blue-600 text-white px-4 py-2 rounded-lg text-sm hover:bg-blue-700">+ Add Staff</a>
@endsection

@section('content')
<div class="bg-white rounded-xl shadow-sm border border-gray-100">
    <div class="hidden md:block overflow-x-auto">
        <table class="w-full">
            <thead class="bg-gray-50">
                <tr>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Name</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Email</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Role</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Position</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Status</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Actions</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-100">
                @forelse($staff as $member)
                <tr class="hover:bg-gray-50">
                    <td class="px-6 py-4 text-sm font-medium text-gray-800">{{ $member->name }}</td>
                    <td class="px-6 py-4 text-sm text-gray-600">{{ $member->email }}</td>
                    <td class="px-6 py-4"><x-badge :color="$member->role === 'doctor' ? 'blue' : 'purple'">{{ ucfirst($member->role) }}</x-badge></td>
                    <td class="px-6 py-4 text-sm text-gray-600">{{ $member->staffProfile?->position ?? '-' }}</td>
                    <td class="px-6 py-4"><x-badge :color="$member->status === 'active' ? 'green' : 'gray'">{{ ucfirst($member->status) }}</x-badge></td>
                    <td class="px-6 py-4 text-sm">
                        <a href="{{ route('admin.staff.edit', $member) }}" class="text-blue-600 hover:underline">Edit</a>
                    </td>
                </tr>
                @empty
                <tr><td colspan="6"><x-empty-state message="No staff members." action="Add Staff" :actionUrl="route('admin.staff.create')" /></td></tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <div class="md:hidden divide-y divide-gray-100">
        @forelse($staff as $member)
        <a href="{{ route('admin.staff.edit', $member) }}" class="block p-4 hover:bg-gray-50">
            <div class="flex justify-between items-start">
                <div>
                    <p class="font-medium text-gray-800">{{ $member->name }}</p>
                    <p class="text-sm text-gray-500">{{ $member->email }}</p>
                    <p class="text-xs text-gray-400 mt-1">{{ $member->staffProfile?->position ?? 'No position' }}</p>
                </div>
                <div class="flex flex-col items-end space-y-1">
                    <x-badge :color="$member->role === 'doctor' ? 'blue' : 'purple'">{{ ucfirst($member->role) }}</x-badge>
                    <x-badge :color="$member->status === 'active' ? 'green' : 'gray'">{{ ucfirst($member->status) }}</x-badge>
                </div>
            </div>
        </a>
        @empty
        <x-empty-state message="No staff members." action="Add Staff" :actionUrl="route('admin.staff.create')" />
        @endforelse
    </div>

    <div class="p-4">{{ $staff->links() }}</div>
</div>
@endsection
