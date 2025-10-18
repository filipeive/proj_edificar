<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void {
        Schema::create('commitment_packages', function (Blueprint $table) {
            $table->id();
            $table->string('name')->unique(); // Pacote 1, Pacote 2, etc
            $table->decimal('min_amount', 10, 2);
            $table->decimal('max_amount', 10, 2)->nullable(); // NULL = sem limite
            $table->text('description')->nullable();
            $table->integer('order')->default(0);
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });
    }

    public function down(): void {
        Schema::dropIfExists('commitment_packages');
    }
};