<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration {
    public function up(): void
    {
        // MySQL doesn't support direct ENUM modification easily without raw SQL or recreating the column
        DB::statement("ALTER TABLE services MODIFY COLUMN service_type ENUM('1st', '2nd', '3rd', '4th', 'special', 'teaching')");
    }

    public function down(): void
    {
        DB::statement("ALTER TABLE services MODIFY COLUMN service_type ENUM('1st', '2nd', '3rd', '4th', 'special')");
    }
};
