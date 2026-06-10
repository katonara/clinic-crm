<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('clinic_settings', function (Blueprint $table) {
            $table->id();
            $table->string('clinic_name');
            $table->string('logo')->nullable();
            $table->string('email')->nullable();
            $table->string('whatsapp_country_code', 10)->default('+60');
            $table->string('whatsapp_number', 20)->nullable();
            $table->text('address')->nullable();
            $table->integer('default_slot_duration')->default(30);
            $table->time('opening_time')->default('09:00');
            $table->time('closing_time')->default('18:00');
            $table->string('theme_primary_color', 20)->default('blue');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('clinic_settings');
    }
};
