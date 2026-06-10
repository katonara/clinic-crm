<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\WorkingHour;
use App\Models\BlockedDate;
use App\Models\User;
use Illuminate\Http\Request;

class ScheduleController extends Controller
{
    public function index(Request $request)
    {
        $staffMembers = User::whereIn('role', ['staff', 'doctor'])->where('status', 'active')->get();
        $selectedUserId = $request->get('user_id');

        // Clinic hours (user_id = null) or staff hours
        $workingHours = WorkingHour::where('user_id', $selectedUserId)
            ->orderBy('day_of_week')
            ->get()
            ->keyBy('day_of_week');

        $blockedDates = BlockedDate::where('user_id', $selectedUserId)
            ->orderBy('blocked_date')
            ->get();

        return view('admin.schedules.index', compact('staffMembers', 'selectedUserId', 'workingHours', 'blockedDates'));
    }

    public function updateWorkingHours(Request $request)
    {
        $request->validate([
            'user_id' => 'nullable|exists:users,id',
            'hours' => 'required|array',
            'hours.*.opening_time' => 'required|date_format:H:i',
            'hours.*.closing_time' => 'required|date_format:H:i',
            'hours.*.break_start' => 'nullable|date_format:H:i',
            'hours.*.break_end' => 'nullable|date_format:H:i',
        ]);

        $userId = $request->user_id;

        foreach ($request->hours as $day => $data) {
            WorkingHour::updateOrCreate(
                ['user_id' => $userId, 'day_of_week' => $day],
                [
                    'opening_time' => $data['opening_time'],
                    'closing_time' => $data['closing_time'],
                    'break_start' => $data['break_start'] ?? null,
                    'break_end' => $data['break_end'] ?? null,
                    'is_closed' => isset($data['is_closed']),
                ]
            );
        }

        return back()->with('success', 'Working hours updated successfully.');
    }

    public function storeBlockedDate(Request $request)
    {
        $request->validate([
            'user_id' => 'nullable|exists:users,id',
            'blocked_date' => 'required|date|after_or_equal:today',
            'reason' => 'nullable|string|max:255',
        ]);

        BlockedDate::create($request->only('user_id', 'blocked_date', 'reason'));

        return back()->with('success', 'Blocked date added.');
    }

    public function destroyBlockedDate(BlockedDate $blockedDate)
    {
        $blockedDate->delete();

        return back()->with('success', 'Blocked date removed.');
    }
}
