<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class BookingRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'patient_id' => 'required|exists:patients,id',
            'service_id' => 'required|exists:services,id',
            'staff_id' => 'nullable|exists:users,id',
            'booking_date' => 'required|date|after_or_equal:today',
            'start_time' => 'required|date_format:H:i',
            'customer_note' => 'nullable|string|max:1000',
            'internal_note' => 'nullable|string|max:1000',
            'payment_method' => 'nullable|string|max:50',
            'deposit_amount' => 'nullable|numeric|min:0',
        ];
    }
}
