@extends('layouts.app')
@section('title', 'Treatment Room Board')
@section('page-title', 'Room Board')
@section('header-actions')
<a href="{{ route('admin.treatment-rooms.index') }}" class="bg-gray-200 text-gray-700 px-4 py-2 rounded-lg text-sm hover:bg-gray-300">Manage Rooms</a>
@endsection

@section('content')
<div x-data="roomBoard()" x-init="init()">
    {{-- Date picker and refresh --}}
    <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-4 mb-4">
        <div class="flex flex-wrap items-center gap-3">
            <label class="text-sm font-medium text-gray-700">Date:</label>
            <input type="date" x-model="date" @change="refresh()"
                   class="border border-gray-300 rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
            <button @click="date = today(); refresh()" class="px-3 py-2 rounded-lg border border-gray-300 hover:bg-gray-50 text-sm">Today</button>
            <button @click="refresh()" class="px-3 py-2 rounded-lg bg-blue-600 text-white text-sm hover:bg-blue-700">Refresh</button>
            <div class="flex items-center space-x-1 text-xs text-gray-400">
                <span class="relative flex h-2 w-2"><span class="animate-ping absolute inline-flex h-full w-full rounded-full bg-green-400 opacity-75"></span><span class="relative inline-flex rounded-full h-2 w-2 bg-green-500"></span></span>
                <span class="hidden sm:inline">Auto-refresh 30s</span>
            </div>

            {{-- Status legend --}}
            <div class="ml-auto flex items-center gap-3 text-xs">
                <span class="flex items-center gap-1"><span class="w-3 h-3 rounded-full bg-green-500"></span> Available</span>
                <span class="flex items-center gap-1"><span class="w-3 h-3 rounded-full bg-red-500"></span> Occupied</span>
                <span class="flex items-center gap-1"><span class="w-3 h-3 rounded-full bg-yellow-500"></span> Ending Soon</span>
                <span class="flex items-center gap-1"><span class="w-3 h-3 rounded-full bg-gray-400"></span> Closed</span>
            </div>
        </div>
    </div>

    <div class="grid lg:grid-cols-4 gap-4">
        {{-- Unassigned bookings sidebar --}}
        <div class="lg:col-span-1">
            <div class="bg-white rounded-xl shadow-sm border border-gray-100 sticky top-4">
                <div class="p-4 border-b border-gray-100">
                    <h3 class="font-semibold text-gray-800 text-sm">Unassigned Bookings</h3>
                    <p class="text-xs text-gray-500 mt-1">Drag to a room to assign</p>
                </div>
                <div class="p-3 space-y-2 max-h-[70vh] overflow-y-auto">
                    @forelse($unassignedBookings as $booking)
                    <div class="bg-blue-50 border border-blue-200 rounded-lg p-3 cursor-move"
                         draggable="true"
                         @dragstart="onDragBooking($event, {{ $booking->id }})"
                         id="booking-{{ $booking->id }}">
                        <p class="text-sm font-medium text-blue-800">{{ $booking->patient->name }}</p>
                        <p class="text-xs text-blue-600">{{ $booking->service->name }}</p>
                        <p class="text-xs text-blue-500">{{ $booking->start_time }} - {{ $booking->end_time }}</p>
                        @if($booking->staffMember)
                        <p class="text-xs text-blue-400 mt-1">Dr. {{ $booking->staffMember->name }}</p>
                        @endif
                        {{-- Mobile: Assign to Room button (drag-and-drop doesn't work on touch) --}}
                        <div class="md:hidden mt-2" x-data="{ showRooms: false }">
                            <button @click="showRooms = !showRooms" class="w-full text-xs bg-blue-600 text-white px-3 py-1.5 rounded-lg hover:bg-blue-700 transition-colors">
                                <span x-text="showRooms ? 'Cancel' : 'Assign to Room'"></span>
                            </button>
                            <div x-show="showRooms" x-cloak class="mt-2 space-y-1">
                                @foreach($rooms as $room)
                                @if($room->status === 'active')
                                <button @click="assignBookingToRoom({{ $booking->id }}, {{ $room->id }})"
                                        class="w-full text-left text-xs bg-white border border-gray-200 px-3 py-2 rounded-lg hover:bg-blue-50 hover:border-blue-300 transition-colors flex items-center justify-between">
                                    <span>{{ $room->name }} <span class="text-gray-400">({{ $room->room_code }})</span></span>
                                    <svg class="w-3 h-3 text-blue-500" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg>
                                </button>
                                @endif
                                @endforeach
                            </div>
                        </div>
                    </div>
                    @empty
                    <p class="text-sm text-gray-400 text-center py-4">No unassigned bookings.</p>
                    @endforelse
                </div>
            </div>
        </div>

        {{-- Room grid --}}
        <div class="lg:col-span-3">
            {{-- Desktop grid --}}
            <div class="hidden md:grid md:grid-cols-2 xl:grid-cols-3 gap-4">
                @foreach($rooms as $room)
                @php $liveStatus = $room->liveStatus($date); @endphp
                <div class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden"
                     @dragover.prevent
                     @drop="onDropToRoom($event, {{ $room->id }})">
                    {{-- Room header --}}
                    <div class="p-4 border-b border-gray-100 flex items-center justify-between">
                        <div>
                            <h4 class="font-semibold text-gray-800">{{ $room->name }}</h4>
                            <p class="text-xs text-gray-500">{{ $room->room_code ?? '' }}</p>
                        </div>
                        @php $statusColor = \App\Models\TreatmentRoom::statusColor($liveStatus); @endphp
                        <x-badge :color="$statusColor">{{ ucfirst(str_replace('_', ' ', $liveStatus)) }}</x-badge>
                    </div>

                    {{-- Assignments --}}
                    <div class="p-3 space-y-2 min-h-[120px]">
                        @forelse($room->assignments as $assignment)
                        <div class="rounded-lg border p-3 {{ $assignment->status === 'occupied' ? 'bg-red-50 border-red-200' : ($assignment->status === 'ending_soon' ? 'bg-yellow-50 border-yellow-200' : 'bg-gray-50 border-gray-200') }}"
                             draggable="true"
                             @dragstart="onDragAssignment($event, {{ $assignment->id }})">
                            <div class="flex justify-between items-start mb-1">
                                <p class="text-sm font-medium text-gray-800">{{ $assignment->patient->name }}</p>
                                <x-badge :color="$assignment->statusColor()">{{ ucfirst(str_replace('_', ' ', $assignment->status)) }}</x-badge>
                            </div>
                            <p class="text-xs text-gray-600">{{ $assignment->booking->service->name ?? '-' }}</p>
                            <p class="text-xs text-gray-500">{{ \Carbon\Carbon::parse($assignment->start_time)->format('h:i A') }} - {{ \Carbon\Carbon::parse($assignment->end_time)->format('h:i A') }}</p>
                            @if($assignment->doctor)
                            <p class="text-xs text-gray-400 mt-1">Dr. {{ $assignment->doctor->name }}</p>
                            @endif
                            {{-- Remaining time indicator --}}
                            @if($assignment->status === 'occupied' || $assignment->status === 'ending_soon')
                            @php
                                $endTime = \Carbon\Carbon::parse($assignment->end_time);
                                $minutesLeft = now()->diffInMinutes($endTime, false);
                            @endphp
                            @if($minutesLeft > 0)
                            <div class="mt-2 text-xs font-medium {{ $minutesLeft <= 10 ? 'text-yellow-600' : 'text-gray-500' }}">
                                {{ $minutesLeft }} min remaining
                            </div>
                            @endif
                            @endif
                        </div>
                        @empty
                        <div class="flex items-center justify-center h-20 text-sm text-gray-400 border-2 border-dashed border-gray-200 rounded-lg">
                            Drop booking here
                        </div>
                        @endforelse
                    </div>
                </div>
                @endforeach
            </div>

            {{-- Mobile stacked cards --}}
            <div class="md:hidden space-y-4">
                @foreach($rooms as $room)
                @php $liveStatus = $room->liveStatus($date); @endphp
                <div class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden"
                     @dragover.prevent
                     @drop="onDropToRoom($event, {{ $room->id }})">
                    <div class="p-4 border-b border-gray-100 flex items-center justify-between">
                        <div>
                            <h4 class="font-semibold text-gray-800">{{ $room->name }}</h4>
                            <p class="text-xs text-gray-500">{{ $room->room_code ?? '' }}</p>
                        </div>
                        @php $statusColor = \App\Models\TreatmentRoom::statusColor($liveStatus); @endphp
                        <x-badge :color="$statusColor">{{ ucfirst(str_replace('_', ' ', $liveStatus)) }}</x-badge>
                    </div>
                    <div class="p-3 space-y-2">
                        @forelse($room->assignments as $assignment)
                        <div class="rounded-lg border p-3 {{ $assignment->status === 'occupied' ? 'bg-red-50 border-red-200' : 'bg-gray-50 border-gray-200' }}">
                            <p class="text-sm font-medium text-gray-800">{{ $assignment->patient->name }}</p>
                            <p class="text-xs text-gray-600">{{ $assignment->booking->service->name ?? '-' }}</p>
                            <p class="text-xs text-gray-500">{{ \Carbon\Carbon::parse($assignment->start_time)->format('h:i A') }} - {{ \Carbon\Carbon::parse($assignment->end_time)->format('h:i A') }}</p>
                            @if($assignment->doctor)
                            <p class="text-xs text-gray-400 mt-1">Dr. {{ $assignment->doctor->name }}</p>
                            @endif
                        </div>
                        @empty
                        <p class="text-sm text-gray-400 text-center py-3">No sessions</p>
                        @endforelse
                    </div>
                </div>
                @endforeach
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
function roomBoard() {
    return {
        date: '{{ $date }}',
        dragType: null,
        dragId: null,

        autoRefreshInterval: null,

        init() {
            // Auto-refresh every 30 seconds for live room status
            this.autoRefreshInterval = setInterval(() => {
                if (this.date === this.today()) this.refresh();
            }, 30000);
        },

        today() { return new Date().toISOString().split('T')[0]; },

        refresh() { window.location.href = `{{ route('admin.treatment-rooms.board') }}?date=${this.date}`; },

        onDragBooking(e, bookingId) {
            this.dragType = 'booking';
            this.dragId = bookingId;
            e.dataTransfer.setData('text/plain', JSON.stringify({type:'booking', id:bookingId}));
            e.dataTransfer.effectAllowed = 'move';
        },

        onDragAssignment(e, assignmentId) {
            this.dragType = 'assignment';
            this.dragId = assignmentId;
            e.dataTransfer.setData('text/plain', JSON.stringify({type:'assignment', id:assignmentId}));
            e.dataTransfer.effectAllowed = 'move';
        },

        async assignBookingToRoom(bookingId, roomId) {
            const csrfToken = document.querySelector('meta[name="csrf-token"]').content;
            const res = await fetch('{{ route("admin.treatment-rooms.assign") }}', {
                method: 'POST',
                headers: {'Content-Type':'application/json', 'X-CSRF-TOKEN': csrfToken},
                body: JSON.stringify({treatment_room_id: roomId, booking_id: bookingId})
            });
            const result = await res.json();
            if (result.error) { alert(result.error); } else { this.refresh(); }
        },

        async onDropToRoom(e, roomId) {
            e.preventDefault();
            let data;
            try { data = JSON.parse(e.dataTransfer.getData('text/plain')); } catch { return; }

            const csrfToken = document.querySelector('meta[name="csrf-token"]').content;

            if (data.type === 'booking') {
                const res = await fetch('{{ route("admin.treatment-rooms.assign") }}', {
                    method: 'POST',
                    headers: {'Content-Type':'application/json', 'X-CSRF-TOKEN': csrfToken},
                    body: JSON.stringify({treatment_room_id: roomId, booking_id: data.id})
                });
                const result = await res.json();
                if (result.error) { alert(result.error); } else { this.refresh(); }
            }
            else if (data.type === 'assignment') {
                const res = await fetch('{{ route("admin.treatment-rooms.move") }}', {
                    method: 'POST',
                    headers: {'Content-Type':'application/json', 'X-CSRF-TOKEN': csrfToken},
                    body: JSON.stringify({assignment_id: data.id, new_room_id: roomId})
                });
                const result = await res.json();
                if (result.error) { alert(result.error); } else { this.refresh(); }
            }
        }
    }
}
</script>
@endpush
@endsection
