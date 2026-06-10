<?php

namespace Database\Seeders;

use App\Models\WhatsappTemplate;
use Illuminate\Database\Seeder;

class WhatsappTemplateSeeder extends Seeder
{
    public function run(): void
    {
        $templates = [
            [
                'title' => 'Booking Confirmation',
                'type' => 'booking_confirmation',
                'message' => "Hi {patient_name}! 👋\n\nYour appointment has been confirmed:\n📋 Service: {service_name}\n📅 Date: {booking_date}\n⏰ Time: {booking_time}\n\nPlease arrive 10 minutes early.\n\nThank you!\n{clinic_name}",
            ],
            [
                'title' => 'Booking Reminder',
                'type' => 'booking_reminder',
                'message' => "Hi {patient_name}! 🔔\n\nThis is a reminder for your upcoming appointment:\n📋 Service: {service_name}\n📅 Date: {booking_date}\n⏰ Time: {booking_time}\n\nPlease contact us if you need to reschedule.\n\n{clinic_name}\n📞 {clinic_whatsapp}",
            ],
            [
                'title' => 'Follow Up Message',
                'type' => 'follow_up',
                'message' => "Hi {patient_name}! 😊\n\nHow are you feeling after your {service_name} treatment?\n\nIf you have any questions or concerns, don't hesitate to reach out.\n\nWould you like to book a follow-up appointment?\n\n{clinic_name}\n📞 {clinic_whatsapp}",
            ],
            [
                'title' => 'Reschedule Notice',
                'type' => 'reschedule',
                'message' => "Hi {patient_name},\n\nYour appointment for {service_name} on {booking_date} has been rescheduled.\n\nPlease contact us to arrange a new date and time.\n\n{clinic_name}\n📞 {clinic_whatsapp}",
            ],
            [
                'title' => 'Payment Reminder',
                'type' => 'payment_reminder',
                'message' => "Hi {patient_name},\n\nThis is a friendly reminder about the outstanding payment for your {service_name} treatment on {booking_date}.\n\nPlease contact us to arrange payment.\n\nThank you!\n{clinic_name}\n📞 {clinic_whatsapp}",
            ],
        ];

        foreach ($templates as $template) {
            WhatsappTemplate::create(array_merge($template, ['status' => 'active']));
        }
    }
}
