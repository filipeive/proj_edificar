<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void {
        Schema::table('users', function (Blueprint $table) {
            // Adicionar campos após 'email'
            $table->string('phone')->nullable()->after('email');
            $table->enum('role', ['membro', 'lider_celula', 'supervisor', 'pastor_zona', 'admin'])->default('membro')->after('password');
            $table->foreignId('cell_id')->nullable()->constrained('cells')->onDelete('set null')->after('role');
            $table->boolean('is_active')->default(true)->after('cell_id');
        });
    }

    public function down(): void {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn(['phone', 'role', 'cell_id', 'is_active']);
        });
    }
};
