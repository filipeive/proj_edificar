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
        Schema::table('service_zone_participations', function (Blueprint $table) {
            $table->integer('leaders')->default(0)->after('adults_visitors');
            $table->integer('supervisors')->default(0)->after('leaders');
            $table->integer('zone_pastors')->default(0)->after('supervisors');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('service_zone_participations', function (Blueprint $table) {
            $table->dropColumn(['leaders', 'supervisors', 'zone_pastors']);
        });
    }
};
