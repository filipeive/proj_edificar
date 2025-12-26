<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('quarterly_reports', function (Blueprint $table) {
            $table->id();
            $table->foreignId('zone_id')->constrained('zones')->onDelete('cascade');
            $table->foreignId('supervisor_id')->constrained('users')->onDelete('restrict');
            $table->integer('year');
            $table->tinyInteger('quarter'); // 1, 2, 3, 4

            // Estatísticas Gerais
            $table->integer('leaders_count')->default(0);
            $table->integer('cells_count')->default(0);
            $table->integer('timoteos_count')->default(0);
            $table->integer('members_count')->default(0);
            $table->integer('participants_count')->default(0);

            // Resultados Ministeriais
            $table->integer('saved_count')->default(0);
            $table->integer('planned_baptism_count')->default(0);
            $table->integer('baptized_count')->default(0);
            $table->integer('cell_multiplications_count')->default(0);
            $table->integer('disciplined_leaders_count')->default(0);
            $table->integer('closed_cells_count')->default(0);
            $table->text('ministerial_observations')->nullable();

            // Fortalezas e Fraquezas (0-3)
            $table->tinyInteger('discipleship_score')->default(0);
            $table->tinyInteger('pastoral_score')->default(0);
            $table->tinyInteger('cell_participation_score')->default(0);
            $table->tinyInteger('service_participation_score')->default(0);
            $table->tinyInteger('communion_in_cells_score')->default(0);
            $table->tinyInteger('relationship_building_score')->default(0);
            $table->tinyInteger('prayer_intercession_score')->default(0);

            $table->timestamp('submitted_at')->nullable();
            $table->enum('status', ['draft', 'submitted', 'approved'])->default('draft');

            $table->timestamps();

            $table->unique(['zone_id', 'year', 'quarter'], 'unique_zone_quarter');
            $table->index(['year', 'quarter']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('quarterly_reports');
    }
};
