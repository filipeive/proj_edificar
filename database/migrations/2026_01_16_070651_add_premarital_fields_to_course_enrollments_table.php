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
        Schema::table('course_enrollments', function (Blueprint $table) {
            // Tornar user_id opcional (usaremos male/female partner)
            $table->foreignId('user_id')->nullable()->change();

            // Novos campos para casal
            $table->foreignId('male_partner_id')->nullable()->after('course_class_id')->constrained('users')->nullOnDelete();
            $table->foreignId('female_partner_id')->nullable()->after('male_partner_id')->constrained('users')->nullOnDelete();

            // Informações
            $table->boolean('is_church_member')->default(true)->after('female_partner_id');

            // Frequência
            $table->integer('attendance_count')->default(0)->after('is_church_member');
            $table->integer('absence_count')->default(0)->after('attendance_count');
            $table->text('absence_reasons')->nullable()->after('absence_count');

            // Casamento
            $table->date('wedding_date')->nullable()->after('status');
            $table->date('engagement_date')->nullable()->after('wedding_date');
            $table->string('godparents_male')->nullable()->after('engagement_date');
            $table->string('godparents_female')->nullable()->after('godparents_male');

            // Avaliação
            $table->integer('completed_pillars')->default(0)->after('godparents_female');
            $table->text('recommendation')->nullable()->after('completed_pillars');
            $table->text('notes')->nullable()->after('recommendation');

            // Atualizar enum status para incluir os novos valores
            $table->dropColumn('status');
        });

        Schema::table('course_enrollments', function (Blueprint $table) {
            $table->enum('status', ['cursando', 'aprovado', 'reprovado', 'desistente'])
                ->default('cursando')
                ->after('female_partner_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('course_enrollments', function (Blueprint $table) {
            $table->dropForeign(['male_partner_id']);
            $table->dropForeign(['female_partner_id']);
            $table->dropColumn([
                'male_partner_id',
                'female_partner_id',
                'is_church_member',
                'attendance_count',
                'absence_count',
                'absence_reasons',
                'wedding_date',
                'engagement_date',
                'godparents_male',
                'godparents_female',
                'completed_pillars',
                'recommendation',
                'status',
                'notes'
            ]);
        });

        Schema::table('course_enrollments', function (Blueprint $table) {
            $table->string('status')->default('enrolled')->after('user_id');
        });
    }
};
