@extends('layouts.app')
@section('title', 'Follow-ups')
@section('page-title', 'Follow-ups')
@section('header-actions')
<a href="{{ route('admin.follow-ups.create') }}" class="bg-blue-600 text-white px-4 py-2 rounded-lg text-sm hover:bg-blue-700">+ Add Follow-up</a>
@endsection

@section('content')
<div class="bg-white rounded-xl shadow-sm border border-gray-100">
    <div class="p-4 border-b border-gray-100">
        <form method="GET" class="grid sm:grid-cols-3 gap-3">
            <input type="text" name="search" value="{{ request('search') }}" placeholder="Search patient..." class="px-3 py-2 border border-gray-300 rounded-lg text-sm">
            <select name="status" class="px-3 py-2 border border-gray-300 rounded-lg text-sm">
                <option value="">All Status</option>
                @foreach(['not_contacted','contacted','interested','not_interested','booked','closed'] as $s)
                <option value="{{ $s }}" {{ request('status') === $s ? 'selected' : '' }}>{{ ucfirst(str_replace('_',' ',$s)) }}</option>
                @endforeach
            </select>
            <button type="submit" class="bg-blue-600 text-white px-4 py-2 rounded-lg text-sm">Filter</button>
        </form>
    </div>

    <div class="divide-y divide-gray-100">
        @forelse($followUps as $fu)
        <div class="p-4 hover:bg-gray-50">
            <div class="flex justify-between items-start mb-2">
                <div>
                    <p class="font-medium text-gray-800">{{ $fu->patient->name }}</p>
                    <p class="text-sm text-gray-500">{{ $fu->follow_up_date->format('d M Y') }} &middot; {{ $fu->assignedUser?->name ?? 'Unassigned' }}</p>
                    @if($fu->booking)<p class="text-xs text-gray-400 mt-1">Booking: {{ $fu->booking->booking_number }}</p>@endif
                    @if($fu->notes)<p class="text-sm text-gray-500 mt-1">{{ Str::limit($fu->notes, 100) }}</p>@endif
                </div>
                <div class="flex items-center space-x-2">
                    <x-badge :color="$fu->statusBadgeColor()">{{ ucfirst(str_replace('_',' ',$fu->status)) }}</x-badge>
                    <a href="{{ route('admin.follow-ups.edit', $fu) }}" class="text-blue-600 hover:underline text-sm">Edit</a>
                    @if($fu->patient->whatsapp_number)
                    @php $waNum = ltrim($fu->patient->country_code, '+') . $fu->patient->whatsapp_number; @endphp
                    <a href="https://wa.me/{{ $waNum }}" target="_blank" class="text-green-600 hover:underline text-sm">WhatsApp</a>
                    @endif
                </div>
            </div>
        </div>
        @empty
        <x-empty-state message="No follow-ups." action="Add Follow-up" :actionUrl="route('admin.follow-ups.create')" />
        @endforelse
    </div>
    <div class="p-4">{{ $followUps->links() }}</div>
</div>
@endsection
