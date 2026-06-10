@extends('layouts.app')
@section('title', 'Patient Detail')
@section('page-title', $patient->name)
@section('header-actions')
<a href="{{ route('admin.patients.edit', $patient) }}" class="bg-blue-600 text-white px-4 py-2 rounded-lg text-sm hover:bg-blue-700">Edit</a>
<a href="{{ route('admin.follow-ups.create', ['patient_id' => $patient->id]) }}" class="bg-green-600 text-white px-4 py-2 rounded-lg text-sm hover:bg-green-700">+ Follow-up</a>
<a href="{{ route('admin.patient-packages.create', ['patient_id' => $patient->id]) }}" class="bg-purple-600 text-white px-4 py-2 rounded-lg text-sm hover:bg-purple-700">+ Package</a>
@endsection

@section('content')
<div class="grid lg:grid-cols-3 gap-6">
    {{-- Left column: patient info --}}
    <div class="space-y-6">
        <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6">
            <h3 class="font-semibold text-gray-800 mb-4">Patient Info</h3>
            <dl class="space-y-3 text-sm">
                <div><dt class="text-gray-500">Email</dt><dd class="text-gray-800">{{ $patient->email ?? '-' }}</dd></div>
                <div><dt class="text-gray-500">WhatsApp</dt><dd class="text-gray-800">{{ $patient->country_code }}{{ $patient->whatsapp_number }}
                    @if($patient->whatsapp_number)
                    <a href="https://wa.me/{{ ltrim($patient->country_code, '+') }}{{ $patient->whatsapp_number }}" target="_blank" class="ml-2 text-green-600 hover:underline text-xs">Chat</a>
                    @endif
                </dd></div>
                <div><dt class="text-gray-500">Gender</dt><dd class="text-gray-800 capitalize">{{ $patient->gender ?? '-' }}</dd></div>
                <div><dt class="text-gray-500">DOB</dt><dd class="text-gray-800">{{ $patient->date_of_birth?->format('d M Y') ?? '-' }}</dd></div>
                <div><dt class="text-gray-500">Tag</dt><dd>@if($patient->tag)<x-badge color="blue">{{ $patient->tag }}</x-badge>@else - @endif</dd></div>
                <div><dt class="text-gray-500">Notes</dt><dd class="text-gray-800">{{ $patient->notes ?? '-' }}</dd></div>
            </dl>
        </div>

        {{-- Package/Session Balance --}}
        <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6">
            <div class="flex items-center justify-between mb-4">
                <h3 class="font-semibold text-gray-800">Packages</h3>
                <a href="{{ route('admin.patient-packages.create', ['patient_id' => $patient->id]) }}" class="text-blue-600 hover:underline text-xs">+ Add</a>
            </div>
            @forelse($patient->packages as $pkg)
            <div class="mb-3 p-3 rounded-lg border {{ $pkg->status === 'active' ? 'border-green-200 bg-green-50' : 'border-gray-200 bg-gray-50' }}">
                <div class="flex justify-between items-start mb-1">
                    <p class="text-sm font-medium text-gray-800">{{ $pkg->package_name }}</p>
                    <x-badge :color="$pkg->statusColor()">{{ ucfirst($pkg->status) }}</x-badge>
                </div>
                <p class="text-xs text-gray-600">{{ $pkg->service?->name ?? '-' }}</p>
                <div class="mt-2 flex items-center justify-between">
                    <div class="text-xs text-gray-500">Used: {{ $pkg->used_sessions }}/{{ $pkg->total_sessions }}</div>
                    <div class="text-sm font-bold {{ $pkg->remaining_sessions <= 1 && $pkg->status === 'active' ? 'text-red-600' : 'text-gray-800' }}">
                        {{ $pkg->remaining_sessions }} left
                    </div>
                </div>
                {{-- Session progress bar --}}
                @if($pkg->total_sessions > 0)
                <div class="mt-2 w-full bg-gray-200 rounded-full h-1.5">
                    <div class="h-1.5 rounded-full {{ $pkg->remaining_sessions <= 1 ? 'bg-red-500' : 'bg-green-500' }}"
                         style="width: {{ ($pkg->used_sessions / $pkg->total_sessions) * 100 }}%"></div>
                </div>
                @endif
                @if($pkg->status === 'active' && $pkg->remaining_sessions > 0)
                <form method="POST" action="{{ route('admin.patient-packages.deduct', $pkg) }}" class="mt-2" x-data
                      @submit.prevent="if(confirm('Deduct 1 session from this package?')) $el.submit()">
                    @csrf
                    <button type="submit" class="w-full text-center bg-green-600 text-white py-1.5 rounded-lg text-xs font-medium hover:bg-green-700">Use Session</button>
                </form>
                @endif
            </div>
            @empty
            <p class="text-sm text-gray-400">No packages.</p>
            @endforelse
        </div>
    </div>

    {{-- Right column: history sections --}}
    <div class="lg:col-span-2 space-y-6">
        {{-- Booking History --}}
        <div class="bg-white rounded-xl shadow-sm border border-gray-100">
            <div class="p-4 border-b border-gray-100"><h3 class="font-semibold text-gray-800">Booking History</h3></div>
            <div class="divide-y divide-gray-100">
                @forelse($patient->bookings->sortByDesc('booking_date') as $booking)
                <a href="{{ route('admin.bookings.show', $booking) }}" class="block p-4 hover:bg-gray-50">
                    <div class="flex justify-between items-start">
                        <div>
                            <p class="font-medium text-gray-800">{{ $booking->service->name }}</p>
                            <p class="text-sm text-gray-500">{{ $booking->booking_date->format('d M Y') }} at {{ $booking->start_time }}</p>
                            @if($booking->staffMember)
                            <p class="text-xs text-gray-400">Dr. {{ $booking->staffMember->name }}</p>
                            @endif
                        </div>
                        <x-badge :color="$booking->statusBadgeColor()">{{ ucfirst($booking->status) }}</x-badge>
                    </div>
                </a>
                @empty
                <x-empty-state message="No bookings yet." />
                @endforelse
            </div>
        </div>

        {{-- Treatment History --}}
        <div class="bg-white rounded-xl shadow-sm border border-gray-100">
            <div class="p-4 border-b border-gray-100"><h3 class="font-semibold text-gray-800">Treatment History</h3></div>
            <div class="divide-y divide-gray-100">
                @forelse($patient->treatmentHistories->sortByDesc('treatment_date') as $th)
                <div class="p-4">
                    <div class="flex justify-between items-start">
                        <div>
                            <p class="font-medium text-gray-800">{{ $th->treatment_name }}</p>
                            <p class="text-sm text-gray-500">{{ $th->treatment_date->format('d M Y') }}</p>
                            @if($th->package_name)
                            <p class="text-xs text-blue-600 mt-1">Package: {{ $th->package_name }}</p>
                            @endif
                            @if($th->session_number)
                            <p class="text-xs text-gray-500">Session {{ $th->session_number }} of {{ $th->total_sessions }} ({{ $th->remaining_sessions }} remaining)</p>
                            @endif
                            @if($th->doctor)
                            <p class="text-xs text-gray-400 mt-1">Dr. {{ $th->doctor->name }}</p>
                            @endif
                            @if($th->notes)
                            <p class="text-sm text-gray-600 mt-2">{{ $th->notes }}</p>
                            @endif
                        </div>
                        <p class="text-xs text-gray-400">{{ $th->service->name ?? '-' }}</p>
                    </div>
                </div>
                @empty
                <x-empty-state message="No treatment history." />
                @endforelse
            </div>
        </div>

        {{-- Follow-ups --}}
        <div class="bg-white rounded-xl shadow-sm border border-gray-100">
            <div class="p-4 border-b border-gray-100"><h3 class="font-semibold text-gray-800">Follow-ups</h3></div>
            <div class="divide-y divide-gray-100">
                @forelse($patient->followUps->sortByDesc('follow_up_date') as $fu)
                <div class="p-4">
                    <div class="flex justify-between items-start">
                        <div>
                            <p class="text-sm text-gray-800">{{ $fu->follow_up_date->format('d M Y') }}</p>
                            <p class="text-sm text-gray-500">{{ $fu->notes ?? 'No notes' }}</p>
                            <p class="text-xs text-gray-400 mt-1">Assigned: {{ $fu->assignedUser?->name ?? '-' }}</p>
                        </div>
                        <x-badge :color="$fu->statusBadgeColor()">{{ ucfirst(str_replace('_', ' ', $fu->status)) }}</x-badge>
                    </div>
                </div>
                @empty
                <x-empty-state message="No follow-ups." />
                @endforelse
            </div>
        </div>
    </div>
</div>
@endsection
