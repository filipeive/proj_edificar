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
            $table->integer('evangelism_strategy')->default(2)->after('discipleship_score');
            $table->integer('consolidation_growth')->default(2)->after('evangelism_strategy');
            $table->integer('visitation_routine')->default(2)->after('pastoral_score');
            $table->integer('leader_support')->default(2)->after('visitation_routine');
            $table->integer('tadium_participation')->default(2)->after('service_participation_score');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('quarterly_reports', function (Blueprint $table) {
            $table->dropColumn([
                'evangelism_strategy',
                'consolidation_growth',
                'visitation_routine',
                'leader_support',
                'tadium_participation'
            ]);
        });
    }
};
