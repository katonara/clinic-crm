<?php

namespace App\Http\Controllers\Customer;

use App\Http\Controllers\Controller;
use App\Models\Booking;
use App\Models\Patient;
use App\Models\PatientPackage;
use Carbon\Carbon;
use Illuminate\Http\JsonResponse;

class CustomerLiveController extends Controller
{
    public function dashboard(): JsonResponse
    {
        $patient = Patient::where('user_id', auth()->id())->first();

        if (!$patient) {
            return response()->json([
                'upcoming' => [],
                'packages' => [],
                'notifications' => [],
            ]);
        }

        $upcoming = Booking::with(['service', 'staffMember'])
            ->where('patient_id', $patient->id)
            ->where('booking_date', '>=', Carbon::today())
            ->whereIn('status', ['pending', 'confirmed', 'rescheduled'])
            ->orderBy('booking_date')
            ->orderBy('start_time')
            ->get()
            ->map(fn ($b) => [
                'id' => $b->id,
                'service_name' => $b->service->name,
                'date' => $b->booking_date->format('D, d M Y'),
                'time' => $b->start_time,
                'staff_name' => $b->staffMember?->name ?? 'Staff TBD',
                'status' => $b->status,
                'status_label' => $b->status === 'pending' ? 'Pending Review' : ucfirst($b->status),
                'status_color' => $b->statusBadgeColor(),
                'can_cancel' => in_array($b->status, ['pending', 'confirmed']),
            ]);

        $packages = PatientPackage::with('service')
            ->where('patient_id', $patient->id)
            ->where('status', 'active')
            ->get()
            ->map(fn ($p) => [
                'id' => $p->id,
                'name' => $p->package_name,
                'service' => $p->service?->name,
                'used' => $p->used_sessions,
                'total' => $p->total_sessions,
                'remaining' => $p->remaining_sessions,
                'progress' => $p->total_sessions > 0 ? round(($p->used_sessions / $p->total_sessions) * 100) : 0,
            ]);

        return response()->json([
            'upcoming' => $upcoming,
            'packages' => $packages,
        ]);
    }

    public function notifications(): JsonResponse
    {
        $patient = Patient::where('user_id', auth()->id())->first();

        if (!$patient) {
            return response()->json(['notifications' => [], 'unread_count' => 0]);
        }

        // Recent status changes (bookings updated in last 24h)
        $recentChanges = Booking::with('service')
            ->where('patient_id', $patient->id)
            ->where('updated_at', '>=', now()->subDay())
            ->whereIn('status', ['confirmed', 'cancelled', 'completed', 'rescheduled'])
            ->orderByDesc('updated_at')
            ->limit(5)
            ->get()
            ->map(fn ($b) => [
                'id' => $b->id,
                'message' => $this->statusMessage($b),
                'status' => $b->status,
                'status_color' => $b->statusBadgeColor(),
                'time' => $b->updated_at->diffForHumans(),
            ]);

        $pendingCount = Booking::where('patient_id', $patient->id)
            ->where('status', 'pending')
            ->count();

        return response()->json([
            'notifications' => $recentChanges,
            'pending_count' => $pendingCount,
        ]);
    }

    private function statusMessage(Booking $booking): string
    {
        return match ($booking->status) {
            'confirmed' => 'Your booking for ' . $booking->service->name . ' has been approved!',
            'cancelled' => 'Your booking for ' . $booking->service->name . ' was cancelled.',
            'completed' => 'Your ' . $booking->service->name . ' session is completed.',
            'rescheduled' => 'Your ' . $booking->service->name . ' booking is being rescheduled.',
            default => 'Booking for ' . $booking->service->name . ' updated.',
        };
    }
}
