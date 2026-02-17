<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('cell_meetings', function (Blueprint $table) {
            $table->foreignId('zone_id')->nullable()->after('cell_id')->constrained('zones')->nullOnDelete();
            $table->foreignId('supervision_id')->nullable()->after('zone_id')->constrained('supervisions')->nullOnDelete();
        });
    }

    public function down(): void
    {
        Schema::table('cell_meetings', function (Blueprint $table) {
            $table->dropForeign(['zone_id']);
            $table->dropForeign(['supervision_id']);
            $table->dropColumn(['zone_id', 'supervision_id']);
        });
    }
};
