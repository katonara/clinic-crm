<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\FollowUpRequest;
use App\Models\FollowUp;
use App\Models\Patient;
use App\Models\Booking;
use App\Models\User;
use Illuminate\Http\Request;

class FollowUpController extends Controller
{
    public function index(Request $request)
    {
        $query = FollowUp::with(['patient', 'booking.service', 'assignedUser']);

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }
        if ($request->filled('assigned_to')) {
            $query->where('assigned_to', $request->assigned_to);
        }
        if ($request->filled('search')) {
            $search = $request->search;
            $query->whereHas('patient', fn($q) => $q->where('name', 'like', "%{$search}%"));
        }

        $followUps = $query->orderBy('follow_up_date')->paginate(15)->withQueryString();
        $staffMembers = User::whereIn('role', ['admin', 'staff'])->get();

        return view('admin.follow-ups.index', compact('followUps', 'staffMembers'));
    }

    public function create(Request $request)
    {
        $patients = Patient::orderBy('name')->get();
        $staffMembers = User::whereIn('role', ['admin', 'staff'])->get();
        $bookings = Booking::orderByDesc('booking_date')->get();
        $selectedPatientId = $request->get('patient_id');
        $selectedBookingId = $request->get('booking_id');

        return view('admin.follow-ups.create', compact('patients', 'staffMembers', 'bookings', 'selectedPatientId', 'selectedBookingId'));
    }

    public function store(FollowUpRequest $request)
    {
        FollowUp::create($request->validated());

        return redirect()->route('admin.follow-ups.index')
            ->with('success', 'Follow-up created successfully.');
    }

    public function edit(FollowUp $followUp)
    {
        $patients = Patient::orderBy('name')->get();
        $staffMembers = User::whereIn('role', ['admin', 'staff'])->get();
        $bookings = Booking::orderByDesc('booking_date')->get();

        return view('admin.follow-ups.edit', compact('followUp', 'patients', 'staffMembers', 'bookings'));
    }

    public function update(FollowUpRequest $request, FollowUp $followUp)
    {
        $followUp->update($request->validated());

        return redirect()->route('admin.follow-ups.index')
            ->with('success', 'Follow-up updated successfully.');
    }

    public function destroy(FollowUp $followUp)
    {
        $followUp->delete();

        return redirect()->route('admin.follow-ups.index')
            ->with('success', 'Follow-up deleted successfully.');
    }
}
