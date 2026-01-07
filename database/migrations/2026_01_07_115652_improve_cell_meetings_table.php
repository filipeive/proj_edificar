<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('cell_meetings', function (Blueprint $table) {
            $table->string('meeting_type')->default('normal')->after('leader_id'); // normal, leadership, supervision, zone
            $table->longText('minutes')->nullable()->after('observations'); // Ata do encontro
        });
    }

    public function down(): void
    {
        Schema::table('cell_meetings', function (Blueprint $table) {
            $table->dropColumn(['meeting_type', 'minutes']);
        });
    }
};
