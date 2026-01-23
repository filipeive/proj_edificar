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
            $table->integer('pastors_count')->default(0)->after('participants_count');
            $table->integer('supervisors_count')->default(0)->after('pastors_count');
            $table->integer('visitors_count')->default(0)->after('supervisors_count');
        });
    }

    public function down(): void
    {
        Schema::table('quarterly_reports', function (Blueprint $table) {
            $table->dropColumn(['pastors_count', 'supervisors_count', 'visitors_count']);
        });
    }
};
