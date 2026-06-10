<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class User extends Authenticatable
{
    use HasFactory, Notifiable;

    protected $fillable = [
        'name', 'email', 'password', 'role', 'country_code',
        'whatsapp_number', 'email_verified_at', 'otp_code',
        'otp_expires_at', 'status',
    ];

    protected $hidden = [
        'password', 'remember_token', 'otp_code',
    ];

    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'otp_expires_at' => 'datetime',
            'password' => 'hashed',
        ];
    }

    public function isAdmin(): bool
    {
        return $this->role === 'admin';
    }

    public function isStaff(): bool
    {
        return $this->role === 'staff';
    }

    public function isDoctor(): bool
    {
        return $this->role === 'doctor';
    }

    public function isCustomer(): bool
    {
        return $this->role === 'customer';
    }

    public function isStaffOrAdmin(): bool
    {
        return in_array($this->role, ['admin', 'staff']);
    }

    public function isClinicMember(): bool
    {
        return in_array($this->role, ['admin', 'staff', 'doctor']);
    }

    public function staffProfile(): HasOne
    {
        return $this->hasOne(StaffProfile::class);
    }

    public function patient(): HasOne
    {
        return $this->hasOne(Patient::class);
    }

    public function services(): BelongsToMany
    {
        return $this->belongsToMany(Service::class, 'service_staff');
    }

    public function bookingsAsStaff(): HasMany
    {
        return $this->hasMany(Booking::class, 'staff_id');
    }

    public function bookingsAsCustomer(): HasMany
    {
        return $this->hasMany(Booking::class, 'user_id');
    }

    public function workingHours(): HasMany
    {
        return $this->hasMany(WorkingHour::class);
    }

    public function blockedDates(): HasMany
    {
        return $this->hasMany(BlockedDate::class);
    }

    public function assignedFollowUps(): HasMany
    {
        return $this->hasMany(FollowUp::class, 'assigned_to');
    }

    public function doctorProfile(): HasOne
    {
        return $this->hasOne(Doctor::class);
    }

    public function roomAssignments(): HasMany
    {
        return $this->hasMany(RoomAssignment::class, 'doctor_id');
    }

    public function treatmentHistoriesAsDoctor(): HasMany
    {
        return $this->hasMany(TreatmentHistory::class, 'doctor_id');
    }

    public function fullWhatsappNumber(): string
    {
        return $this->country_code . $this->whatsapp_number;
    }
}
