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
        Schema::table('cell_meetings', function (Blueprint $table) {
            $table->string('biblical_text')->nullable()->after('theme');
            $table->integer('visitors_count')->default(0)->after('children_count');
            $table->text('decisions')->nullable()->after('visitors_count');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('cell_meetings', function (Blueprint $table) {
            $table->dropColumn(['biblical_text', 'visitors_count', 'decisions']);
        });
    }
};
