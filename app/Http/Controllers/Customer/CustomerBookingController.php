<?php

namespace App\Http\Controllers\Customer;

use App\Http\Controllers\Controller;
use App\Models\Booking;
use App\Models\Patient;
use App\Models\Service;
use App\Models\User;
use App\Models\BlockedDate;
use Carbon\Carbon;
use Illuminate\Http\Request;

class CustomerBookingController extends Controller
{
    public function create()
    {
        $services = Service::active()->get();
        $staffMembers = User::whereIn('role', ['staff', 'doctor'])->where('status', 'active')->get();

        return view('customer.bookings.create', compact('services', 'staffMembers'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'service_id' => 'required|exists:services,id',
            'staff_id' => 'nullable|exists:users,id',
            'booking_date' => 'required|date|after_or_equal:today',
            'start_time' => 'required|date_format:H:i',
            'customer_note' => 'nullable|string|max:1000',
        ]);

        $patient = Patient::where('user_id', auth()->id())->firstOrFail();
        $service = Service::findOrFail($request->service_id);
        $startTime = Carbon::createFromFormat('H:i', $request->start_time);
        $endTime = $startTime->copy()->addMinutes($service->duration_minutes);

        // Check blocked dates
        $blocked = BlockedDate::where('blocked_date', $request->booking_date)
            ->where(function ($q) use ($request) {
                $q->whereNull('user_id')->orWhere('user_id', $request->staff_id);
            })->exists();

        if ($blocked) {
            return back()->withInput()->withErrors(['booking_date' => 'This date is not available.']);
        }

        // Check double booking
        if ($request->staff_id) {
            $overlap = Booking::where('staff_id', $request->staff_id)
                ->where('booking_date', $request->booking_date)
                ->whereIn('status', ['pending', 'confirmed'])
                ->where('start_time', '<', $endTime->format('H:i'))
                ->where('end_time', '>', $request->start_time)
                ->exists();

            if ($overlap) {
                return back()->withInput()->withErrors(['start_time' => 'This time slot is not available.']);
            }
        }

        Booking::create([
            'booking_number' => Booking::generateBookingNumber($request->booking_date),
            'patient_id' => $patient->id,
            'user_id' => auth()->id(),
            'service_id' => $request->service_id,
            'staff_id' => $request->staff_id,
            'booking_date' => $request->booking_date,
            'start_time' => $request->start_time,
            'end_time' => $endTime->format('H:i'),
            'total_amount' => $service->price,
            'customer_note' => $request->customer_note,
        ]);

        return redirect()->route('customer.dashboard')
            ->with('success', 'Booking submitted successfully!');
    }

    public function history()
    {
        $patient = Patient::where('user_id', auth()->id())->first();

        $bookings = collect();
        if ($patient) {
            $bookings = Booking::with(['service', 'staffMember'])
                ->where('patient_id', $patient->id)
                ->orderByDesc('booking_date')
                ->paginate(15);
        }

        return view('customer.bookings.history', compact('bookings'));
    }

    public function cancel(Booking $booking)
    {
        $patient = Patient::where('user_id', auth()->id())->first();

        if (!$patient || $booking->patient_id !== $patient->id) {
            abort(403);
        }

        if (!in_array($booking->status, ['pending', 'confirmed'])) {
            return back()->withErrors(['error' => 'This booking cannot be cancelled.']);
        }

        $booking->update(['status' => 'cancelled', 'cancellation_reason' => 'Cancelled by customer']);

        return back()->with('success', 'Booking cancelled successfully.');
    }

    public function requestReschedule(Request $request, Booking $booking)
    {
        $patient = Patient::where('user_id', auth()->id())->first();

        if (!$patient || $booking->patient_id !== $patient->id) {
            abort(403);
        }

        if (!in_array($booking->status, ['pending', 'confirmed'])) {
            return back()->withErrors(['error' => 'This booking cannot be rescheduled.']);
        }

        $booking->update([
            'status' => 'rescheduled',
            'reschedule_reason' => $request->input('reason', 'Reschedule requested by customer'),
        ]);

        return back()->with('success', 'Reschedule request submitted.');
    }
}
