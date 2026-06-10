@extends('layouts.app')
@section('title', 'Patient Packages')
@section('page-title', 'Patient Packages')
@section('header-actions')
<a href="{{ route('admin.patient-packages.create') }}" class="bg-blue-600 text-white px-4 py-2 rounded-lg text-sm hover:bg-blue-700">+ Add Package</a>
@endsection

@section('content')
{{-- Filters --}}
<div class="bg-white rounded-xl shadow-sm border border-gray-100 p-4 mb-4">
    <form method="GET" class="flex flex-wrap items-end gap-3">
        <div>
            <label class="block text-xs text-gray-500 mb-1">Status</label>
            <select name="status" class="border border-gray-300 rounded-lg px-3 py-2 text-sm">
                <option value="">All</option>
                @foreach(['active', 'completed', 'expired', 'cancelled'] as $s)
                <option value="{{ $s }}" {{ request('status') === $s ? 'selected' : '' }}>{{ ucfirst($s) }}</option>
                @endforeach
            </select>
        </div>
        <button type="submit" class="px-4 py-2 bg-gray-100 rounded-lg text-sm hover:bg-gray-200">Filter</button>
        <a href="{{ route('admin.patient-packages.index') }}" class="px-4 py-2 text-sm text-gray-500 hover:underline">Reset</a>
    </form>
</div>

{{-- Desktop table --}}
<div class="hidden md:block bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden">
    <table class="min-w-full divide-y divide-gray-200">
        <thead class="bg-gray-50">
            <tr>
                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Patient</th>
                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Package</th>
                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Service</th>
                <th class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase">Sessions</th>
                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Period</th>
                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Status</th>
                <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase">Actions</th>
            </tr>
        </thead>
        <tbody class="divide-y divide-gray-100">
            @forelse($packages as $pkg)
            <tr class="hover:bg-gray-50">
                <td class="px-6 py-4 text-sm font-medium text-gray-800">{{ $pkg->patient->name }}</td>
                <td class="px-6 py-4 text-sm text-gray-600">{{ $pkg->package_name }}</td>
                <td class="px-6 py-4 text-sm text-gray-600">{{ $pkg->service?->name ?? '-' }}</td>
                <td class="px-6 py-4 text-center">
                    <span class="text-sm font-semibold {{ $pkg->remaining_sessions <= 1 && $pkg->status === 'active' ? 'text-red-600' : 'text-gray-800' }}">
                        {{ $pkg->used_sessions }}/{{ $pkg->total_sessions }}
                    </span>
                    <span class="text-xs text-gray-500 block">{{ $pkg->remaining_sessions }} left</span>
                </td>
                <td class="px-6 py-4 text-sm text-gray-600">
                    {{ $pkg->start_date->format('d M Y') }}
                    @if($pkg->end_date) - {{ $pkg->end_date->format('d M Y') }} @endif
                </td>
                <td class="px-6 py-4"><x-badge :color="$pkg->statusColor()">{{ ucfirst($pkg->status) }}</x-badge></td>
                <td class="px-6 py-4 text-right space-x-2">
                    <a href="{{ route('admin.patient-packages.edit', $pkg) }}" class="text-yellow-600 hover:underline text-sm">Edit</a>
                    @if($pkg->status === 'active' && $pkg->remaining_sessions > 0)
                    <form method="POST" action="{{ route('admin.patient-packages.deduct', $pkg) }}" class="inline" x-data
                          @submit.prevent="if(confirm('Deduct 1 session?')) $el.submit()">
                        @csrf
                        <button type="submit" class="text-green-600 hover:underline text-sm">Use Session</button>
                    </form>
                    @endif
                    <form method="POST" action="{{ route('admin.patient-packages.destroy', $pkg) }}" class="inline" x-data
                          @submit.prevent="if(confirm('Delete this package?')) $el.submit()">
                        @csrf @method('DELETE')
                        <button type="submit" class="text-red-600 hover:underline text-sm">Delete</button>
                    </form>
                </td>
            </tr>
            @empty
            <tr><td colspan="7"><x-empty-state message="No packages found." action="+ Add Package" :actionUrl="route('admin.patient-packages.create')" /></td></tr>
            @endforelse
        </tbody>
    </table>
</div>

{{-- Mobile cards --}}
<div class="md:hidden space-y-3">
    @forelse($packages as $pkg)
    <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-4">
        <div class="flex justify-between items-start mb-2">
            <div>
                <p class="font-semibold text-gray-800">{{ $pkg->patient->name }}</p>
                <p class="text-sm text-gray-500">{{ $pkg->package_name }}</p>
            </div>
            <x-badge :color="$pkg->statusColor()">{{ ucfirst($pkg->status) }}</x-badge>
        </div>
        <p class="text-sm text-gray-600">{{ $pkg->service?->name ?? '-' }}</p>
        <div class="flex items-center justify-between mt-2">
            <div>
                <span class="text-lg font-bold {{ $pkg->remaining_sessions <= 1 && $pkg->status === 'active' ? 'text-red-600' : 'text-gray-800' }}">{{ $pkg->remaining_sessions }}</span>
                <span class="text-sm text-gray-500">/ {{ $pkg->total_sessions }} remaining</span>
            </div>
        </div>
        <div class="flex space-x-2 mt-3">
            <a href="{{ route('admin.patient-packages.edit', $pkg) }}" class="flex-1 text-center bg-yellow-50 text-yellow-700 py-2 rounded-lg text-sm font-medium">Edit</a>
            @if($pkg->status === 'active' && $pkg->remaining_sessions > 0)
            <form method="POST" action="{{ route('admin.patient-packages.deduct', $pkg) }}" class="flex-1" x-data
                  @submit.prevent="if(confirm('Deduct 1 session?')) $el.submit()">
                @csrf
                <button type="submit" class="w-full text-center bg-green-50 text-green-700 py-2 rounded-lg text-sm font-medium">Use Session</button>
            </form>
            @endif
        </div>
    </div>
    @empty
    <x-empty-state message="No packages found." action="+ Add Package" :actionUrl="route('admin.patient-packages.create')" />
    @endforelse
</div>

<div class="mt-4">{{ $packages->links() }}</div>
@endsection
