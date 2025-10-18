<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB; 

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('user_commitments', function (Blueprint $table) {
            // 1. Adicionar committed_amount (sem problemas)
            $table->decimal('committed_amount', 10, 2)->nullable()->after('package_id'); 
            
            // 2. Adicionar cell_id como nulo TEMPORARIAMENTE para não falhar a FK
            // Usamos 'unsignedBigInteger' em vez de foreignId()
            $table->unsignedBigInteger('cell_id')->nullable()->after('user_id'); 
        });
        // NOTE: Isso deve ser feito com cuidado. Se o usuário não tiver célula, use um valor padrão (ex: 1)
        DB::statement("
            UPDATE user_commitments uc
            JOIN users u ON uc.user_id = u.id
            SET uc.cell_id = u.cell_id, 
                uc.committed_amount = (
                    SELECT cp.min_amount 
                    FROM commitment_packages cp 
                    WHERE cp.id = uc.package_id
                );
        ");
        
        // 4. Adicionar Restrição NOT NULL e Chave Estrangeira
        Schema::table('user_commitments', function (Blueprint $table) {
            // Alterar committed_amount para NOT NULL (se for a regra de negócio)
            $table->decimal('committed_amount', 10, 2)->change();
            
            // Alterar cell_id para NOT NULL e adicionar a FK
            $table->unsignedBigInteger('cell_id')->nullable(false)->change(); // Torna NOT NULL
            $table->foreign('cell_id')->references('id')->on('cells'); // Adiciona a chave estrangeira
        });
    }

    public function down(): void
    {
        Schema::table('user_commitments', function (Blueprint $table) {
            $table->dropForeign(['cell_id']);
            $table->dropColumn(['cell_id', 'committed_amount']);
        });
    }
};