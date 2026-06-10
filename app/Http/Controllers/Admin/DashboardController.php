<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Booking;
use App\Models\Patient;
use App\Models\Service;
use Carbon\Carbon;

class DashboardController extends Controller
{
    public function index()
    {
        $today = Carbon::today();
        $weekStart = Carbon::now()->startOfWeek();
        $weekEnd = Carbon::now()->endOfWeek();
        $monthStart = Carbon::now()->startOfMonth();
        $monthEnd = Carbon::now()->endOfMonth();
        $user = auth()->user();

        // Doctor sees only their own bookings
        $scope = fn () => $user->role === 'doctor' ? Booking::where('staff_id', $user->id) : Booking::query();

        $stats = [
            'today' => $scope()->whereDate('booking_date', $today)->count(),
            'this_week' => $scope()->whereBetween('booking_date', [$weekStart, $weekEnd])->count(),
            'pending' => $scope()->where('status', 'pending')->count(),
            'confirmed' => $scope()->where('status', 'confirmed')->count(),
            'completed' => $scope()->where('status', 'completed')->count(),
            'cancelled' => $scope()->where('status', 'cancelled')->count(),
            'revenue_month' => $scope()->where('payment_status', 'paid')
                ->whereBetween('booking_date', [$monthStart, $monthEnd])
                ->sum('total_amount'),
        ];

        $upcomingQuery = Booking::with(['patient', 'service', 'staffMember'])
            ->where('booking_date', '>=', $today)
            ->whereIn('status', ['pending', 'confirmed'])
            ->orderBy('booking_date')
            ->orderBy('start_time')
            ->limit(10);

        if ($user->role === 'doctor') {
            $upcomingQuery->where('staff_id', $user->id);
        }

        $upcomingBookings = $upcomingQuery->get();

        $upcomingBookingsJson = $upcomingBookings->map(function ($b) {
            return [
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
            ];
        });

        $statusQuery = $scope();
        $bookingsByStatus = $statusQuery->selectRaw('status, COUNT(*) as count')
            ->groupBy('status')
            ->pluck('count', 'status');

        $serviceQuery = $scope();
        $bookingsByService = $serviceQuery->join('services', 'bookings.service_id', '=', 'services.id')
            ->selectRaw('services.name, COUNT(*) as count')
            ->groupBy('services.name')
            ->pluck('count', 'name');

        return view('admin.dashboard', compact(
            'stats', 'upcomingBookings', 'upcomingBookingsJson', 'bookingsByStatus', 'bookingsByService'
        ));
    }
}
