<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\BookingRequest;
use App\Models\Booking;
use App\Models\Patient;
use App\Models\Service;
use App\Models\User;
use App\Models\WorkingHour;
use App\Models\BlockedDate;
use Carbon\Carbon;
use Illuminate\Http\Request;

class BookingController extends Controller
{
    public function index(Request $request)
    {
        $query = Booking::with(['patient', 'service', 'staffMember']);

        if ($request->filled('date')) {
            $query->whereDate('booking_date', $request->date);
        }
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }
        if ($request->filled('service_id')) {
            $query->where('service_id', $request->service_id);
        }
        if ($request->filled('staff_id')) {
            $query->where('staff_id', $request->staff_id);
        }
        if ($request->filled('payment_status')) {
            $query->where('payment_status', $request->payment_status);
        }
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('booking_number', 'like', "%{$search}%")
                  ->orWhereHas('patient', fn($q2) => $q2->where('name', 'like', "%{$search}%"));
            });
        }

        $bookings = $query->orderByDesc('booking_date')->orderByDesc('start_time')->paginate(15)->withQueryString();
        $services = Service::all();
        $staffMembers = User::whereIn('role', ['staff', 'doctor'])->get();

        return view('admin.bookings.index', compact('bookings', 'services', 'staffMembers'));
    }

    public function create()
    {
        $patients = Patient::orderBy('name')->get();
        $services = Service::active()->get();
        $staffMembers = User::whereIn('role', ['staff', 'doctor'])->where('status', 'active')->get();

        return view('admin.bookings.create', compact('patients', 'services', 'staffMembers'));
    }

    public function store(BookingRequest $request)
    {
        $service = Service::findOrFail($request->service_id);
        $startTime = Carbon::createFromFormat('H:i', $request->start_time);
        $endTime = $startTime->copy()->addMinutes($service->duration_minutes);

        // Check for double booking
        if ($request->staff_id) {
            $overlap = Booking::where('staff_id', $request->staff_id)
                ->where('booking_date', $request->booking_date)
                ->whereIn('status', ['pending', 'confirmed'])
                ->where(function ($q) use ($request, $endTime) {
                    $q->where(function ($q2) use ($request, $endTime) {
                        $q2->where('start_time', '<', $endTime->format('H:i'))
                            ->where('end_time', '>', $request->start_time);
                    });
                })->exists();

            if ($overlap) {
                return back()->withInput()->withErrors(['start_time' => 'This time slot is already booked for the selected staff.']);
            }
        }

        // Check blocked dates
        $blockedDate = BlockedDate::where('blocked_date', $request->booking_date)
            ->where(function ($q) use ($request) {
                $q->whereNull('user_id')->orWhere('user_id', $request->staff_id);
            })->exists();

        if ($blockedDate) {
            return back()->withInput()->withErrors(['booking_date' => 'This date is blocked.']);
        }

        Booking::create([
            'booking_number' => Booking::generateBookingNumber($request->booking_date),
            'patient_id' => $request->patient_id,
            'service_id' => $request->service_id,
            'staff_id' => $request->staff_id,
            'booking_date' => $request->booking_date,
            'start_time' => $request->start_time,
            'end_time' => $endTime->format('H:i'),
            'total_amount' => $service->price,
            'deposit_amount' => $request->deposit_amount,
            'payment_method' => $request->payment_method,
            'customer_note' => $request->customer_note,
            'internal_note' => $request->internal_note,
        ]);

        return redirect()->route('admin.bookings.index')
            ->with('success', 'Booking created successfully.');
    }

    public function show(Booking $booking)
    {
        $booking->load(['patient', 'service', 'staffMember', 'followUps.assignedUser']);

        return view('admin.bookings.show', compact('booking'));
    }

    public function edit(Booking $booking)
    {
        $patients = Patient::orderBy('name')->get();
        $services = Service::active()->get();
        $staffMembers = User::whereIn('role', ['staff', 'doctor'])->where('status', 'active')->get();

        return view('admin.bookings.edit', compact('booking', 'patients', 'services', 'staffMembers'));
    }

    public function update(BookingRequest $request, Booking $booking)
    {
        $service = Service::findOrFail($request->service_id);
        $startTime = Carbon::createFromFormat('H:i', $request->start_time);
        $endTime = $startTime->copy()->addMinutes($service->duration_minutes);

        // Check for double booking (exclude current)
        if ($request->staff_id) {
            $overlap = Booking::where('staff_id', $request->staff_id)
                ->where('booking_date', $request->booking_date)
                ->where('id', '!=', $booking->id)
                ->whereIn('status', ['pending', 'confirmed'])
                ->where(function ($q) use ($request, $endTime) {
                    $q->where('start_time', '<', $endTime->format('H:i'))
                      ->where('end_time', '>', $request->start_time);
                })->exists();

            if ($overlap) {
                return back()->withInput()->withErrors(['start_time' => 'This time slot is already booked for the selected staff.']);
            }
        }

        $booking->update([
            'patient_id' => $request->patient_id,
            'service_id' => $request->service_id,
            'staff_id' => $request->staff_id,
            'booking_date' => $request->booking_date,
            'start_time' => $request->start_time,
            'end_time' => $endTime->format('H:i'),
            'total_amount' => $service->price,
            'deposit_amount' => $request->deposit_amount,
            'payment_method' => $request->payment_method,
            'customer_note' => $request->customer_note,
            'internal_note' => $request->internal_note,
        ]);

        return redirect()->route('admin.bookings.show', $booking)
            ->with('success', 'Booking updated successfully.');
    }

    public function updateStatus(Request $request, Booking $booking)
    {
        $request->validate([
            'status' => 'required|in:pending,confirmed,rescheduled,completed,cancelled,no_show',
            'cancellation_reason' => 'nullable|string|max:500',
            'reschedule_reason' => 'nullable|string|max:500',
            'reason' => 'nullable|string|max:500',
        ]);

        $data = ['status' => $request->status];

        if ($request->status === 'cancelled') {
            $data['cancellation_reason'] = $request->cancellation_reason ?? $request->reason;
        }
        if ($request->status === 'rescheduled') {
            $data['reschedule_reason'] = $request->reschedule_reason;
        }

        $booking->update($data);

        if ($request->expectsJson()) {
            return response()->json(['success' => true, 'status' => $booking->status]);
        }

        return back()->with('success', 'Booking status updated.');
    }

    public function updatePayment(Request $request, Booking $booking)
    {
        $request->validate([
            'payment_status' => 'required|in:unpaid,deposit_paid,paid,refunded',
            'payment_method' => 'nullable|string|max:50',
        ]);

        $booking->update([
            'payment_status' => $request->payment_status,
            'payment_method' => $request->payment_method,
        ]);

        return back()->with('success', 'Payment status updated.');
    }

    public function destroy(Booking $booking)
    {
        $booking->delete();

        return redirect()->route('admin.bookings.index')
            ->with('success', 'Booking deleted successfully.');
    }
}
