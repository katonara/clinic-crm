<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class WhatsappTemplateRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'title' => 'required|string|max:255',
            'type' => 'required|in:booking_confirmation,booking_reminder,follow_up,reschedule,payment_reminder',
            'message' => 'required|string|max:2000',
            'status' => 'required|in:active,inactive',
        ];
    }
}
