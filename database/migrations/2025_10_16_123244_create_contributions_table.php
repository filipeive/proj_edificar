<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void {
        Schema::create('contributions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained('users')->onDelete('cascade');
            $table->foreignId('cell_id')->constrained('cells')->onDelete('restrict');
            $table->foreignId('supervision_id')->constrained('supervisions')->onDelete('restrict');
            $table->foreignId('zone_id')->constrained('zones')->onDelete('restrict');
            $table->decimal('amount', 10, 2);
            $table->date('contribution_date'); // Data real da contribuição
            $table->string('proof_path')->nullable(); // Caminho do arquivo (PDF/Imagem)
            $table->enum('status', ['pendente', 'verificada', 'rejeitada'])->default('pendente');
            $table->foreignId('registered_by_id')->nullable()->constrained('users')->onDelete('set null');
            $table->foreignId('verified_by_id')->nullable()->constrained('users')->onDelete('set null');
            $table->text('notes')->nullable(); // Observações do verificador
            $table->timestamps();
            
            // Índices para queries rápidas
            $table->index('user_id');
            $table->index('cell_id');
            $table->index('zone_id');
            $table->index('contribution_date');
            $table->index('status');
        });
    }

    public function down(): void {
        Schema::dropIfExists('contributions');
    }
};