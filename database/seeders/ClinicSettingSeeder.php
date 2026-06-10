<?php

namespace Database\Seeders;

use App\Models\ClinicSetting;
use Illuminate\Database\Seeder;

class ClinicSettingSeeder extends Seeder
{
    public function run(): void
    {
        ClinicSetting::create([
            'clinic_name' => 'Glow Aesthetic Clinic',
            'email' => 'info@glowclinic.com',
            'whatsapp_country_code' => '+60',
            'whatsapp_number' => '123456789',
            'address' => '123 Beauty Street, Kuala Lumpur, Malaysia',
            'default_slot_duration' => 30,
            'opening_time' => '09:00',
            'closing_time' => '18:00',
        ]);
    }
}
