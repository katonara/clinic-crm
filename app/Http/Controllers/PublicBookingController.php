<?php

namespace App\Http\Controllers;

use App\Models\Booking;
use App\Models\Patient;
use App\Models\Service;
use App\Models\User;
use App\Models\BlockedDate;
use App\Models\ClinicSetting;
use Carbon\Carbon;
use Illuminate\Http\Request;

class PublicBookingController extends Controller
{
    public function index()
    {
        $services = Service::active()->get();
        $staffMembers = User::whereIn('role', ['staff', 'doctor'])->where('status', 'active')->get();
        $clinic = ClinicSetting::instance();

        return view('public.booking', compact('services', 'staffMembers', 'clinic'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'nullable|email|max:255',
            'country_code' => 'required|string|max:10',
            'whatsapp_number' => 'required|string|max:20',
            'service_id' => 'required|exists:services,id',
            'staff_id' => 'nullable|exists:users,id',
            'booking_date' => 'required|date|after_or_equal:today',
            'start_time' => 'required|date_format:H:i',
            'customer_note' => 'nullable|string|max:1000',
        ]);

        $patient = Patient::firstOrCreate(
            ['whatsapp_number' => $request->whatsapp_number, 'country_code' => $request->country_code],
            ['name' => $request->name, 'email' => $request->email]
        );

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
            'service_id' => $request->service_id,
            'staff_id' => $request->staff_id,
            'booking_date' => $request->booking_date,
            'start_time' => $request->start_time,
            'end_time' => $endTime->format('H:i'),
            'total_amount' => $service->price,
            'customer_note' => $request->customer_note,
        ]);

        return redirect()->route('public.booking')
            ->with('success', 'Your booking has been submitted! We will confirm your appointment shortly.');
    }
}
