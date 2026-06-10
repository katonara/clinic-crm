<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class DoctorRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        $userId = $this->route('doctor')?->user_id;

        return [
            'name' => 'required|string|max:255',
            'email' => [
                'required', 'email', 'max:255',
                Rule::unique('users', 'email')->ignore($userId),
            ],
            'password' => $userId ? 'nullable|string|min:8' : 'required|string|min:8',
            'country_code' => 'required|string|max:10',
            'whatsapp_number' => 'required|string|max:20',
            'specialty' => 'nullable|string|max:255',
            'bio' => 'nullable|string|max:2000',
            'status' => 'required|in:active,inactive',
            'service_ids' => 'nullable|array',
            'service_ids.*' => 'exists:services,id',
        ];
    }
}
