<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('services', function (Blueprint $table) {
            // Removendo colunas antigas (serão substituídas pelo detalhamento)
            $table->dropColumn(['adults_count', 'children_count']);

            // Novo detalhamento de participação (Adultos)
            $table->integer('adults_members')->default(0);
            $table->integer('adults_visitors')->default(0);
            $table->integer('adults_salvations')->default(0);

            // Novo detalhamento de participação (Crianças)
            $table->integer('children_members')->default(0);
            $table->integer('children_visitors')->default(0);
            $table->integer('children_salvations')->default(0);

            // Campos financeiros adicionais
            $table->decimal('special_offerings_total', 15, 2)->default(0);
        });
    }

    public function down(): void
    {
        Schema::table('services', function (Blueprint $table) {
            $table->integer('adults_count')->default(0);
            $table->integer('children_count')->default(0);

            $table->dropColumn([
                'adults_members',
                'adults_visitors',
                'adults_salvations',
                'children_members',
                'children_visitors',
                'children_salvations',
                'special_offerings_total'
            ]);
        });
    }
};
