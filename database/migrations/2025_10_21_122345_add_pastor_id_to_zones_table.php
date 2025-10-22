<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('zones', function (Blueprint $table) {
            // pastor_id é nullable porque uma Zona pode ser criada antes de um Pastor ser nomeado
            $table->foreignId('pastor_id')
                  ->nullable()
                  ->after('description') // Colocamos após a descrição
                  ->constrained('users')
                  ->onDelete('set null'); // Se o usuário Pastor for deletado, o campo é setado para null
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('zones', function (Blueprint $table) {
            $table->dropForeign(['pastor_id']);
            $table->dropColumn('pastor_id');
        });
    }
};