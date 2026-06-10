<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ClinicSetting extends Model
{
    protected $fillable = [
        'clinic_name', 'logo', 'email', 'whatsapp_country_code',
        'whatsapp_number', 'address', 'default_slot_duration',
        'opening_time', 'closing_time', 'theme_primary_color',
    ];

    public static function instance(): self
    {
        return self::firstOrCreate(['id' => 1], [
            'clinic_name' => 'Klinik Demo',
            'default_slot_duration' => 30,
            'opening_time' => '09:00',
            'closing_time' => '18:00',
        ]);
    }
}
