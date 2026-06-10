<?php

namespace Database\Seeders;

use App\Models\Service;
use Illuminate\Database\Seeder;

class ServiceSeeder extends Seeder
{
    public function run(): void
    {
        $services = [
            ['name' => 'Botox Treatment', 'description' => 'Anti-wrinkle botulinum toxin injection for fine lines and wrinkles.', 'price' => 800.00, 'duration_minutes' => 30],
            ['name' => 'Dermal Filler', 'description' => 'Hyaluronic acid filler for volume restoration and facial contouring.', 'price' => 1200.00, 'duration_minutes' => 45],
            ['name' => 'Chemical Peel', 'description' => 'Professional-grade chemical exfoliation for skin rejuvenation.', 'price' => 350.00, 'duration_minutes' => 30],
            ['name' => 'Laser Hair Removal', 'description' => 'Permanent hair reduction using advanced laser technology.', 'price' => 500.00, 'duration_minutes' => 60],
            ['name' => 'Hydrafacial', 'description' => 'Deep cleansing and hydrating facial treatment.', 'price' => 450.00, 'duration_minutes' => 45],
            ['name' => 'PRP Therapy', 'description' => 'Platelet-rich plasma therapy for skin and hair rejuvenation.', 'price' => 1500.00, 'duration_minutes' => 60],
            ['name' => 'Microneedling', 'description' => 'Collagen induction therapy for scar and texture improvement.', 'price' => 600.00, 'duration_minutes' => 45],
            ['name' => 'Consultation', 'description' => 'Initial consultation with our aesthetic doctor.', 'price' => 50.00, 'duration_minutes' => 30],
        ];

        foreach ($services as $service) {
            Service::create(array_merge($service, ['status' => 'active']));
        }
    }
}
