<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class AdminUserSeeder extends Seeder
{
    public function run(): void
    {
        User::create([
            'name' => 'Admin',
            'email' => 'admin@radiantclinic',
            'password' => Hash::make('adminrc@1234!'),
            'role' => 'admin',
            'country_code' => '+60',
            'whatsapp_number' => '123456789',
            'email_verified_at' => now(),
            'status' => 'active',
        ]);

        $staff = User::create([
            'name' => 'Sarah Staff',
            'email' => 'staff@clinic.test',
            'password' => Hash::make('password'),
            'role' => 'staff',
            'country_code' => '+60',
            'whatsapp_number' => '111222333',
            'email_verified_at' => now(),
            'status' => 'active',
        ]);

        \App\Models\StaffProfile::create([
            'user_id' => $staff->id,
            'position' => 'Receptionist',
            'bio' => 'Friendly clinic receptionist.',
            'status' => 'active',
        ]);

        $doctor = User::create([
            'name' => 'Dr. Ahmad',
            'email' => 'doctor@clinic.test',
            'password' => Hash::make('password'),
            'role' => 'doctor',
            'country_code' => '+60',
            'whatsapp_number' => '444555666',
            'email_verified_at' => now(),
            'status' => 'active',
        ]);

        \App\Models\Doctor::create([
            'user_id' => $doctor->id,
            'specialty' => 'Aesthetic & Dermatology',
            'bio' => 'Specialist in aesthetic treatments and skin care.',
            'status' => 'active',
        ]);

        User::create([
            'name' => 'Ali Customer',
            'email' => 'customer@clinic.test',
            'password' => Hash::make('password'),
            'role' => 'customer',
            'country_code' => '+60',
            'whatsapp_number' => '777888999',
            'email_verified_at' => now(),
            'status' => 'active',
        ]);
    }
}
