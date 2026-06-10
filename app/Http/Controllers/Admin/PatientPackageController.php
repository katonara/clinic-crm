<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\PatientPackageRequest;
use App\Models\Patient;
use App\Models\PatientPackage;
use App\Models\Service;
use App\Models\TreatmentHistory;
use Illuminate\Http\Request;

class PatientPackageController extends Controller
{
    public function index(Request $request)
    {
        $query = PatientPackage::with(['patient', 'service']);

        if ($request->filled('patient_id')) {
            $query->where('patient_id', $request->patient_id);
        }
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        $packages = $query->orderByDesc('created_at')->paginate(15)->withQueryString();

        return view('admin.patient-packages.index', compact('packages'));
    }

    public function create(Request $request)
    {
        $patients = Patient::orderBy('name')->get();
        $services = Service::active()->get();
        $selectedPatient = $request->get('patient_id');

        return view('admin.patient-packages.create', compact('patients', 'services', 'selectedPatient'));
    }

    public function store(PatientPackageRequest $request)
    {
        PatientPackage::create([
            'patient_id' => $request->patient_id,
            'package_name' => $request->package_name,
            'service_id' => $request->service_id,
            'total_sessions' => $request->total_sessions,
            'used_sessions' => 0,
            'remaining_sessions' => $request->total_sessions,
            'start_date' => $request->start_date,
            'end_date' => $request->end_date,
            'status' => $request->status,
            'notes' => $request->notes,
        ]);

        return redirect()->route('admin.patient-packages.index', ['patient_id' => $request->patient_id])
            ->with('success', 'Package created successfully.');
    }

    public function edit(PatientPackage $patientPackage)
    {
        $patients = Patient::orderBy('name')->get();
        $services = Service::active()->get();

        return view('admin.patient-packages.edit', compact('patientPackage', 'patients', 'services'));
    }

    public function update(PatientPackageRequest $request, PatientPackage $patientPackage)
    {
        $newTotal = $request->total_sessions;
        $usedSessions = $patientPackage->used_sessions;
        $remaining = max(0, $newTotal - $usedSessions);

        $patientPackage->update([
            'patient_id' => $request->patient_id,
            'package_name' => $request->package_name,
            'service_id' => $request->service_id,
            'total_sessions' => $newTotal,
            'remaining_sessions' => $remaining,
            'start_date' => $request->start_date,
            'end_date' => $request->end_date,
            'status' => $request->status,
            'notes' => $request->notes,
        ]);

        return redirect()->route('admin.patient-packages.index', ['patient_id' => $patientPackage->patient_id])
            ->with('success', 'Package updated successfully.');
    }

    public function destroy(PatientPackage $patientPackage)
    {
        $patientPackage->delete();

        return redirect()->route('admin.patient-packages.index')
            ->with('success', 'Package deleted successfully.');
    }

    // Deduct a session from package and record treatment history
    public function deductSession(Request $request, PatientPackage $patientPackage)
    {
        $request->validate([
            'booking_id' => 'nullable|exists:bookings,id',
            'doctor_id' => 'nullable|exists:users,id',
            'notes' => 'nullable|string|max:2000',
        ]);

        if ($patientPackage->remaining_sessions <= 0) {
            return back()->with('error', 'No remaining sessions in this package.');
        }

        $sessionNumber = $patientPackage->used_sessions + 1;

        $patientPackage->deductSession();

        TreatmentHistory::create([
            'patient_id' => $patientPackage->patient_id,
            'booking_id' => $request->booking_id,
            'patient_package_id' => $patientPackage->id,
            'service_id' => $patientPackage->service_id ?? 0,
            'doctor_id' => $request->doctor_id,
            'treatment_date' => now()->toDateString(),
            'package_name' => $patientPackage->package_name,
            'treatment_name' => $patientPackage->service?->name ?? $patientPackage->package_name,
            'session_number' => $sessionNumber,
            'total_sessions' => $patientPackage->total_sessions,
            'remaining_sessions' => $patientPackage->remaining_sessions,
            'notes' => $request->notes,
        ]);

        return back()->with('success', "Session $sessionNumber recorded. {$patientPackage->remaining_sessions} sessions remaining.");
    }
}
