@extends('layouts.app')
@section('title', 'Schedules')
@section('page-title', 'Schedule Management')

@section('content')
<div class="space-y-6">
    {{-- Staff selector --}}
    <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-4">
        <form method="GET" class="flex flex-wrap gap-3">
            <select name="user_id" class="px-4 py-2 border border-gray-300 rounded-lg text-sm">
                <option value="">Clinic Hours (Default)</option>
                @foreach($staffMembers as $staff)
                <option value="{{ $staff->id }}" {{ $selectedUserId == $staff->id ? 'selected' : '' }}>{{ $staff->name }}</option>
                @endforeach
            </select>
            <button type="submit" class="bg-blue-600 text-white px-4 py-2 rounded-lg text-sm">View</button>
        </form>
    </div>

    {{-- Working Hours --}}
    <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6">
        <h3 class="font-semibold text-gray-800 mb-4">Working Hours</h3>
        <form method="POST" action="{{ route('admin.schedules.working-hours') }}">
            @csrf
            <input type="hidden" name="user_id" value="{{ $selectedUserId }}">
            <div class="space-y-3">
                @for($day = 0; $day < 7; $day++)
                @php $wh = $workingHours->get($day); @endphp
                <div class="grid grid-cols-1 sm:grid-cols-6 gap-2 items-center p-3 bg-gray-50 rounded-lg">
                    <div class="sm:col-span-1">
                        <label class="flex items-center">
                            <input type="checkbox" name="hours[{{ $day }}][is_closed]" {{ $wh && $wh->is_closed ? 'checked' : '' }} class="rounded border-gray-300 text-red-500 mr-2">
                            <span class="text-sm font-medium text-gray-700">{{ \App\Models\WorkingHour::dayName($day) }}</span>
                        </label>
                    </div>
                    <div><input type="time" name="hours[{{ $day }}][opening_time]" value="{{ $wh?->opening_time ?? '09:00' }}" class="w-full px-2 py-1.5 border border-gray-300 rounded text-sm"></div>
                    <div><input type="time" name="hours[{{ $day }}][closing_time]" value="{{ $wh?->closing_time ?? '18:00' }}" class="w-full px-2 py-1.5 border border-gray-300 rounded text-sm"></div>
                    <div><input type="time" name="hours[{{ $day }}][break_start]" value="{{ $wh?->break_start }}" placeholder="Break start" class="w-full px-2 py-1.5 border border-gray-300 rounded text-sm"></div>
                    <div><input type="time" name="hours[{{ $day }}][break_end]" value="{{ $wh?->break_end }}" placeholder="Break end" class="w-full px-2 py-1.5 border border-gray-300 rounded text-sm"></div>
                    <div class="text-xs text-gray-400">Check = Closed</div>
                </div>
                @endfor
            </div>
            <button type="submit" class="mt-4 bg-blue-600 text-white px-6 py-2 rounded-lg text-sm hover:bg-blue-700">Save Working Hours</button>
        </form>
    </div>

    {{-- Blocked Dates --}}
    <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6">
        <h3 class="font-semibold text-gray-800 mb-4">Blocked Dates</h3>
        <form method="POST" action="{{ route('admin.schedules.blocked-dates.store') }}" class="flex flex-wrap gap-3 mb-4">
            @csrf
            <input type="hidden" name="user_id" value="{{ $selectedUserId }}">
            <input type="date" name="blocked_date" required min="{{ date('Y-m-d') }}" class="px-3 py-2 border border-gray-300 rounded-lg text-sm">
            <input type="text" name="reason" placeholder="Reason (optional)" class="px-3 py-2 border border-gray-300 rounded-lg text-sm">
            <button type="submit" class="bg-red-600 text-white px-4 py-2 rounded-lg text-sm">Block Date</button>
        </form>

        <div class="space-y-2">
            @forelse($blockedDates as $bd)
            <div class="flex justify-between items-center p-3 bg-red-50 rounded-lg">
                <div>
                    <span class="text-sm font-medium text-gray-800">{{ $bd->blocked_date->format('d M Y') }}</span>
                    @if($bd->reason)<span class="text-sm text-gray-500 ml-2">- {{ $bd->reason }}</span>@endif
                </div>
                <form method="POST" action="{{ route('admin.schedules.blocked-dates.destroy', $bd) }}">
                    @csrf @method('DELETE')
                    <button type="submit" class="text-red-600 hover:text-red-800 text-sm">Remove</button>
                </form>
            </div>
            @empty
            <p class="text-sm text-gray-400">No blocked dates.</p>
            @endforelse
        </div>
    </div>
</div>
@endsection
