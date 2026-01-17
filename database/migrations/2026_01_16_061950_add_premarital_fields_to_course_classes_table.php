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
        Schema::table('course_classes', function (Blueprint $table) {
            // Tipo de turma
            $table->enum('type', ['casais_vivendo', 'pre_nupcial'])->default('casais_vivendo')->after('name');

            // Professores (renomear leaders para teachers)
            $table->renameColumn('leader_husband_id', 'teacher_male_id');
            $table->renameColumn('leader_wife_id', 'teacher_female_id');

            // Auxiliares
            $table->foreignId('assistant_male_id')->nullable()->after('teacher_female_id')->constrained('users')->nullOnDelete();
            $table->foreignId('assistant_female_id')->nullable()->after('assistant_male_id')->constrained('users')->nullOnDelete();

            // Observações
            $table->text('notes')->nullable()->after('end_date');

            // Criado por
            $table->foreignId('created_by')->nullable()->after('notes')->constrained('users')->nullOnDelete();

            // Atualizar enum status
            $table->dropColumn('status');
        });

        Schema::table('course_classes', function (Blueprint $table) {
            $table->enum('status', ['em_andamento', 'concluida', 'cancelada'])->default('em_andamento')->after('type');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('course_classes', function (Blueprint $table) {
            $table->dropColumn(['type', 'assistant_male_id', 'assistant_female_id', 'notes', 'created_by']);
            $table->renameColumn('teacher_male_id', 'leader_husband_id');
            $table->renameColumn('teacher_female_id', 'leader_wife_id');
            $table->dropColumn('status');
        });

        Schema::table('course_classes', function (Blueprint $table) {
            $table->enum('status', ['active', 'completed', 'cancelled'])->default('active');
        });
    }
};
