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
        Schema::create('conversions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('cell_id')->constrained()->onDelete('cascade');
            $table->string('name');
            $table->date('date');
            $table->boolean('is_new_salvation')->default(false);
            $table->boolean('is_water_baptism_candidate')->default(false);
            $table->boolean('is_holy_spirit_baptism_candidate')->default(false);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('conversions');
    }
};
