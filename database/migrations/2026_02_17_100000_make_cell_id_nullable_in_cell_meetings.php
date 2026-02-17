<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('cell_meetings', function (Blueprint $table) {
            // Drop the unique constraint that forces cell_id to be non-null
            $table->dropUnique('unique_cell_meeting');

            // Make cell_id nullable for non-cell meetings (supervision, zone, leadership, etc.)
            $table->foreignId('cell_id')->nullable()->change();
        });
    }

    public function down(): void
    {
        Schema::table('cell_meetings', function (Blueprint $table) {
            $table->foreignId('cell_id')->nullable(false)->change();
            $table->unique(['cell_id', 'meeting_date'], 'unique_cell_meeting');
        });
    }
};
