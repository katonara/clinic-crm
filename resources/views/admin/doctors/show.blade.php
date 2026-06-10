@extends('layouts.app')
@section('title', 'Doctor Detail')
@section('page-title', $doctor->user->name)
@section('header-actions')
<a href="{{ route('admin.doctors.slots', $doctor) }}" class="bg-purple-600 text-white px-4 py-2 rounded-lg text-sm hover:bg-purple-700">View Slots</a>
<a href="{{ route('admin.doctors.edit', $doctor) }}" class="bg-blue-600 text-white px-4 py-2 rounded-lg text-sm hover:bg-blue-700">Edit</a>
@endsection

@section('content')
<div class="grid lg:grid-cols-3 gap-6">
    {{-- Doctor info card --}}
    <div class="space-y-6">
        <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6">
            <div class="flex items-center mb-4">
                <div class="w-14 h-14 rounded-full bg-blue-100 flex items-center justify-center text-blue-600 font-bold text-xl">
                    {{ strtoupper(substr($doctor->user->name, 0, 1)) }}
                </div>
                <div class="ml-4">
                    <h3 class="font-semibold text-gray-800 text-lg">{{ $doctor->user->name }}</h3>
                    <p class="text-sm text-gray-500">{{ $doctor->specialty ?? 'General' }}</p>
                </div>
            </div>

            <dl class="space-y-3 text-sm">
                <div><dt class="text-gray-500">Email</dt><dd class="text-gray-800">{{ $doctor->user->email }}</dd></div>
                <div><dt class="text-gray-500">WhatsApp</dt>
                    <dd class="text-gray-800">
                        {{ $doctor->user->country_code }}{{ $doctor->user->whatsapp_number }}
                        @if($doctor->user->whatsapp_number)
                        <a href="https://wa.me/{{ ltrim($doctor->user->country_code, '+') }}{{ $doctor->user->whatsapp_number }}" target="_blank" class="ml-2 text-green-600 hover:underline text-xs">Chat</a>
                        @endif
                    </dd>
                </div>
                <div><dt class="text-gray-500">Status</dt><dd><x-badge :color="$doctor->status === 'active' ? 'green' : 'gray'">{{ ucfirst($doctor->status) }}</x-badge></dd></div>
                <div><dt class="text-gray-500">Bio</dt><dd class="text-gray-800">{{ $doctor->bio ?? '-' }}</dd></div>
            </dl>
        </div>

        {{-- Assigned services --}}
        <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6">
            <h3 class="font-semibold text-gray-800 mb-3">Assigned Services</h3>
            @forelse($doctor->user->services as $service)
            <div class="flex items-center justify-between py-2 {{ !$loop->last ? 'border-b border-gray-100' : '' }}">
                <span class="text-sm text-gray-700">{{ $service->name }}</span>
                <span class="text-xs text-gray-500">{{ $service->duration_minutes }}min</span>
            </div>
            @empty
            <p class="text-sm text-gray-400">No services assigned.</p>
            @endforelse
        </div>
    </div>

    {{-- Assigned bookings --}}
    <div class="lg:col-span-2">
        <div class="bg-white rounded-xl shadow-sm border border-gray-100">
            <div class="p-4 border-b border-gray-100">
                <h3 class="font-semibold text-gray-800">Assigned Bookings</h3>
            </div>
            <div class="divide-y divide-gray-100">
                @forelse($doctor->user->bookingsAsStaff->sortByDesc('booking_date') as $booking)
                <a href="{{ route('admin.bookings.show', $booking) }}" class="block p-4 hover:bg-gray-50">
                    <div class="flex justify-between items-start">
                        <div>
                            <p class="font-medium text-gray-800">{{ $booking->patient->name }}</p>
                            <p class="text-sm text-gray-500">{{ $booking->service->name }}</p>
                            <p class="text-sm text-gray-400">{{ $booking->booking_date->format('d M Y') }} at {{ $booking->start_time }}</p>
                        </div>
                        <x-badge :color="$booking->statusBadgeColor()">{{ ucfirst($booking->status) }}</x-badge>
                    </div>
                </a>
                @empty
                <x-empty-state message="No bookings assigned to this doctor." />
                @endforelse
            </div>
        </div>
    </div>
</div>
@endsection
