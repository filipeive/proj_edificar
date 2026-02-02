<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        // SQLite não suporta ALTER TABLE ... MODIFY COLUMN ... ENUM.
        // Em SQLite (testes) o campo role fica como TEXT, sem constraint.
        if (DB::getDriverName() === 'sqlite') {
            return;
        }

        // Add comissao_obra and responsavel_pacote to the ENUM list.
        DB::statement(
            "ALTER TABLE users MODIFY COLUMN role ENUM('membro', 'lider_celula', 'supervisor', 'pastor_zona', 'admin', 'secretaria', 'tesouraria', 'pastor', 'pastor_senior', 'comissao_obra', 'responsavel_pacote') DEFAULT 'membro'"
        );
    }

    public function down(): void
    {
        if (DB::getDriverName() === 'sqlite') {
            return;
        }

        DB::statement(
            "ALTER TABLE users MODIFY COLUMN role ENUM('membro', 'lider_celula', 'supervisor', 'pastor_zona', 'admin', 'secretaria', 'tesouraria', 'pastor', 'pastor_senior') DEFAULT 'membro'"
        );
    }
};
