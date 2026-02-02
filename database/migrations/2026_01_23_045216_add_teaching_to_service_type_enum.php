<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        // SQLite não suporta ALTER TABLE ... MODIFY COLUMN ... ENUM.
        // Em SQLite o campo service_type fica como TEXT, suficiente para testes.
        if (DB::getDriverName() === 'sqlite') {
            return;
        }

        DB::statement(
            "ALTER TABLE services MODIFY COLUMN service_type ENUM('1st', '2nd', '3rd', '4th', 'special', 'teaching')"
        );
    }

    public function down(): void
    {
        if (DB::getDriverName() === 'sqlite') {
            return;
        }

        DB::statement(
            "ALTER TABLE services MODIFY COLUMN service_type ENUM('1st', '2nd', '3rd', '4th', 'special')"
        );
    }
};
