<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('services', function (Blueprint $table) {
            $table->id();
            $table->date('date');
            $table->enum('service_type', ['1st', '2nd', '3rd', '4th', 'special']);
            $table->foreignId('preacher_id')->nullable()->constrained('users')->onDelete('set null');
            $table->string('theme')->nullable();
            $table->text('message')->nullable();
            $table->text('observations')->nullable();
            $table->integer('adults_count')->default(0);
            $table->integer('children_count')->default(0);
            $table->timestamps();

            $table->index('date');
            $table->index('service_type');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('services');
    }
};
