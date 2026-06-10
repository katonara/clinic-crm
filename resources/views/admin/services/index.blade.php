@extends('layouts.app')
@section('title', 'Services')
@section('page-title', 'Services')
@section('header-actions')
@if(auth()->user()->isAdmin())
<a href="{{ route('admin.services.create') }}" class="bg-blue-600 text-white px-4 py-2 rounded-lg text-sm hover:bg-blue-700">+ Add Service</a>
@endif
@endsection

@section('content')
<div class="grid sm:grid-cols-2 lg:grid-cols-3 gap-4">
    @forelse($services as $service)
    <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-5">
        <div class="flex justify-between items-start mb-3">
            <h3 class="font-semibold text-gray-800">{{ $service->name }}</h3>
            <x-badge :color="$service->status === 'active' ? 'green' : 'gray'">{{ ucfirst($service->status) }}</x-badge>
        </div>
        <p class="text-sm text-gray-500 mb-3">{{ Str::limit($service->description, 80) ?: 'No description' }}</p>
        <div class="flex justify-between items-center text-sm">
            <div>
                <span class="font-bold text-blue-600">RM {{ number_format($service->price, 2) }}</span>
                <span class="text-gray-400 ml-2">{{ $service->duration_minutes }} min</span>
            </div>
            @if(auth()->user()->isAdmin())
            <a href="{{ route('admin.services.edit', $service) }}" class="text-blue-600 hover:underline text-sm">Edit</a>
            @endif
        </div>
        <div class="mt-2 text-xs text-gray-400">{{ $service->staff_count }} staff &middot; {{ $service->bookings_count }} bookings</div>
    </div>
    @empty
    <div class="col-span-full"><x-empty-state message="No services yet." action="Add Service" :actionUrl="route('admin.services.create')" /></div>
    @endforelse
</div>
<div class="mt-4">{{ $services->links() }}</div>
@endsection
