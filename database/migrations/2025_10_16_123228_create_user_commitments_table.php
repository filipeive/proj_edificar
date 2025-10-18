<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void {
        Schema::create('user_commitments', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained('users')->onDelete('cascade');
            $table->foreignId('package_id')->constrained('commitment_packages')->onDelete('restrict');
            
            // Colunas de Relatório (Se você as adicionou em migrações posteriores, mantenha-as lá. Se as moveu, adicione-as aqui.)
            // $table->foreignId('cell_id')->constrained('cells'); 
            // $table->decimal('committed_amount', 10, 2); 
            
            $table->date('start_date');
            $table->date('end_date')->nullable();
            $table->timestamps();
            
            // LINHA REMOVIDA: $table->unique(['user_id', 'end_date']);
            // Essa restrição não é necessária e causa o erro SQLSTATE[23000].
            
            // Podemos adicionar um índice para melhorar o desempenho de busca
            $table->index('user_id');
        });
    }

    public function down(): void {
        Schema::dropIfExists('user_commitments');
    }
};