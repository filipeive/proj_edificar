<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // Add comissao_obra and responsavel_pacote to the ENUM list
        // We must include all existing roles + new ones
        DB::statement("ALTER TABLE users MODIFY COLUMN role ENUM('membro', 'lider_celula', 'supervisor', 'pastor_zona', 'admin', 'secretaria', 'tesouraria', 'pastor', 'pastor_senior', 'comissao_obra', 'responsavel_pacote') DEFAULT 'membro'");
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Revert to the previous list (removing new roles could be risky if data exists, but this is the logical reverse)
        DB::statement("ALTER TABLE users MODIFY COLUMN role ENUM('membro', 'lider_celula', 'supervisor', 'pastor_zona', 'admin', 'secretaria', 'tesouraria', 'pastor', 'pastor_senior') DEFAULT 'membro'");
    }
};
