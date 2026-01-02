<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('course_class_attendance', function (Blueprint $table) {
            $table->id();
            $table->foreignId('course_class_meeting_id')->constrained('course_class_meetings')->onDelete('cascade');
            $table->morphs('enrollable'); // CourseEnrollment or CoupleEnrollment
            $table->enum('status', ['present', 'absent', 'justified'])->default('absent');
            $table->text('observations')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('course_class_attendance');
    }
};
