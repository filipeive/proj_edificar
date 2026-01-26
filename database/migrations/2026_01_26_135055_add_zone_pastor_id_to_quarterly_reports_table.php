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
        Schema::table('quarterly_reports', function (Blueprint $table) {
            $table->foreignId('zone_pastor_id')->nullable()->after('supervisor_id')->constrained('users')->onDelete('set null');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('quarterly_reports', function (Blueprint $table) {
            $table->dropForeign(['zone_pastor_id']);
            $table->dropColumn('zone_pastor_id');
        });
    }
};
