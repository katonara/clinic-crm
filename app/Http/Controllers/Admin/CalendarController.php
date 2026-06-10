<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Booking;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Http\Request;

class CalendarController extends Controller
{
    public function index(Request $request)
    {
        $staffMembers = User::whereIn('role', ['staff', 'doctor'])->where('status', 'active')->get();

        return view('admin.calendar.index', compact('staffMembers'));
    }

    public function events(Request $request)
    {
        $query = Booking::with(['patient', 'service', 'staffMember']);

        if ($request->filled('start') && $request->filled('end')) {
            $query->whereBetween('booking_date', [$request->start, $request->end]);
        }

        if ($request->filled('staff_id')) {
            $query->where('staff_id', $request->staff_id);
        }

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        $bookings = $query->get();

        $events = $bookings->map(function ($booking) {
            $colors = [
                'pending' => '#EAB308',
                'confirmed' => '#3B82F6',
                'completed' => '#22C55E',
                'cancelled' => '#EF4444',
                'rescheduled' => '#A855F7',
                'no_show' => '#6B7280',
            ];

            return [
                'id' => $booking->id,
                'title' => $booking->patient->name . ' - ' . $booking->service->name,
                'start' => $booking->booking_date->format('Y-m-d') . 'T' . $booking->start_time,
                'end' => $booking->booking_date->format('Y-m-d') . 'T' . $booking->end_time,
                'color' => $colors[$booking->status] ?? '#6B7280',
                'extendedProps' => [
                    'patient' => $booking->patient->name,
                    'service' => $booking->service->name,
                    'staff' => $booking->staffMember?->name ?? 'Unassigned',
                    'status' => $booking->status,
                    'booking_number' => $booking->booking_number,
                ],
            ];
        });

        return response()->json($events);
    }
}
