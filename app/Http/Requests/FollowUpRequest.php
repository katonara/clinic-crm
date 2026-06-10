<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class FollowUpRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'patient_id' => 'required|exists:patients,id',
            'booking_id' => 'nullable|exists:bookings,id',
            'assigned_to' => 'nullable|exists:users,id',
            'follow_up_date' => 'required|date',
            'status' => 'required|in:not_contacted,contacted,interested,not_interested,booked,closed',
            'notes' => 'nullable|string|max:2000',
        ];
    }
}
