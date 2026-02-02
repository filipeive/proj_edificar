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
        Schema::table('zones', function (Blueprint $row) {
            $row->boolean('show_in_teaching_services')->default(true)->after('is_active');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('zones', function (Blueprint $row) {
            $row->dropColumn('show_in_teaching_services');
        });
    }
};
