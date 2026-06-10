<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class FollowUp extends Model
{
    protected $fillable = [
        'patient_id', 'booking_id', 'assigned_to',
        'follow_up_date', 'status', 'notes',
    ];

    protected function casts(): array
    {
        return [
            'follow_up_date' => 'date',
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

    public function assignedUser(): BelongsTo
    {
        return $this->belongsTo(User::class, 'assigned_to');
    }

    public function statusBadgeColor(): string
    {
        return match ($this->status) {
            'not_contacted' => 'gray',
            'contacted' => 'blue',
            'interested' => 'yellow',
            'not_interested' => 'red',
            'booked' => 'green',
            'closed' => 'purple',
            default => 'gray',
        };
    }
}
