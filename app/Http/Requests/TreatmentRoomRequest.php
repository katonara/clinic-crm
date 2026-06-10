<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class TreatmentRoomRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'name' => 'required|string|max:255',
            'room_code' => 'nullable|string|max:20',
            'description' => 'nullable|string|max:1000',
            'status' => 'required|in:active,inactive,closed',
        ];
    }
}
