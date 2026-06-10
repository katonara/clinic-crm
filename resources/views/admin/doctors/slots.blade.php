@extends('layouts.app')
@section('title', 'Doctor Slots - ' . $doctor->user->name)
@section('page-title', 'Slots: ' . $doctor->user->name)
@section('header-actions')
<a href="{{ route('admin.doctors.show', $doctor) }}" class="bg-gray-200 text-gray-700 px-4 py-2 rounded-lg text-sm hover:bg-gray-300">Back to Profile</a>
@endsection

@section('content')
<div x-data="doctorSlots()" x-init="init()" class="space-y-4">
    {{-- Controls --}}
    <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-4">
        <div class="flex flex-wrap items-center gap-3">
            <button @click="prevPeriod()" class="p-2 rounded-lg border border-gray-300 hover:bg-gray-50">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/></svg>
            </button>
            <button @click="goToday()" class="px-3 py-2 rounded-lg border border-gray-300 hover:bg-gray-50 text-sm">Today</button>
            <button @click="nextPeriod()" class="p-2 rounded-lg border border-gray-300 hover:bg-gray-50">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg>
            </button>
            <span class="text-lg font-semibold text-gray-800" x-text="headerTitle"></span>
            <div class="ml-auto flex gap-1">
                <button @click="setView('daily')" :class="view === 'daily' ? 'bg-blue-600 text-white' : 'bg-gray-100 text-gray-600'" class="px-3 py-1.5 rounded-lg text-sm font-medium">Day</button>
                <button @click="setView('weekly')" :class="view === 'weekly' ? 'bg-blue-600 text-white' : 'bg-gray-100 text-gray-600'" class="px-3 py-1.5 rounded-lg text-sm font-medium">Week</button>
                <button @click="setView('monthly')" :class="view === 'monthly' ? 'bg-blue-600 text-white' : 'bg-gray-100 text-gray-600'" class="px-3 py-1.5 rounded-lg text-sm font-medium">Month</button>
            </div>
        </div>
    </div>

    {{-- Slot grid --}}
    <div class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-x-auto">
        {{-- Daily view --}}
        <template x-if="view === 'daily'">
            <div class="min-w-[400px]">
                <div class="grid grid-cols-1 divide-y divide-gray-100">
                    <template x-for="hour in hours" :key="hour">
                        <div class="flex items-stretch min-h-[60px] hover:bg-gray-50 relative"
                             @dragover.prevent @drop="onDrop($event, currentDate, hour)">
                            <div class="w-20 flex-shrink-0 px-3 py-2 text-xs text-gray-500 border-r border-gray-100 bg-gray-50 flex items-center" x-text="hour"></div>
                            <div class="flex-1 px-3 py-1 relative">
                                <template x-for="evt in getEventsForSlot(currentDate, hour)" :key="evt.id">
                                    <div class="mb-1 px-3 py-2 rounded-lg text-sm cursor-move border-l-4"
                                         :class="eventClasses(evt)"
                                         draggable="true"
                                         @dragstart="onDragStart($event, evt)">
                                        <p class="font-medium" x-text="evt.patient_name"></p>
                                        <p class="text-xs opacity-75" x-text="evt.service_name"></p>
                                        <p class="text-xs opacity-60" x-text="evt.room ? 'Room: ' + evt.room : ''"></p>
                                    </div>
                                </template>
                            </div>
                        </div>
                    </template>
                </div>
            </div>
        </template>

        {{-- Weekly view --}}
        <template x-if="view === 'weekly'">
            <div class="min-w-[800px]">
                <div class="grid grid-cols-8 border-b border-gray-200">
                    <div class="p-2 bg-gray-50 border-r border-gray-200 text-xs text-gray-500">Time</div>
                    <template x-for="day in weekDays" :key="day.date">
                        <div class="p-2 bg-gray-50 border-r border-gray-200 text-center">
                            <div class="text-xs text-gray-500" x-text="day.label"></div>
                            <div class="text-sm font-semibold" :class="day.isToday ? 'text-blue-600' : 'text-gray-800'" x-text="day.day"></div>
                        </div>
                    </template>
                </div>
                <template x-for="hour in hours" :key="hour">
                    <div class="grid grid-cols-8 border-b border-gray-50">
                        <div class="p-2 text-xs text-gray-500 border-r border-gray-100 bg-gray-50 flex items-start" x-text="hour"></div>
                        <template x-for="day in weekDays" :key="day.date">
                            <div class="p-1 border-r border-gray-50 min-h-[50px]"
                                 @dragover.prevent @drop="onDrop($event, day.date, hour)">
                                <template x-for="evt in getEventsForSlot(day.date, hour)" :key="evt.id">
                                    <div class="mb-1 px-2 py-1 rounded text-xs cursor-move border-l-2"
                                         :class="eventClasses(evt)"
                                         draggable="true"
                                         @dragstart="onDragStart($event, evt)">
                                        <p class="font-medium truncate" x-text="evt.patient_name"></p>
                                        <p class="opacity-75 truncate" x-text="evt.service_name"></p>
                                    </div>
                                </template>
                            </div>
                        </template>
                    </div>
                </template>
            </div>
        </template>

        {{-- Monthly view --}}
        <template x-if="view === 'monthly'">
            <div class="p-4">
                <div class="grid grid-cols-7 gap-1 mb-2">
                    <template x-for="d in ['Mon','Tue','Wed','Thu','Fri','Sat','Sun']" :key="d">
                        <div class="text-center text-xs font-medium text-gray-500 py-1" x-text="d"></div>
                    </template>
                </div>
                <div class="grid grid-cols-7 gap-1">
                    <template x-for="cell in monthCells" :key="cell.key">
                        <div class="border border-gray-100 rounded-lg min-h-[80px] p-1"
                             :class="cell.isCurrentMonth ? 'bg-white' : 'bg-gray-50'">
                            <div class="text-xs font-medium mb-1" :class="cell.isToday ? 'text-blue-600 font-bold' : 'text-gray-600'" x-text="cell.day"></div>
                            <template x-for="evt in getEventsForDate(cell.date)" :key="evt.id">
                                <div class="text-xs px-1 py-0.5 rounded mb-0.5 truncate"
                                     :class="eventClasses(evt)" x-text="evt.patient_name"></div>
                            </template>
                        </div>
                    </template>
                </div>
            </div>
        </template>
    </div>
</div>

@push('scripts')
<script>
function doctorSlots() {
    return {
        view: '{{ $view }}',
        currentDate: '{{ $date }}',
        events: [],
        hours: [],

        init() {
            this.generateHours();
            this.loadEvents();
        },

        generateHours() {
            this.hours = [];
            for (let h = 10; h <= 17; h++) {
                this.hours.push(String(h).padStart(2, '0') + ':00');
            }
        },

        get headerTitle() {
            const d = new Date(this.currentDate + 'T00:00:00');
            if (this.view === 'daily') return d.toLocaleDateString('en-US', {weekday:'long', year:'numeric', month:'long', day:'numeric'});
            if (this.view === 'weekly') {
                const start = this.getWeekStart(d);
                const end = new Date(start); end.setDate(end.getDate() + 6);
                return start.toLocaleDateString('en-US', {month:'short', day:'numeric'}) + ' - ' + end.toLocaleDateString('en-US', {month:'short', day:'numeric', year:'numeric'});
            }
            return d.toLocaleDateString('en-US', {month:'long', year:'numeric'});
        },

        get weekDays() {
            const d = new Date(this.currentDate + 'T00:00:00');
            const start = this.getWeekStart(d);
            const days = [];
            const today = new Date().toISOString().split('T')[0];
            for (let i = 0; i < 7; i++) {
                const day = new Date(start);
                day.setDate(day.getDate() + i);
                const dateStr = day.toISOString().split('T')[0];
                days.push({
                    date: dateStr,
                    label: day.toLocaleDateString('en-US', {weekday:'short'}),
                    day: day.getDate(),
                    isToday: dateStr === today
                });
            }
            return days;
        },

        get monthCells() {
            const d = new Date(this.currentDate + 'T00:00:00');
            const year = d.getFullYear(), month = d.getMonth();
            const firstDay = new Date(year, month, 1);
            const lastDay = new Date(year, month + 1, 0);
            let startDay = (firstDay.getDay() + 6) % 7;
            const cells = [];
            const today = new Date().toISOString().split('T')[0];

            for (let i = startDay - 1; i >= 0; i--) {
                const dd = new Date(year, month, -i);
                cells.push({ key: 'p'+i, date: dd.toISOString().split('T')[0], day: dd.getDate(), isCurrentMonth: false, isToday: false });
            }
            for (let i = 1; i <= lastDay.getDate(); i++) {
                const dd = new Date(year, month, i);
                const dateStr = dd.toISOString().split('T')[0];
                cells.push({ key: 'c'+i, date: dateStr, day: i, isCurrentMonth: true, isToday: dateStr === today });
            }
            const remaining = 42 - cells.length;
            for (let i = 1; i <= remaining; i++) {
                const dd = new Date(year, month + 1, i);
                cells.push({ key: 'n'+i, date: dd.toISOString().split('T')[0], day: dd.getDate(), isCurrentMonth: false, isToday: false });
            }
            return cells;
        },

        getWeekStart(d) {
            const day = d.getDay();
            const diff = (day + 6) % 7;
            const start = new Date(d);
            start.setDate(start.getDate() - diff);
            return start;
        },

        setView(v) { this.view = v; this.loadEvents(); },
        goToday() { this.currentDate = new Date().toISOString().split('T')[0]; this.loadEvents(); },

        prevPeriod() {
            const d = new Date(this.currentDate + 'T00:00:00');
            if (this.view === 'daily') d.setDate(d.getDate() - 1);
            else if (this.view === 'weekly') d.setDate(d.getDate() - 7);
            else d.setMonth(d.getMonth() - 1);
            this.currentDate = d.toISOString().split('T')[0];
            this.loadEvents();
        },

        nextPeriod() {
            const d = new Date(this.currentDate + 'T00:00:00');
            if (this.view === 'daily') d.setDate(d.getDate() + 1);
            else if (this.view === 'weekly') d.setDate(d.getDate() + 7);
            else d.setMonth(d.getMonth() + 1);
            this.currentDate = d.toISOString().split('T')[0];
            this.loadEvents();
        },

        async loadEvents() {
            let start, end;
            const d = new Date(this.currentDate + 'T00:00:00');
            if (this.view === 'daily') { start = end = this.currentDate; }
            else if (this.view === 'weekly') {
                const ws = this.getWeekStart(d);
                start = ws.toISOString().split('T')[0];
                const we = new Date(ws); we.setDate(we.getDate() + 6);
                end = we.toISOString().split('T')[0];
            } else {
                start = new Date(d.getFullYear(), d.getMonth(), 1).toISOString().split('T')[0];
                end = new Date(d.getFullYear(), d.getMonth() + 1, 0).toISOString().split('T')[0];
            }

            const url = `{{ route('admin.doctors.slot-events', $doctor) }}?start=${start}&end=${end}`;
            const res = await fetch(url);
            this.events = await res.json();
        },

        getEventsForSlot(date, hour) {
            return this.events.filter(e => {
                const eDate = e.start.split('T')[0];
                const eHour = e.start.split('T')[1].substring(0, 5);
                return eDate === date && eHour === hour;
            });
        },

        getEventsForDate(date) {
            return this.events.filter(e => e.start.split('T')[0] === date);
        },

        eventClasses(evt) {
            const colors = { pending:'bg-yellow-50 border-yellow-400 text-yellow-800', confirmed:'bg-blue-50 border-blue-400 text-blue-800', completed:'bg-green-50 border-green-400 text-green-800' };
            return colors[evt.status] || 'bg-gray-50 border-gray-400 text-gray-800';
        },

        // Drag and drop
        draggedEvent: null,
        onDragStart(e, evt) {
            this.draggedEvent = evt;
            e.dataTransfer.effectAllowed = 'move';
        },

        async onDrop(e, date, hour) {
            e.preventDefault();
            if (!this.draggedEvent) return;

            const res = await fetch(`{{ route('admin.doctors.move-slot', $doctor) }}`, {
                method: 'POST',
                headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': '{{ csrf_token() }}' },
                body: JSON.stringify({ booking_id: this.draggedEvent.id, new_date: date, new_start_time: hour })
            });

            const data = await res.json();
            if (data.error) { alert(data.error); }
            else { this.loadEvents(); }
            this.draggedEvent = null;
        }
    }
}
</script>
@endpush
@endsection
