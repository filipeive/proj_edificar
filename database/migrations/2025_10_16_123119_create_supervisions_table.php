<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void {
        Schema::create('supervisions', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->foreignId('zone_id')->constrained('zones')->onDelete('cascade');
            $table->text('description')->nullable();
            $table->timestamps();
            
            $table->unique(['name', 'zone_id']);
        });
    }

    public function down(): void {
        Schema::dropIfExists('supervisions');
    }
};