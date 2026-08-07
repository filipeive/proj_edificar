<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void {
        try {
            Schema::table('user_commitments', function (Blueprint $table) {
                $table->dropForeign(['cell_id']);
            });
        } catch (\Throwable $e) {
            // Foreign key may have already been dropped
        }

        Schema::table('user_commitments', function (Blueprint $table) {
            $table->unsignedBigInteger('cell_id')->nullable()->change();
            $table->foreign('cell_id')
                ->references('id')
                ->on('cells')
                ->onDelete('set null');
        });
    }

    public function down(): void {
        try {
            Schema::table('user_commitments', function (Blueprint $table) {
                $table->dropForeign(['cell_id']);
            });
        } catch (\Throwable $e) {
        }

        Schema::table('user_commitments', function (Blueprint $table) {
            $table->unsignedBigInteger('cell_id')->nullable(false)->change();
            $table->foreign('cell_id')
                ->references('id')
                ->on('cells');
        });
    }
};
