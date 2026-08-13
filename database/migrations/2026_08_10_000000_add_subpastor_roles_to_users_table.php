<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Adiciona os roles "subpastor_zona" e "subpastor" ao enum de users.role,
     * seguindo o padrão do role "sub_supervisor" já existente.
     * Estes roles representam as subcategorias que permitem a "subida de nível"
     * entre células (ex.: supervisor+subpastor_zona -> célula de pastores de zona).
     *
     * Run the migrations.
     */
    public function up(): void
    {
        DB::statement("ALTER TABLE users MODIFY COLUMN role
            ENUM('membro','lider_celula','supervisor','sub_supervisor','pastor_zona','admin','super_admin','secretaria','tesouraria','pastor','pastor_senior','comissao_obra','responsavel_pacote','timoteo','administracao','subpastor_zona','subpastor')
            DEFAULT 'membro'");
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        DB::statement("ALTER TABLE users MODIFY COLUMN role
            ENUM('membro','lider_celula','supervisor','sub_supervisor','pastor_zona','admin','super_admin','secretaria','tesouraria','pastor','pastor_senior','comissao_obra','responsavel_pacote','timoteo','administracao')
            DEFAULT 'membro'");
    }
};
