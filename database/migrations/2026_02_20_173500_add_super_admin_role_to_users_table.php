<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        if (DB::getDriverName() === 'sqlite') {
            return;
        }

        DB::statement("ALTER TABLE users MODIFY COLUMN role 
            ENUM('membro','lider_celula','supervisor','sub_supervisor','pastor_zona','admin','super_admin','secretaria','tesouraria','pastor','pastor_senior','comissao_obra','responsavel_pacote','timoteo','administracao') 
            DEFAULT 'membro'");
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        if (DB::getDriverName() === 'sqlite') {
            return;
        }

        DB::statement("ALTER TABLE users MODIFY COLUMN role 
            ENUM('membro','lider_celula','supervisor','sub_supervisor','pastor_zona','admin','secretaria','tesouraria','pastor','pastor_senior','comissao_obra','responsavel_pacote','timoteo','administracao') 
            DEFAULT 'membro'");
    }
};

