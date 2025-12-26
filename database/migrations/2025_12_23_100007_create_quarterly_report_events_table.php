<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('quarterly_report_events', function (Blueprint $table) {
            $table->id();
            $table->foreignId('quarterly_report_id')->constrained('quarterly_reports')->onDelete('cascade');
            $table->foreignId('event_type_id')->constrained('event_types')->onDelete('restrict');
            $table->integer('count')->default(0);
            $table->text('description')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('quarterly_report_events');
    }
};
