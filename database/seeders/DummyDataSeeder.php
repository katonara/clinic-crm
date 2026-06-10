<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use App\Models\Doctor;
use App\Models\StaffProfile;
use App\Models\Patient;
use App\Models\Service;
use App\Models\Booking;
use App\Models\FollowUp;
use App\Models\WorkingHour;
use App\Models\BlockedDate;
use App\Models\TreatmentRoom;
use App\Models\RoomAssignment;
use App\Models\PatientPackage;
use App\Models\TreatmentHistory;
use Carbon\Carbon;
use Illuminate\Support\Facades\Hash;

class DummyDataSeeder extends Seeder
{
    public function run(): void
    {
        // ───────────────────────────────────────────
        // 1. Extra staff & doctors
        // ───────────────────────────────────────────
        $staff2 = User::create([
            'name' => 'Nurul Aina',
            'email' => 'nurul@clinic.test',
            'password' => Hash::make('password'),
            'role' => 'staff',
            'country_code' => '+60',
            'whatsapp_number' => '112233445',
            'email_verified_at' => now(),
            'status' => 'active',
        ]);
        StaffProfile::create(['user_id' => $staff2->id, 'position' => 'Nurse', 'bio' => 'Certified aesthetic nurse.', 'status' => 'active']);

        $doctor2 = User::create([
            'name' => 'Dr. Siti Fatimah',
            'email' => 'drsiti@clinic.test',
            'password' => Hash::make('password'),
            'role' => 'doctor',
            'country_code' => '+60',
            'whatsapp_number' => '556677889',
            'email_verified_at' => now(),
            'status' => 'active',
        ]);
        Doctor::create(['user_id' => $doctor2->id, 'specialty' => 'Dermatology', 'bio' => 'Skin specialist with 8 years experience.', 'status' => 'active']);

        $doctor3 = User::create([
            'name' => 'Dr. Tan Wei Ming',
            'email' => 'drtan@clinic.test',
            'password' => Hash::make('password'),
            'role' => 'doctor',
            'country_code' => '+60',
            'whatsapp_number' => '998877665',
            'email_verified_at' => now(),
            'status' => 'active',
        ]);
        Doctor::create(['user_id' => $doctor3->id, 'specialty' => 'Aesthetic Surgery', 'bio' => 'Board-certified aesthetic surgeon.', 'status' => 'active']);

        // Get existing users
        $drAhmad = User::where('email', 'doctor@clinic.test')->first();
        $sarah = User::where('email', 'staff@clinic.test')->first();
        $aliUser = User::where('email', 'customer@clinic.test')->first();

        // Assign services to doctors
        $services = Service::all();
        $drAhmad->services()->sync($services->pluck('id')->take(5)->toArray());
        $doctor2->services()->sync($services->pluck('id')->slice(2, 4)->toArray());
        $doctor3->services()->sync($services->pluck('id')->slice(0, 3)->toArray());
        $sarah->services()->sync($services->pluck('id')->take(3)->toArray());
        $staff2->services()->sync($services->pluck('id')->take(2)->toArray());

        // ───────────────────────────────────────────
        // 2. Extra customer accounts
        // ───────────────────────────────────────────
        $cust2 = User::create([
            'name' => 'Aminah Binti Yusof',
            'email' => 'aminah@clinic.test',
            'password' => Hash::make('password'),
            'role' => 'customer',
            'country_code' => '+60',
            'whatsapp_number' => '171234567',
            'email_verified_at' => now(),
            'status' => 'active',
        ]);

        $cust3 = User::create([
            'name' => 'David Lee',
            'email' => 'david@clinic.test',
            'password' => Hash::make('password'),
            'role' => 'customer',
            'country_code' => '+60',
            'whatsapp_number' => '181234567',
            'email_verified_at' => now(),
            'status' => 'active',
        ]);

        // ───────────────────────────────────────────
        // 3. Patients (linked + walk-in)
        // ───────────────────────────────────────────
        $patientAli = Patient::create(['user_id' => $aliUser->id, 'name' => 'Ali Customer', 'email' => 'customer@clinic.test', 'country_code' => '+60', 'whatsapp_number' => '777888999', 'gender' => 'male', 'date_of_birth' => '1990-05-15', 'notes' => 'Regular customer. Prefers morning slots.', 'tag' => 'VIP']);
        $patientAminah = Patient::create(['user_id' => $cust2->id, 'name' => 'Aminah Binti Yusof', 'email' => 'aminah@clinic.test', 'country_code' => '+60', 'whatsapp_number' => '171234567', 'gender' => 'female', 'date_of_birth' => '1985-11-22', 'notes' => 'Allergic to certain chemical peels.', 'tag' => 'Regular']);
        $patientDavid = Patient::create(['user_id' => $cust3->id, 'name' => 'David Lee', 'email' => 'david@clinic.test', 'country_code' => '+60', 'whatsapp_number' => '181234567', 'gender' => 'male', 'date_of_birth' => '1992-03-08', 'tag' => 'New']);

        // Walk-in patients (no user account)
        $patientRahma = Patient::create(['name' => 'Rahma Abdullah', 'country_code' => '+60', 'whatsapp_number' => '191122334', 'gender' => 'female', 'date_of_birth' => '1988-07-10', 'notes' => 'Walk-in patient. Interested in Botox.', 'tag' => 'Walk-in']);
        $patientJohn = Patient::create(['name' => 'John Tan', 'email' => 'john.tan@gmail.com', 'country_code' => '+60', 'whatsapp_number' => '121234567', 'gender' => 'male', 'date_of_birth' => '1995-01-20', 'tag' => 'Walk-in']);
        $patientSusan = Patient::create(['name' => 'Susan Wong', 'email' => 'susan@email.com', 'country_code' => '+60', 'whatsapp_number' => '131234567', 'gender' => 'female', 'date_of_birth' => '1993-09-30', 'notes' => 'Sensitive skin. Use gentle products only.', 'tag' => 'Regular']);
        $patientKumar = Patient::create(['name' => 'Raj Kumar', 'country_code' => '+60', 'whatsapp_number' => '141234567', 'gender' => 'male', 'date_of_birth' => '1980-12-05', 'tag' => 'VIP']);
        $patientLina = Patient::create(['name' => 'Lina Hashim', 'email' => 'lina.h@email.com', 'country_code' => '+60', 'whatsapp_number' => '151234567', 'gender' => 'female', 'date_of_birth' => '1997-04-18']);

        $allPatients = [$patientAli, $patientAminah, $patientDavid, $patientRahma, $patientJohn, $patientSusan, $patientKumar, $patientLina];
        $allDoctors = [$drAhmad, $doctor2, $doctor3];
        $rooms = TreatmentRoom::all();

        // ───────────────────────────────────────────
        // 4. Working hours (clinic-wide + per doctor)
        // ───────────────────────────────────────────
        $days = [1, 2, 3, 4, 5, 6]; // Mon-Sat
        foreach ($days as $day) {
            // Clinic-wide working hours
            WorkingHour::create([
                'user_id' => null,
                'day_of_week' => $day,
                'opening_time' => '10:00',
                'closing_time' => '18:00',
                'break_start' => '13:00',
                'break_end' => '14:00',
                'is_closed' => false,
            ]);
        }
        // Sunday closed
        WorkingHour::create(['user_id' => null, 'day_of_week' => 0, 'opening_time' => '10:00', 'closing_time' => '18:00', 'is_closed' => true]);

        // Doctor-specific hours
        foreach ($allDoctors as $doc) {
            foreach ([1, 2, 3, 4, 5] as $day) {
                WorkingHour::create([
                    'user_id' => $doc->id,
                    'day_of_week' => $day,
                    'opening_time' => '10:00',
                    'closing_time' => '18:00',
                    'break_start' => '13:00',
                    'break_end' => '14:00',
                    'is_closed' => false,
                ]);
            }
        }

        // ───────────────────────────────────────────
        // 5. Blocked dates
        // ───────────────────────────────────────────
        BlockedDate::create(['user_id' => null, 'blocked_date' => Carbon::now()->addWeeks(2)->next('Sunday'), 'reason' => 'Public Holiday - Hari Raya']);
        BlockedDate::create(['user_id' => $drAhmad->id, 'blocked_date' => Carbon::now()->addDays(5), 'reason' => 'Dr. Ahmad on leave']);
        BlockedDate::create(['user_id' => $doctor2->id, 'blocked_date' => Carbon::now()->addDays(8), 'reason' => 'Dr. Siti attending conference']);

        // ───────────────────────────────────────────
        // 6. Bookings — past, today, future
        // ───────────────────────────────────────────
        $today = Carbon::today();
        $bookings = [];

        // ---- PAST bookings (completed / cancelled / no_show) ----
        $pastData = [
            ['patient' => $patientAli,    'service_idx' => 7, 'doctor' => $drAhmad, 'days_ago' => 30, 'time' => '10:00', 'status' => 'completed', 'payment' => 'paid',         'method' => 'Cash'],
            ['patient' => $patientAli,    'service_idx' => 0, 'doctor' => $drAhmad, 'days_ago' => 20, 'time' => '11:00', 'status' => 'completed', 'payment' => 'paid',         'method' => 'Card'],
            ['patient' => $patientAminah, 'service_idx' => 4, 'doctor' => $doctor2, 'days_ago' => 25, 'time' => '10:00', 'status' => 'completed', 'payment' => 'paid',         'method' => 'Online Transfer'],
            ['patient' => $patientAminah, 'service_idx' => 6, 'doctor' => $doctor2, 'days_ago' => 15, 'time' => '14:00', 'status' => 'completed', 'payment' => 'paid',         'method' => 'Card'],
            ['patient' => $patientDavid,  'service_idx' => 7, 'doctor' => $doctor3, 'days_ago' => 22, 'time' => '10:00', 'status' => 'completed', 'payment' => 'paid',         'method' => 'Cash'],
            ['patient' => $patientRahma,  'service_idx' => 0, 'doctor' => $drAhmad, 'days_ago' => 18, 'time' => '15:00', 'status' => 'completed', 'payment' => 'paid',         'method' => 'Card'],
            ['patient' => $patientJohn,   'service_idx' => 3, 'doctor' => $doctor3, 'days_ago' => 12, 'time' => '11:00', 'status' => 'completed', 'payment' => 'paid',         'method' => 'Cash'],
            ['patient' => $patientSusan,  'service_idx' => 4, 'doctor' => $doctor2, 'days_ago' => 10, 'time' => '10:00', 'status' => 'completed', 'payment' => 'paid',         'method' => 'Online Transfer'],
            ['patient' => $patientKumar,  'service_idx' => 1, 'doctor' => $drAhmad, 'days_ago' => 8,  'time' => '16:00', 'status' => 'completed', 'payment' => 'paid',         'method' => 'Card'],
            ['patient' => $patientLina,   'service_idx' => 2, 'doctor' => $doctor2, 'days_ago' => 7,  'time' => '14:00', 'status' => 'cancelled', 'payment' => 'refunded',     'method' => null],
            ['patient' => $patientJohn,   'service_idx' => 5, 'doctor' => $doctor3, 'days_ago' => 5,  'time' => '10:00', 'status' => 'no_show',   'payment' => 'deposit_paid', 'method' => 'Cash'],
            ['patient' => $patientAli,    'service_idx' => 6, 'doctor' => $drAhmad, 'days_ago' => 3,  'time' => '14:00', 'status' => 'completed', 'payment' => 'paid',         'method' => 'Card'],
        ];

        foreach ($pastData as $p) {
            $service = $services[$p['service_idx']];
            $date = $today->copy()->subDays($p['days_ago']);
            $start = Carbon::createFromFormat('H:i', $p['time']);
            $end = $start->copy()->addMinutes($service->duration_minutes);

            $bookings[] = Booking::create([
                'booking_number' => Booking::generateBookingNumber($date->toDateString()),
                'patient_id' => $p['patient']->id,
                'user_id' => $p['patient']->user_id,
                'service_id' => $service->id,
                'staff_id' => $p['doctor']->id,
                'booking_date' => $date,
                'start_time' => $p['time'],
                'end_time' => $end->format('H:i'),
                'status' => $p['status'],
                'payment_status' => $p['payment'],
                'payment_method' => $p['method'],
                'total_amount' => $service->price,
                'deposit_amount' => $service->price * 0.3,
                'internal_note' => $p['status'] === 'completed' ? 'Treatment went well.' : null,
                'cancellation_reason' => $p['status'] === 'cancelled' ? 'Patient had schedule conflict.' : null,
            ]);
        }

        // ---- TODAY bookings ----
        $todayData = [
            ['patient' => $patientAminah, 'service_idx' => 4, 'doctor' => $doctor2, 'time' => '10:00', 'status' => 'confirmed', 'payment' => 'deposit_paid', 'method' => 'Cash'],
            ['patient' => $patientKumar,  'service_idx' => 0, 'doctor' => $drAhmad, 'time' => '10:00', 'status' => 'confirmed', 'payment' => 'paid',         'method' => 'Card'],
            ['patient' => $patientAli,    'service_idx' => 5, 'doctor' => $drAhmad, 'time' => '11:00', 'status' => 'confirmed', 'payment' => 'deposit_paid', 'method' => 'Online Transfer'],
            ['patient' => $patientSusan,  'service_idx' => 6, 'doctor' => $doctor2, 'time' => '11:00', 'status' => 'pending',   'payment' => 'unpaid',       'method' => null],
            ['patient' => $patientRahma,  'service_idx' => 1, 'doctor' => $doctor3, 'time' => '14:00', 'status' => 'confirmed', 'payment' => 'deposit_paid', 'method' => 'Cash'],
            ['patient' => $patientDavid,  'service_idx' => 3, 'doctor' => $doctor3, 'time' => '15:00', 'status' => 'pending',   'payment' => 'unpaid',       'method' => null],
            ['patient' => $patientLina,   'service_idx' => 2, 'doctor' => $doctor2, 'time' => '16:00', 'status' => 'confirmed', 'payment' => 'paid',         'method' => 'Card'],
        ];

        $todayBookings = [];
        foreach ($todayData as $t) {
            $service = $services[$t['service_idx']];
            $start = Carbon::createFromFormat('H:i', $t['time']);
            $end = $start->copy()->addMinutes($service->duration_minutes);

            $todayBookings[] = Booking::create([
                'booking_number' => Booking::generateBookingNumber($today->toDateString()),
                'patient_id' => $t['patient']->id,
                'user_id' => $t['patient']->user_id,
                'service_id' => $service->id,
                'staff_id' => $t['doctor']->id,
                'booking_date' => $today,
                'start_time' => $t['time'],
                'end_time' => $end->format('H:i'),
                'status' => $t['status'],
                'payment_status' => $t['payment'],
                'payment_method' => $t['method'],
                'total_amount' => $service->price,
                'deposit_amount' => $t['payment'] !== 'unpaid' ? $service->price * 0.3 : null,
                'customer_note' => $t['status'] === 'pending' ? 'Please confirm my appointment.' : null,
            ]);
        }

        // ---- FUTURE bookings (this week + next week) ----
        $futureData = [
            ['patient' => $patientAli,    'service_idx' => 4, 'doctor' => $drAhmad, 'days' => 1, 'time' => '10:00', 'status' => 'confirmed', 'payment' => 'deposit_paid', 'method' => 'Card'],
            ['patient' => $patientJohn,   'service_idx' => 0, 'doctor' => $drAhmad, 'days' => 1, 'time' => '14:00', 'status' => 'pending',   'payment' => 'unpaid',       'method' => null],
            ['patient' => $patientAminah, 'service_idx' => 6, 'doctor' => $doctor2, 'days' => 2, 'time' => '10:00', 'status' => 'confirmed', 'payment' => 'paid',         'method' => 'Online Transfer'],
            ['patient' => $patientSusan,  'service_idx' => 1, 'doctor' => $doctor3, 'days' => 2, 'time' => '11:00', 'status' => 'pending',   'payment' => 'unpaid',       'method' => null],
            ['patient' => $patientKumar,  'service_idx' => 5, 'doctor' => $drAhmad, 'days' => 3, 'time' => '15:00', 'status' => 'confirmed', 'payment' => 'deposit_paid', 'method' => 'Cash'],
            ['patient' => $patientRahma,  'service_idx' => 4, 'doctor' => $doctor2, 'days' => 3, 'time' => '10:00', 'status' => 'confirmed', 'payment' => 'paid',         'method' => 'Card'],
            ['patient' => $patientDavid,  'service_idx' => 7, 'doctor' => $doctor3, 'days' => 4, 'time' => '10:00', 'status' => 'pending',   'payment' => 'unpaid',       'method' => null],
            ['patient' => $patientLina,   'service_idx' => 3, 'doctor' => $doctor2, 'days' => 5, 'time' => '11:00', 'status' => 'confirmed', 'payment' => 'deposit_paid', 'method' => 'Cash'],
            ['patient' => $patientAli,    'service_idx' => 2, 'doctor' => $drAhmad, 'days' => 7, 'time' => '10:00', 'status' => 'pending',   'payment' => 'unpaid',       'method' => null],
            ['patient' => $patientJohn,   'service_idx' => 6, 'doctor' => $doctor3, 'days' => 7, 'time' => '14:00', 'status' => 'confirmed', 'payment' => 'paid',         'method' => 'Card'],
            ['patient' => $patientAminah, 'service_idx' => 0, 'doctor' => $drAhmad, 'days' => 10, 'time' => '11:00', 'status' => 'pending',  'payment' => 'unpaid',       'method' => null],
            ['patient' => $patientKumar,  'service_idx' => 4, 'doctor' => $doctor2, 'days' => 12, 'time' => '15:00', 'status' => 'confirmed','payment' => 'deposit_paid', 'method' => 'Card'],
        ];

        foreach ($futureData as $f) {
            $service = $services[$f['service_idx']];
            $date = $today->copy()->addDays($f['days']);
            $start = Carbon::createFromFormat('H:i', $f['time']);
            $end = $start->copy()->addMinutes($service->duration_minutes);

            $bookings[] = Booking::create([
                'booking_number' => Booking::generateBookingNumber($date->toDateString()),
                'patient_id' => $f['patient']->id,
                'user_id' => $f['patient']->user_id,
                'service_id' => $service->id,
                'staff_id' => $f['doctor']->id,
                'booking_date' => $date,
                'start_time' => $f['time'],
                'end_time' => $end->format('H:i'),
                'status' => $f['status'],
                'payment_status' => $f['payment'],
                'payment_method' => $f['method'],
                'total_amount' => $service->price,
                'deposit_amount' => $f['payment'] !== 'unpaid' ? $service->price * 0.3 : null,
            ]);
        }

        // ───────────────────────────────────────────
        // 7. Room assignments for today's bookings
        //    Only assign first 4 bookings — leave 3 unassigned for drag-and-drop demo
        // ───────────────────────────────────────────
        foreach (array_slice($todayBookings, 0, 4) as $idx => $booking) {
            $room = $rooms[$idx % $rooms->count()];
            RoomAssignment::create([
                'treatment_room_id' => $room->id,
                'booking_id' => $booking->id,
                'doctor_id' => $booking->staff_id,
                'patient_id' => $booking->patient_id,
                'assigned_date' => $today,
                'start_time' => $booking->start_time,
                'end_time' => $booking->end_time,
                'status' => $booking->status === 'confirmed' ? 'scheduled' : 'scheduled',
            ]);
        }

        // ───────────────────────────────────────────
        // 8. Patient packages
        // ───────────────────────────────────────────
        $pkg1 = PatientPackage::create([
            'patient_id' => $patientAli->id,
            'package_name' => 'Hydrafacial 10-Session Package',
            'service_id' => $services[4]->id, // Hydrafacial
            'total_sessions' => 10,
            'used_sessions' => 3,
            'remaining_sessions' => 7,
            'start_date' => $today->copy()->subDays(30),
            'end_date' => $today->copy()->addMonths(6),
            'status' => 'active',
            'notes' => 'Monthly hydrafacial treatment plan.',
        ]);

        $pkg2 = PatientPackage::create([
            'patient_id' => $patientAminah->id,
            'package_name' => 'Microneedling 6-Session Package',
            'service_id' => $services[6]->id, // Microneedling
            'total_sessions' => 6,
            'used_sessions' => 4,
            'remaining_sessions' => 2,
            'start_date' => $today->copy()->subDays(60),
            'end_date' => $today->copy()->addMonths(3),
            'status' => 'active',
            'notes' => 'Bi-weekly microneedling for acne scars.',
        ]);

        $pkg3 = PatientPackage::create([
            'patient_id' => $patientKumar->id,
            'package_name' => 'Botox Maintenance 4 Sessions',
            'service_id' => $services[0]->id, // Botox
            'total_sessions' => 4,
            'used_sessions' => 4,
            'remaining_sessions' => 0,
            'start_date' => $today->copy()->subMonths(4),
            'end_date' => $today->copy()->subDays(5),
            'status' => 'completed',
            'notes' => 'Quarterly botox maintenance completed.',
        ]);

        $pkg4 = PatientPackage::create([
            'patient_id' => $patientSusan->id,
            'package_name' => 'Laser Hair Removal 8 Sessions',
            'service_id' => $services[3]->id, // Laser Hair Removal
            'total_sessions' => 8,
            'used_sessions' => 1,
            'remaining_sessions' => 7,
            'start_date' => $today->copy()->subDays(10),
            'end_date' => $today->copy()->addMonths(8),
            'status' => 'active',
            'notes' => 'Full legs laser hair removal.',
        ]);

        $pkg5 = PatientPackage::create([
            'patient_id' => $patientDavid->id,
            'package_name' => 'PRP Hair Treatment 5 Sessions',
            'service_id' => $services[5]->id, // PRP
            'total_sessions' => 5,
            'used_sessions' => 0,
            'remaining_sessions' => 5,
            'start_date' => $today,
            'end_date' => $today->copy()->addMonths(5),
            'status' => 'active',
            'notes' => 'PRP for hair loss treatment.',
        ]);

        // ───────────────────────────────────────────
        // 9. Treatment histories
        // ───────────────────────────────────────────
        // Ali — 3 Hydrafacial sessions used
        for ($i = 1; $i <= 3; $i++) {
            TreatmentHistory::create([
                'patient_id' => $patientAli->id,
                'patient_package_id' => $pkg1->id,
                'service_id' => $services[4]->id,
                'doctor_id' => $drAhmad->id,
                'treatment_date' => $today->copy()->subDays(30 - ($i * 10)),
                'package_name' => $pkg1->package_name,
                'treatment_name' => 'Hydrafacial',
                'session_number' => $i,
                'total_sessions' => 10,
                'remaining_sessions' => 10 - $i,
                'notes' => "Session $i completed successfully. Skin condition improving.",
            ]);
        }

        // Aminah — 4 Microneedling sessions used
        for ($i = 1; $i <= 4; $i++) {
            TreatmentHistory::create([
                'patient_id' => $patientAminah->id,
                'patient_package_id' => $pkg2->id,
                'service_id' => $services[6]->id,
                'doctor_id' => $doctor2->id,
                'treatment_date' => $today->copy()->subDays(60 - ($i * 14)),
                'package_name' => $pkg2->package_name,
                'treatment_name' => 'Microneedling',
                'session_number' => $i,
                'total_sessions' => 6,
                'remaining_sessions' => 6 - $i,
                'notes' => "Session $i - Acne scars showing significant improvement.",
            ]);
        }

        // Kumar — 4 Botox sessions (completed package)
        for ($i = 1; $i <= 4; $i++) {
            TreatmentHistory::create([
                'patient_id' => $patientKumar->id,
                'patient_package_id' => $pkg3->id,
                'service_id' => $services[0]->id,
                'doctor_id' => $drAhmad->id,
                'treatment_date' => $today->copy()->subMonths(4)->addDays(($i - 1) * 30),
                'package_name' => $pkg3->package_name,
                'treatment_name' => 'Botox Treatment',
                'session_number' => $i,
                'total_sessions' => 4,
                'remaining_sessions' => 4 - $i,
                'notes' => $i === 4 ? 'Final session completed. Results excellent.' : "Session $i done. Good results.",
            ]);
        }

        // Susan — 1 Laser session
        TreatmentHistory::create([
            'patient_id' => $patientSusan->id,
            'patient_package_id' => $pkg4->id,
            'service_id' => $services[3]->id,
            'doctor_id' => $doctor3->id,
            'treatment_date' => $today->copy()->subDays(10),
            'package_name' => $pkg4->package_name,
            'treatment_name' => 'Laser Hair Removal',
            'session_number' => 1,
            'total_sessions' => 8,
            'remaining_sessions' => 7,
            'notes' => 'First session. Patient tolerated well. Slight redness expected.',
        ]);

        // Standalone treatment histories (not linked to packages)
        TreatmentHistory::create([
            'patient_id' => $patientRahma->id,
            'service_id' => $services[0]->id,
            'doctor_id' => $drAhmad->id,
            'treatment_date' => $today->copy()->subDays(18),
            'treatment_name' => 'Botox Treatment',
            'notes' => 'First time Botox. 20 units forehead. Results visible in 5-7 days.',
        ]);

        TreatmentHistory::create([
            'patient_id' => $patientJohn->id,
            'service_id' => $services[3]->id,
            'doctor_id' => $doctor3->id,
            'treatment_date' => $today->copy()->subDays(12),
            'treatment_name' => 'Laser Hair Removal',
            'notes' => 'Single session underarm laser. No adverse reaction.',
        ]);

        // ───────────────────────────────────────────
        // 10. Follow-ups
        // ───────────────────────────────────────────
        FollowUp::create(['patient_id' => $patientAli->id, 'booking_id' => $bookings[1]->id ?? null, 'assigned_to' => $sarah->id, 'follow_up_date' => $today->copy()->addDays(2), 'status' => 'not_contacted', 'notes' => 'Follow up after Botox treatment. Check results.']);
        FollowUp::create(['patient_id' => $patientAminah->id, 'assigned_to' => $sarah->id, 'follow_up_date' => $today->copy()->addDays(1), 'status' => 'contacted', 'notes' => 'Called to remind about next microneedling session.']);
        FollowUp::create(['patient_id' => $patientRahma->id, 'assigned_to' => $staff2->id, 'follow_up_date' => $today, 'status' => 'interested', 'notes' => 'Patient interested in Dermal Filler package. Send pricing info.']);
        FollowUp::create(['patient_id' => $patientJohn->id, 'assigned_to' => $sarah->id, 'follow_up_date' => $today->copy()->subDays(2), 'status' => 'not_interested', 'notes' => 'Patient not interested in continuing laser treatment.']);
        FollowUp::create(['patient_id' => $patientSusan->id, 'assigned_to' => $staff2->id, 'follow_up_date' => $today->copy()->addDays(3), 'status' => 'not_contacted', 'notes' => 'Check skin condition after Chemical Peel session.']);
        FollowUp::create(['patient_id' => $patientKumar->id, 'assigned_to' => $sarah->id, 'follow_up_date' => $today->copy()->subDays(5), 'status' => 'booked', 'notes' => 'Patient rebooked for new Botox package.']);
        FollowUp::create(['patient_id' => $patientDavid->id, 'assigned_to' => $staff2->id, 'follow_up_date' => $today->copy()->addDays(5), 'status' => 'not_contacted', 'notes' => 'New patient. Follow up after initial consultation.']);
        FollowUp::create(['patient_id' => $patientLina->id, 'assigned_to' => $sarah->id, 'follow_up_date' => $today->copy()->subDays(7), 'status' => 'closed', 'notes' => 'Patient cancelled and not returning.']);
    }
}
