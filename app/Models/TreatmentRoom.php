<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class TreatmentRoom extends Model
{
    protected $fillable = [
        'name', 'room_code', 'description', 'status',
    ];

    public function assignments(): HasMany
    {
        return $this->hasMany(RoomAssignment::class);
    }

    public function scopeActive($query)
    {
        return $query->where('status', 'active');
    }

    // Get live availability status based on current time
    public function liveStatus(?string $date = null, ?string $time = null): string
    {
        if ($this->status !== 'active') {
            return 'closed';
        }

        $date = $date ?? now()->toDateString();
        $time = $time ?? now()->format('H:i:s');

        $assignment = $this->assignments()
            ->where('assigned_date', $date)
            ->where('start_time', '<=', $time)
            ->where('end_time', '>', $time)
            ->whereIn('status', ['scheduled', 'occupied', 'ending_soon'])
            ->first();

        if (!$assignment) {
            return 'available';
        }

        $endTime = \Carbon\Carbon::parse($assignment->end_time);
        $now = \Carbon\Carbon::parse($time);
        $minutesLeft = $now->diffInMinutes($endTime, false);

        if ($minutesLeft <= 10 && $minutesLeft > 0) {
            return 'ending_soon';
        }

        return 'occupied';
    }

    public static function statusColor(string $status): string
    {
        return match ($status) {
            'available' => 'green',
            'occupied' => 'red',
            'ending_soon' => 'yellow',
            'closed' => 'gray',
            default => 'gray',
        };
    }
}
