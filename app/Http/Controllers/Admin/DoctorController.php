<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\DoctorRequest;
use App\Models\Booking;
use App\Models\Doctor;
use App\Models\RoomAssignment;
use App\Models\Service;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Http\Request;

class DoctorController extends Controller
{
    public function index()
    {
        $doctors = Doctor::with('user')
            ->orderByDesc('created_at')
            ->paginate(15);

        return view('admin.doctors.index', compact('doctors'));
    }

    public function create()
    {
        $services = Service::active()->get();

        return view('admin.doctors.create', compact('services'));
    }

    public function store(DoctorRequest $request)
    {
        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => $request->password,
            'role' => 'doctor',
            'country_code' => $request->country_code,
            'whatsapp_number' => $request->whatsapp_number,
            'status' => $request->status,
            'email_verified_at' => now(),
        ]);

        Doctor::create([
            'user_id' => $user->id,
            'specialty' => $request->specialty,
            'bio' => $request->bio,
            'status' => $request->status,
        ]);

        if ($request->has('service_ids')) {
            $user->services()->sync($request->service_ids);
        }

        return redirect()->route('admin.doctors.index')
            ->with('success', 'Doctor created successfully.');
    }

    public function show(Doctor $doctor)
    {
        $doctor->load(['user.services', 'user.bookingsAsStaff.patient', 'user.bookingsAsStaff.service']);

        return view('admin.doctors.show', compact('doctor'));
    }

    public function edit(Doctor $doctor)
    {
        $services = Service::active()->get();
        $doctor->load('user.services');

        return view('admin.doctors.edit', compact('doctor', 'services'));
    }

    public function update(DoctorRequest $request, Doctor $doctor)
    {
        $data = [
            'name' => $request->name,
            'email' => $request->email,
            'country_code' => $request->country_code,
            'whatsapp_number' => $request->whatsapp_number,
            'status' => $request->status,
        ];

        if ($request->filled('password')) {
            $data['password'] = $request->password;
        }

        $doctor->user->update($data);

        $doctor->update([
            'specialty' => $request->specialty,
            'bio' => $request->bio,
            'status' => $request->status,
        ]);

        if ($request->has('service_ids')) {
            $doctor->user->services()->sync($request->service_ids);
        } else {
            $doctor->user->services()->detach();
        }

        return redirect()->route('admin.doctors.index')
            ->with('success', 'Doctor updated successfully.');
    }

    public function destroy(Doctor $doctor)
    {
        // Prevent deletion if linked to active bookings
        $activeBookings = Booking::where('staff_id', $doctor->user_id)
            ->whereIn('status', ['pending', 'confirmed'])
            ->exists();

        if ($activeBookings) {
            return back()->with('error', 'Cannot delete doctor with active bookings.');
        }

        $doctor->user->delete();

        return redirect()->route('admin.doctors.index')
            ->with('success', 'Doctor deleted successfully.');
    }

    // Doctor slot calendar page
    public function slots(Request $request, Doctor $doctor)
    {
        $date = $request->get('date', now()->toDateString());
        $view = $request->get('view', 'daily');

        return view('admin.doctors.slots', compact('doctor', 'date', 'view'));
    }

    // API endpoint for doctor slot events (JSON)
    public function slotEvents(Request $request, Doctor $doctor)
    {
        $start = $request->get('start', now()->startOfWeek()->toDateString());
        $end = $request->get('end', now()->endOfWeek()->toDateString());

        $bookings = Booking::with(['patient', 'service', 'roomAssignment.treatmentRoom'])
            ->where('staff_id', $doctor->user_id)
            ->whereBetween('booking_date', [$start, $end])
            ->whereIn('status', ['pending', 'confirmed', 'completed'])
            ->get()
            ->map(fn($b) => [
                'id' => $b->id,
                'title' => $b->patient->name . ' - ' . $b->service->name,
                'start' => $b->booking_date->format('Y-m-d') . 'T' . $b->start_time,
                'end' => $b->booking_date->format('Y-m-d') . 'T' . $b->end_time,
                'status' => $b->status,
                'color' => $b->statusBadgeColor(),
                'patient_name' => $b->patient->name,
                'service_name' => $b->service->name,
                'room' => $b->roomAssignment?->treatmentRoom?->name,
            ]);

        return response()->json($bookings);
    }

    // Drag and drop: move booking to new time slot
    public function moveSlot(Request $request, Doctor $doctor)
    {
        $request->validate([
            'booking_id' => 'required|exists:bookings,id',
            'new_date' => 'required|date',
            'new_start_time' => 'required|date_format:H:i',
        ]);

        $booking = Booking::findOrFail($request->booking_id);
        $service = $booking->service;
        $newStart = Carbon::createFromFormat('H:i', $request->new_start_time);
        $newEnd = $newStart->copy()->addMinutes($service->duration_minutes);

        // Prevent double booking
        $overlap = Booking::where('staff_id', $doctor->user_id)
            ->where('booking_date', $request->new_date)
            ->where('id', '!=', $booking->id)
            ->whereIn('status', ['pending', 'confirmed'])
            ->where('start_time', '<', $newEnd->format('H:i'))
            ->where('end_time', '>', $request->new_start_time)
            ->exists();

        if ($overlap) {
            return response()->json(['error' => 'Time slot already booked.'], 422);
        }

        $booking->update([
            'booking_date' => $request->new_date,
            'start_time' => $request->new_start_time,
            'end_time' => $newEnd->format('H:i'),
            'staff_id' => $doctor->user_id,
        ]);

        // Update room assignment if exists
        if ($booking->roomAssignment) {
            $booking->roomAssignment->update([
                'assigned_date' => $request->new_date,
                'start_time' => $request->new_start_time,
                'end_time' => $newEnd->format('H:i'),
            ]);
        }

        return response()->json(['success' => true]);
    }
}
