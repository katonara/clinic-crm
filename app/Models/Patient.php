<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Patient extends Model
{
    protected $fillable = [
        'user_id', 'name', 'email', 'country_code',
        'whatsapp_number', 'gender', 'date_of_birth',
        'notes', 'tag',
    ];

    protected function casts(): array
    {
        return [
            'date_of_birth' => 'date',
        ];
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function bookings(): HasMany
    {
        return $this->hasMany(Booking::class);
    }

    public function followUps(): HasMany
    {
        return $this->hasMany(FollowUp::class);
    }

    public function packages(): HasMany
    {
        return $this->hasMany(PatientPackage::class);
    }

    public function treatmentHistories(): HasMany
    {
        return $this->hasMany(TreatmentHistory::class);
    }

    public function roomAssignments(): HasMany
    {
        return $this->hasMany(RoomAssignment::class);
    }

    public function fullWhatsappNumber(): string
    {
        return $this->country_code . $this->whatsapp_number;
    }
}
