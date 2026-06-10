<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Carbon\Carbon;

class Booking extends Model
{
    protected $fillable = [
        'booking_number', 'patient_id', 'user_id', 'service_id',
        'staff_id', 'booking_date', 'start_time', 'end_time',
        'status', 'payment_status', 'payment_method', 'total_amount',
        'deposit_amount', 'customer_note', 'internal_note',
        'reschedule_reason', 'cancellation_reason',
    ];

    protected function casts(): array
    {
        return [
            'booking_date' => 'date',
            'total_amount' => 'decimal:2',
            'deposit_amount' => 'decimal:2',
        ];
    }

    // Generate unique booking number: CLN-YYYYMMDD-0001
    public static function generateBookingNumber(string $date): string
    {
        $prefix = 'CLN-' . Carbon::parse($date)->format('Ymd') . '-';
        $lastBooking = self::where('booking_number', 'like', $prefix . '%')
            ->orderByDesc('booking_number')
            ->first();

        if ($lastBooking) {
            $lastNumber = (int) substr($lastBooking->booking_number, -4);
            return $prefix . str_pad($lastNumber + 1, 4, '0', STR_PAD_LEFT);
        }

        return $prefix . '0001';
    }

    public function patient(): BelongsTo
    {
        return $this->belongsTo(Patient::class);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function service(): BelongsTo
    {
        return $this->belongsTo(Service::class);
    }

    public function staffMember(): BelongsTo
    {
        return $this->belongsTo(User::class, 'staff_id');
    }

    public function followUps(): HasMany
    {
        return $this->hasMany(FollowUp::class);
    }

    public function roomAssignment(): \Illuminate\Database\Eloquent\Relations\HasOne
    {
        return $this->hasOne(RoomAssignment::class);
    }

    public function treatmentHistories(): HasMany
    {
        return $this->hasMany(TreatmentHistory::class);
    }

    public function statusBadgeColor(): string
    {
        return match ($this->status) {
            'pending' => 'yellow',
            'confirmed' => 'blue',
            'completed' => 'green',
            'cancelled' => 'red',
            'rescheduled' => 'purple',
            'no_show' => 'gray',
            default => 'gray',
        };
    }

    public function paymentBadgeColor(): string
    {
        return match ($this->payment_status) {
            'unpaid' => 'red',
            'deposit_paid' => 'yellow',
            'paid' => 'green',
            'refunded' => 'gray',
            default => 'gray',
        };
    }
}
