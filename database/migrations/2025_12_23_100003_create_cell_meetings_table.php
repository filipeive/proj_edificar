<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('cell_meetings', function (Blueprint $table) {
            $table->id();
            $table->foreignId('cell_id')->constrained('cells')->onDelete('cascade');
            $table->date('meeting_date');
            $table->string('theme')->nullable();
            $table->foreignId('leader_id')->nullable()->constrained('users')->onDelete('set null');
            $table->integer('adults_count')->default(0);
            $table->integer('children_count')->default(0);
            $table->text('observations')->nullable();
            $table->timestamps();

            $table->index(['cell_id', 'meeting_date']);
            $table->unique(['cell_id', 'meeting_date'], 'unique_cell_meeting');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('cell_meetings');
    }
};
