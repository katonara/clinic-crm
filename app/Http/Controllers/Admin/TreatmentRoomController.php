<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\TreatmentRoomRequest;
use App\Models\Booking;
use App\Models\Doctor;
use App\Models\RoomAssignment;
use App\Models\TreatmentRoom;
use Carbon\Carbon;
use Illuminate\Http\Request;

class TreatmentRoomController extends Controller
{
    public function index()
    {
        $rooms = TreatmentRoom::withCount(['assignments' => fn($q) =>
            $q->where('assigned_date', today())->whereIn('status', ['scheduled', 'occupied', 'ending_soon'])
        ])->orderBy('name')->paginate(15);

        return view('admin.treatment-rooms.index', compact('rooms'));
    }

    public function create()
    {
        return view('admin.treatment-rooms.create');
    }

    public function store(TreatmentRoomRequest $request)
    {
        TreatmentRoom::create($request->validated());

        return redirect()->route('admin.treatment-rooms.index')
            ->with('success', 'Treatment room created successfully.');
    }

    public function edit(TreatmentRoom $treatmentRoom)
    {
        return view('admin.treatment-rooms.edit', compact('treatmentRoom'));
    }

    public function update(TreatmentRoomRequest $request, TreatmentRoom $treatmentRoom)
    {
        $treatmentRoom->update($request->validated());

        return redirect()->route('admin.treatment-rooms.index')
            ->with('success', 'Treatment room updated successfully.');
    }

    public function destroy(TreatmentRoom $treatmentRoom)
    {
        $activeAssignments = $treatmentRoom->assignments()
            ->whereIn('status', ['scheduled', 'occupied', 'ending_soon'])
            ->exists();

        if ($activeAssignments) {
            return back()->with('error', 'Cannot delete room with active assignments.');
        }

        $treatmentRoom->delete();

        return redirect()->route('admin.treatment-rooms.index')
            ->with('success', 'Treatment room deleted successfully.');
    }

    // Room board view showing all rooms with live status
    public function board(Request $request)
    {
        $date = $request->get('date', today()->toDateString());

        $rooms = TreatmentRoom::where('status', '!=', 'closed')
            ->with(['assignments' => fn($q) => $q
                ->where('assigned_date', $date)
                ->with(['doctor', 'patient', 'booking.service'])
                ->orderBy('start_time')
            ])
            ->orderBy('name')
            ->get();

        $doctors = Doctor::active()->with('user')->get();

        // Unassigned bookings for the date (no room assignment yet)
        $unassignedBookings = Booking::with(['patient', 'service', 'staffMember'])
            ->where('booking_date', $date)
            ->whereIn('status', ['pending', 'confirmed'])
            ->whereDoesntHave('roomAssignment')
            ->orderBy('start_time')
            ->get();

        return view('admin.treatment-rooms.board', compact('rooms', 'doctors', 'unassignedBookings', 'date'));
    }

    // API: assign booking to room (supports drag and drop)
    public function assignBooking(Request $request)
    {
        $request->validate([
            'treatment_room_id' => 'required|exists:treatment_rooms,id',
            'booking_id' => 'required|exists:bookings,id',
        ]);

        $booking = Booking::with('service', 'patient')->findOrFail($request->booking_id);
        $room = TreatmentRoom::findOrFail($request->treatment_room_id);

        // Check for room double booking
        $overlap = RoomAssignment::where('treatment_room_id', $room->id)
            ->where('assigned_date', $booking->booking_date)
            ->whereIn('status', ['scheduled', 'occupied', 'ending_soon'])
            ->where('start_time', '<', $booking->end_time)
            ->where('end_time', '>', $booking->start_time)
            ->exists();

        if ($overlap) {
            return response()->json(['error' => 'Room is already occupied at this time.'], 422);
        }

        // Remove existing room assignment if any
        RoomAssignment::where('booking_id', $booking->id)->delete();

        RoomAssignment::create([
            'treatment_room_id' => $room->id,
            'booking_id' => $booking->id,
            'doctor_id' => $booking->staff_id,
            'patient_id' => $booking->patient_id,
            'assigned_date' => $booking->booking_date,
            'start_time' => $booking->start_time,
            'end_time' => $booking->end_time,
            'status' => 'scheduled',
        ]);

        return response()->json(['success' => true]);
    }

    // API: move assignment between rooms
    public function moveAssignment(Request $request)
    {
        $request->validate([
            'assignment_id' => 'required|exists:room_assignments,id',
            'new_room_id' => 'required|exists:treatment_rooms,id',
        ]);

        $assignment = RoomAssignment::findOrFail($request->assignment_id);

        // Check overlap in new room
        $overlap = RoomAssignment::where('treatment_room_id', $request->new_room_id)
            ->where('assigned_date', $assignment->assigned_date)
            ->where('id', '!=', $assignment->id)
            ->whereIn('status', ['scheduled', 'occupied', 'ending_soon'])
            ->where('start_time', '<', $assignment->end_time)
            ->where('end_time', '>', $assignment->start_time)
            ->exists();

        if ($overlap) {
            return response()->json(['error' => 'Target room is occupied at this time.'], 422);
        }

        $assignment->update(['treatment_room_id' => $request->new_room_id]);

        return response()->json(['success' => true]);
    }

    // API: get room status data for board refresh
    public function boardData(Request $request)
    {
        $date = $request->get('date', today()->toDateString());

        $rooms = TreatmentRoom::where('status', '!=', 'closed')
            ->with(['assignments' => fn($q) => $q
                ->where('assigned_date', $date)
                ->with(['doctor', 'patient', 'booking.service'])
                ->orderBy('start_time')
            ])
            ->orderBy('name')
            ->get()
            ->map(function ($room) use ($date) {
                $liveStatus = $room->liveStatus($date);
                $currentAssignment = $room->assignments
                    ->first(fn($a) => $a->start_time <= now()->format('H:i:s') && $a->end_time > now()->format('H:i:s'));

                return [
                    'id' => $room->id,
                    'name' => $room->name,
                    'room_code' => $room->room_code,
                    'live_status' => $liveStatus,
                    'status_color' => TreatmentRoom::statusColor($liveStatus),
                    'assignments' => $room->assignments,
                    'current' => $currentAssignment,
                ];
            });

        return response()->json($rooms);
    }
}
