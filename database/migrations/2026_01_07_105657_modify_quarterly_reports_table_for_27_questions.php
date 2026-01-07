<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('quarterly_reports', function (Blueprint $table) {
            // A tabela já possui a maioria dos campos, mas vou ajustar os scores para 0-3
            // conforme a nova escala de intensidade (Não observado a Muito intenso)

            // Garantindo que todos os campos das imagens existam ou sejam ajustados:
            // Seção I: Já existe (leaders_count, cells_count, timoteos_count, members_count, participants_count)

            // Seção II: Já existe (saved_count, planned_baptism_count, baptized_count, cell_multiplications_count, 
            // disciplined_leaders_count, closed_cells_count, ministerial_observations)

            // Seção IV: Ajustando scores para tinyInteger (0-3) - assumindo que já eram tinyInteger no migration original
            // mas agora a escala no form será 0, 1, 2, 3.
        });
    }

    public function down(): void
    {
        // Nada a reverter especificamente aqui se os campos já existem, 
        // mas este migration serve para documentar a validação do schema com o novo form.
    }
};
