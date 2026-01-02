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
            $table->foreignId('supervision_id')->after('zone_id')->nullable()->constrained('supervisions')->onDelete('cascade');

            // Drop FK and unique constraint
            $table->dropForeign(['zone_id']);
            $table->dropUnique('unique_zone_quarter');

            // Add new unique constraint
            $table->unique(['supervision_id', 'year', 'quarter'], 'unique_supervision_quarter');

            // Re-add FK
            $table->foreign('zone_id')->references('id')->on('zones')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('quarterly_reports', function (Blueprint $table) {
            $table->dropUnique('unique_supervision_quarter');
            $table->unique(['zone_id', 'year', 'quarter'], 'unique_zone_quarter');
            $table->dropForeign(['supervision_id']);
            $table->dropColumn('supervision_id');
        });
    }
};
