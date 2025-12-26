<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('service_offerings', function (Blueprint $table) {
            $table->id();
            $table->foreignId('service_id')->constrained('services')->onDelete('cascade');
            $table->foreignId('offering_type_id')->constrained('offering_types')->onDelete('restrict');
            $table->decimal('amount', 10, 2)->default(0);
            $table->text('notes')->nullable();
            $table->timestamps();

            $table->index(['service_id', 'offering_type_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('service_offerings');
    }
};
