<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class ServiceRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'name' => 'required|string|max:255',
            'description' => 'nullable|string|max:2000',
            'price' => 'required|numeric|min:0',
            'duration_minutes' => 'required|integer|min:5|max:480',
            'status' => 'required|in:active,inactive',
            'staff_ids' => 'nullable|array',
            'staff_ids.*' => 'exists:users,id',
        ];
    }
}
