<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\TreatmentRoom;

class TreatmentRoomSeeder extends Seeder
{
    public function run(): void
    {
        $rooms = [
            ['name' => 'Treatment Room 1', 'room_code' => 'R01', 'description' => 'General treatment room', 'status' => 'active'],
            ['name' => 'Treatment Room 2', 'room_code' => 'R02', 'description' => 'General treatment room', 'status' => 'active'],
            ['name' => 'Treatment Room 3', 'room_code' => 'R03', 'description' => 'Dental treatment room', 'status' => 'active'],
            ['name' => 'Treatment Room 4', 'room_code' => 'R04', 'description' => 'Aesthetic treatment room', 'status' => 'active'],
            ['name' => 'Treatment Room 5', 'room_code' => 'R05', 'description' => 'Physiotherapy room', 'status' => 'active'],
            ['name' => 'Treatment Room 6', 'room_code' => 'R06', 'description' => 'VIP treatment room', 'status' => 'active'],
        ];

        foreach ($rooms as $room) {
            TreatmentRoom::create($room);
        }
    }
}
