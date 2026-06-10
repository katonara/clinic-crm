@extends('layouts.app')
@section('title', 'Calendar')
@section('page-title', 'Calendar')

@push('styles')
<link href="https://cdn.jsdelivr.net/npm/fullcalendar@6.1.8/index.global.min.css" rel="stylesheet">
@endpush

@section('content')
<div class="bg-white rounded-xl shadow-sm border border-gray-100 p-4 lg:p-6">
    <div class="flex flex-wrap gap-3 mb-4">
        <select id="staffFilter" class="px-3 py-2 border border-gray-300 rounded-lg text-sm">
            <option value="">All Staff</option>
            @foreach($staffMembers as $staff)
            <option value="{{ $staff->id }}">{{ $staff->name }}</option>
            @endforeach
        </select>
        <select id="statusFilter" class="px-3 py-2 border border-gray-300 rounded-lg text-sm">
            <option value="">All Status</option>
            <option value="pending">Pending</option>
            <option value="confirmed">Confirmed</option>
            <option value="completed">Completed</option>
            <option value="cancelled">Cancelled</option>
        </select>
    </div>
    <div id="calendar" class="overflow-x-auto"></div>
</div>
@endsection

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/fullcalendar@6.1.8/index.global.min.js"></script>
<script>
document.addEventListener('DOMContentLoaded', function() {
    var calendarEl = document.getElementById('calendar');
    var calendar = new FullCalendar.Calendar(calendarEl, {
        initialView: window.innerWidth < 768 ? 'listWeek' : 'dayGridMonth',
        headerToolbar: {
            left: 'prev,next today',
            center: 'title',
            right: 'dayGridMonth,timeGridWeek,timeGridDay,listWeek'
        },
        events: function(info, successCallback, failureCallback) {
            var staffId = document.getElementById('staffFilter').value;
            var status = document.getElementById('statusFilter').value;
            var url = '{{ route("admin.calendar.events") }}?start=' + info.startStr + '&end=' + info.endStr;
            if (staffId) url += '&staff_id=' + staffId;
            if (status) url += '&status=' + status;

            fetch(url)
                .then(response => response.json())
                .then(data => successCallback(data))
                .catch(error => failureCallback(error));
        },
        eventClick: function(info) {
            window.location.href = '/admin/bookings/' + info.event.id;
        },
        height: 'auto',
        eventTimeFormat: { hour: '2-digit', minute: '2-digit', hour12: false }
    });
    calendar.render();

    document.getElementById('staffFilter').addEventListener('change', function() { calendar.refetchEvents(); });
    document.getElementById('statusFilter').addEventListener('change', function() { calendar.refetchEvents(); });
});
</script>
@endpush
