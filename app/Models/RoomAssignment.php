<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class RoomAssignment extends Model
{
    protected $fillable = [
        'treatment_room_id', 'booking_id', 'doctor_id', 'patient_id',
        'assigned_date', 'start_time', 'end_time', 'status',
    ];

    protected function casts(): array
    {
        return [
            'assigned_date' => 'date',
        ];
    }

    public function treatmentRoom(): BelongsTo
    {
        return $this->belongsTo(TreatmentRoom::class);
    }

    public function booking(): BelongsTo
    {
        return $this->belongsTo(Booking::class);
    }

    public function doctor(): BelongsTo
    {
        return $this->belongsTo(User::class, 'doctor_id');
    }

    public function patient(): BelongsTo
    {
        return $this->belongsTo(Patient::class);
    }

    public function statusColor(): string
    {
        return match ($this->status) {
            'scheduled' => 'blue',
            'occupied' => 'red',
            'ending_soon' => 'yellow',
            'completed' => 'green',
            'cancelled' => 'gray',
            default => 'gray',
        };
    }
}
