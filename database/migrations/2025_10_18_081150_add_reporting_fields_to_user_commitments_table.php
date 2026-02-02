<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('user_commitments', function (Blueprint $table) {
            $table->decimal('committed_amount', 10, 2)->nullable()->after('package_id');

            // Adicionamos como nullable primeiro para não falhar com FK/dados existentes.
            $table->unsignedBigInteger('cell_id')->nullable()->after('user_id');
        });

        // Popular dados existentes (compatível com MySQL e SQLite)
        if (DB::getDriverName() === 'sqlite') {
            // SQLite não suporta UPDATE ... JOIN nem alias no UPDATE.
            DB::statement(<<<'SQL'
                UPDATE user_commitments
                SET
                    cell_id = (
                        SELECT cell_id
                        FROM users
                        WHERE users.id = user_commitments.user_id
                    ),
                    committed_amount = (
                        SELECT min_amount
                        FROM commitment_packages
                        WHERE commitment_packages.id = user_commitments.package_id
                    )
            SQL);
        } else {
            DB::statement(<<<'SQL'
                UPDATE user_commitments uc
                JOIN users u ON uc.user_id = u.id
                SET uc.cell_id = u.cell_id,
                    uc.committed_amount = (
                        SELECT cp.min_amount
                        FROM commitment_packages cp
                        WHERE cp.id = uc.package_id
                    )
            SQL);
        }

        Schema::table('user_commitments', function (Blueprint $table) {
            $table->decimal('committed_amount', 10, 2)->change();
            $table->unsignedBigInteger('cell_id')->nullable(false)->change();
            $table->foreign('cell_id')->references('id')->on('cells');
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
