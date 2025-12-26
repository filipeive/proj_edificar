<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('events', function (Blueprint $table) {
            $table->id();
            $table->foreignId('event_type_id')->constrained('event_types')->onDelete('restrict');
            $table->string('name', 255);
            $table->date('date');
            $table->foreignId('zone_id')->nullable()->constrained('zones')->onDelete('set null');
            $table->foreignId('cell_id')->nullable()->constrained('cells')->onDelete('set null');
            $table->integer('participants_count')->default(0);
            $table->text('description')->nullable();
            $table->text('observations')->nullable();
            $table->timestamps();

            $table->index('date');
            $table->index('zone_id');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('events');
    }
};
