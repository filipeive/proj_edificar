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
        Schema::create('couple_enrollments', function (Blueprint $table) {
            $table->id();
            $table->foreignId('course_id')->constrained()->onDelete('cascade');
            $table->string('husband_name');
            $table->string('wife_name');
            $table->enum('relationship_type', ['namoro', 'noivos', 'vivendo_maritalmente', 'casados']);
            $table->string('address');
            $table->string('contacts');
            $table->string('cell_zone')->nullable();
            $table->integer('years_together');
            $table->string('leader_name')->nullable();
            $table->boolean('has_pastoral_recommendation')->default(false);
            $table->text('observations')->nullable();
            $table->enum('status', ['pending', 'approved', 'rejected'])->default('pending');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('couple_enrollments');
    }
};
