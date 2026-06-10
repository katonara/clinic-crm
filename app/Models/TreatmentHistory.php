<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class TreatmentHistory extends Model
{
    protected $fillable = [
        'patient_id', 'booking_id', 'patient_package_id', 'service_id',
        'doctor_id', 'treatment_date', 'package_name', 'treatment_name',
        'session_number', 'total_sessions', 'remaining_sessions', 'notes',
    ];

    protected function casts(): array
    {
        return [
            'treatment_date' => 'date',
        ];
    }

    public function patient(): BelongsTo
    {
        return $this->belongsTo(Patient::class);
    }

    public function booking(): BelongsTo
    {
        return $this->belongsTo(Booking::class);
    }

    public function patientPackage(): BelongsTo
    {
        return $this->belongsTo(PatientPackage::class);
    }

    public function service(): BelongsTo
    {
        return $this->belongsTo(Service::class);
    }

    public function doctor(): BelongsTo
    {
        return $this->belongsTo(User::class, 'doctor_id');
    }
}
