<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Booking;
use App\Models\Patient;
use App\Models\RoomAssignment;
use App\Models\TreatmentRoom;
use App\Models\PatientPackage;
use Carbon\Carbon;
use Illuminate\Http\JsonResponse;

class LiveUpdateController extends Controller
{
    public function stats(): JsonResponse
    {
        $today = Carbon::today();
        $monthStart = Carbon::now()->startOfMonth();
        $monthEnd = Carbon::now()->endOfMonth();
        $weekStart = Carbon::now()->startOfWeek();
        $weekEnd = Carbon::now()->endOfWeek();

        $user = auth()->user();

        $baseQuery = Booking::query();

        // Doctor sees only their own bookings
        if ($user->role === 'doctor') {
            $baseQuery->where('staff_id', $user->id);
        }

        $stats = [
            'today' => (clone $baseQuery)->whereDate('booking_date', $today)->count(),
            'this_week' => (clone $baseQuery)->whereBetween('booking_date', [$weekStart, $weekEnd])->count(),
            'pending' => (clone $baseQuery)->where('status', 'pending')->count(),
            'confirmed' => (clone $baseQuery)->where('status', 'confirmed')->count(),
            'completed' => (clone $baseQuery)->where('status', 'completed')->count(),
            'cancelled' => (clone $baseQuery)->where('status', 'cancelled')->count(),
            'revenue_month' => (clone $baseQuery)->where('payment_status', 'paid')
                ->whereBetween('booking_date', [$monthStart, $monthEnd])
                ->sum('total_amount'),
        ];

        return response()->json($stats);
    }

    public function upcomingBookings(): JsonResponse
    {
        $user = auth()->user();
        $today = Carbon::today();

        $query = Booking::with(['patient', 'service', 'staffMember'])
            ->where('booking_date', '>=', $today)
            ->whereIn('status', ['pending', 'confirmed'])
            ->orderBy('booking_date')
            ->orderBy('start_time')
            ->limit(10);

        if ($user->role === 'doctor') {
            $query->where('staff_id', $user->id);
        }

        $bookings = $query->get()->map(fn ($b) => [
            'id' => $b->id,
            'booking_number' => $b->booking_number,
            'patient_name' => $b->patient->name,
            'service_name' => $b->service->name,
            'date' => $b->booking_date->format('d M Y'),
            'time' => $b->start_time,
            'staff_name' => $b->staffMember?->name ?? '-',
            'status' => $b->status,
            'status_color' => $b->statusBadgeColor(),
            'url' => route('admin.bookings.show', $b),
        ]);

        return response()->json($bookings);
    }

    public function notifications(): JsonResponse
    {
        $user = auth()->user();

        $pendingCount = Booking::where('status', 'pending')->count();
        $todayCount = Booking::whereDate('booking_date', Carbon::today())
            ->whereIn('status', ['pending', 'confirmed'])
            ->count();

        $recentPending = Booking::with(['patient', 'service'])
            ->where('status', 'pending')
            ->orderByDesc('created_at')
            ->limit(5)
            ->get()
            ->map(fn ($b) => [
                'id' => $b->id,
                'message' => $b->patient->name . ' — ' . $b->service->name,
                'date' => $b->booking_date->format('d M'),
                'time' => $b->start_time,
                'created' => $b->created_at->diffForHumans(),
                'url' => route('admin.bookings.show', $b),
            ]);

        // Doctor-specific: only their assignments
        $roomAlerts = [];
        if (in_array($user->role, ['admin', 'staff'])) {
            $endingSoon = RoomAssignment::with(['treatmentRoom', 'patient'])
                ->where('assigned_date', Carbon::today())
                ->where('status', 'scheduled')
                ->where('start_time', '<=', now()->format('H:i'))
                ->where('end_time', '>=', now()->format('H:i'))
                ->get();

            foreach ($endingSoon as $a) {
                $minutesLeft = now()->diffInMinutes(Carbon::parse($a->end_time), false);
                if ($minutesLeft > 0 && $minutesLeft <= 15) {
                    $roomAlerts[] = [
                        'room' => $a->treatmentRoom->name,
                        'patient' => $a->patient->name,
                        'minutes_left' => $minutesLeft,
                    ];
                }
            }
        }

        return response()->json([
            'pending_count' => $pendingCount,
            'today_count' => $todayCount,
            'recent_pending' => $recentPending,
            'room_alerts' => $roomAlerts,
        ]);
    }

    public function roomStatus(): JsonResponse
    {
        $today = Carbon::today();
        $now = now()->format('H:i');

        $rooms = TreatmentRoom::with(['assignments' => function ($q) use ($today) {
            $q->where('assigned_date', $today)->with(['patient', 'doctor', 'booking.service']);
        }])->get()->map(function ($room) use ($today, $now) {
            $liveStatus = $room->liveStatus($today);
            return [
                'id' => $room->id,
                'name' => $room->name,
                'room_code' => $room->room_code,
                'live_status' => $liveStatus,
                'status_color' => TreatmentRoom::statusColor($liveStatus),
                'assignments' => $room->assignments->map(fn ($a) => [
                    'id' => $a->id,
                    'patient_name' => $a->patient->name,
                    'service_name' => $a->booking->service->name ?? '-',
                    'doctor_name' => $a->doctor?->name,
                    'start_time' => Carbon::parse($a->start_time)->format('h:i A'),
                    'end_time' => Carbon::parse($a->end_time)->format('h:i A'),
                    'status' => $a->status,
                    'status_color' => $a->statusColor(),
                ]),
            ];
        });

        return response()->json($rooms);
    }
}
