<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class WhatsappTemplate extends Model
{
    protected $fillable = ['title', 'type', 'message', 'status'];

    public function renderMessage(array $data): string
    {
        $message = $this->message;
        foreach ($data as $key => $value) {
            $message = str_replace('{' . $key . '}', $value, $message);
        }
        return $message;
    }

    public function scopeActive($query)
    {
        return $query->where('status', 'active');
    }
}
