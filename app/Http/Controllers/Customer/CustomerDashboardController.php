<?php

namespace App\Http\Controllers\Customer;

use App\Http\Controllers\Controller;
use App\Models\Booking;
use App\Models\Patient;
use Carbon\Carbon;

class CustomerDashboardController extends Controller
{
    public function index()
    {
        $patient = Patient::where('user_id', auth()->id())->first();

        $upcomingBookings = collect();
        $pastBookings = collect();

        if ($patient) {
            $upcomingBookings = Booking::with(['service', 'staffMember'])
                ->where('patient_id', $patient->id)
                ->where('booking_date', '>=', Carbon::today())
                ->whereIn('status', ['pending', 'confirmed', 'rescheduled'])
                ->orderBy('booking_date')
                ->orderBy('start_time')
                ->get();

            $pastBookings = Booking::with(['service', 'staffMember'])
                ->where('patient_id', $patient->id)
                ->where(function ($q) {
                    $q->where('booking_date', '<', Carbon::today())
                      ->orWhereIn('status', ['completed', 'cancelled', 'no_show']);
                })
                ->orderByDesc('booking_date')
                ->limit(10)
                ->get();
        }

        $upcomingBookingsJson = $upcomingBookings->map(function ($b) {
            return [
                'id' => $b->id,
                'service_name' => $b->service->name,
                'date' => $b->booking_date->format('D, d M Y'),
                'time' => $b->start_time,
                'staff_name' => $b->staffMember?->name ?? 'Staff TBD',
                'status' => $b->status,
                'status_label' => $b->status === 'pending' ? 'Pending Review' : ucfirst($b->status),
                'status_color' => $b->statusBadgeColor(),
                'can_cancel' => in_array($b->status, ['pending', 'confirmed']),
            ];
        });

        return view('customer.dashboard', compact('upcomingBookings', 'upcomingBookingsJson', 'pastBookings', 'patient'));
    }
}
