<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class PatientPackage extends Model
{
    protected $fillable = [
        'patient_id', 'package_name', 'service_id', 'total_sessions',
        'used_sessions', 'remaining_sessions', 'start_date', 'end_date',
        'status', 'notes',
    ];

    protected function casts(): array
    {
        return [
            'start_date' => 'date',
            'end_date' => 'date',
        ];
    }

    public function patient(): BelongsTo
    {
        return $this->belongsTo(Patient::class);
    }

    public function service(): BelongsTo
    {
        return $this->belongsTo(Service::class);
    }

    public function treatmentHistories(): HasMany
    {
        return $this->hasMany(TreatmentHistory::class);
    }

    // Deduct one session after treatment completion
    public function deductSession(): bool
    {
        if ($this->remaining_sessions <= 0) {
            return false;
        }

        $this->used_sessions += 1;
        $this->remaining_sessions -= 1;

        if ($this->remaining_sessions === 0) {
            $this->status = 'completed';
        }

        return $this->save();
    }

    public function statusColor(): string
    {
        return match ($this->status) {
            'active' => 'green',
            'completed' => 'blue',
            'expired' => 'yellow',
            'cancelled' => 'red',
            default => 'gray',
        };
    }

    public function scopeActive($query)
    {
        return $query->where('status', 'active');
    }
}
