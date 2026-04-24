<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('appointments', function (Blueprint $table) {
            $table->id();
            $table->foreignId('patient_id')->constrained()->onDelete('cascade');
            $table->foreignId('doctor_id')->constrained()->onDelete('cascade');
            $table->date('appointment_date');
            $table->time('appointment_slot'); // e.g. 09:00, 09:30
            $table->enum('status', ['pending', 'confirmed', 'completed', 'cancelled', 'no_show'])
                  ->default('pending');
            $table->text('reason')->nullable();         // reason for visit
            $table->text('consultation_notes')->nullable(); // doctor's notes
            $table->text('diagnosis')->nullable();
            $table->text('prescription')->nullable();
            $table->timestamps();
            $table->softDeletes();

            // Prevent double-booking: same doctor, date, slot
            $table->unique(['doctor_id', 'appointment_date', 'appointment_slot'], 'unique_doctor_slot');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('appointments');
    }
};
