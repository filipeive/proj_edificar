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
        Schema::create('ministerial_enrollments', function (Blueprint $table) {
            $table->id();
            $table->foreignId('course_id')->constrained()->onDelete('cascade');
            $table->foreignId('course_class_id')->nullable()->constrained()->onDelete('set null');
            $table->string('full_name');
            $table->string('email')->nullable();
            $table->string('phone');
            $table->boolean('is_church_member')->default(false);
            $table->foreignId('zone_id')->nullable()->constrained()->onDelete('set null');
            $table->string('cell_name')->nullable();
            $table->text('observations')->nullable();
            $table->string('status')->default('pending'); // pending, approved, rejected
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('ministerial_enrollments');
    }
};
