<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        // SQLite não suporta ALTER TABLE ... MODIFY COLUMN ... ENUM.
        // Em SQLite, o campo role fica como TEXT (sem constraint), o que é suficiente para testes.
        if (DB::getDriverName() === 'sqlite') {
            return;
        }

        DB::statement(
            "ALTER TABLE users MODIFY COLUMN role ENUM('membro', 'lider_celula', 'supervisor', 'pastor_zona', 'admin', 'secretaria', 'tesouraria', 'pastor', 'pastor_senior') DEFAULT 'membro'"
        );
    }

    public function down(): void
    {
        if (DB::getDriverName() === 'sqlite') {
            return;
        }

        DB::statement(
            "ALTER TABLE users MODIFY COLUMN role ENUM('membro', 'lider_celula', 'supervisor', 'pastor_zona', 'admin') DEFAULT 'membro'"
        );
    }
};
