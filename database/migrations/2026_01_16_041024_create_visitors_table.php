<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('visitors', function (Blueprint $table) {
            $table->id();

            // Informações Básicas (da ficha)
            $table->string('name');
            $table->integer('age')->nullable();
            $table->enum('gender', ['masculino', 'feminino'])->nullable();
            $table->string('neighborhood')->nullable(); // Bairro
            $table->string('city')->default('Maputo');
            $table->string('phone')->nullable();

            // Convite
            $table->boolean('invited_by_someone')->default(false);
            $table->string('inviter_name')->nullable(); // Nome de quem convidou

            // Data da visita
            $table->date('visit_date');

            // Atribuição e Acompanhamento
            $table->foreignId('zone_id')->nullable()->constrained('zones')->nullOnDelete();
            $table->foreignId('cell_id')->nullable()->constrained('cells')->nullOnDelete();

            // Status de contato
            $table->enum('contact_status', ['pendente', 'contatado', 'integrado', 'sem_interesse'])
                ->default('pendente');
            $table->datetime('contacted_at')->nullable();
            $table->foreignId('contacted_by')->nullable()->constrained('users')->nullOnDelete();

            // Observações
            $table->text('notes')->nullable();

            // Auditoria
            $table->foreignId('created_by')->constrained('users')->cascadeOnDelete();
            $table->timestamps();

            // Índices para performance
            $table->index('visit_date');
            $table->index('contact_status');
            $table->index(['zone_id', 'contact_status']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('visitors');
    }
};
