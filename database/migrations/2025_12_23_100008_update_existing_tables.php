<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        // Update zones
        Schema::table('zones', function (Blueprint $table) {
            if (!Schema::hasColumn('zones', 'is_active')) {
                $table->boolean('is_active')->default(true)->after('description');
            }
        });

        // Update supervisions
        Schema::table('supervisions', function (Blueprint $table) {
            if (!Schema::hasColumn('supervisions', 'supervisor_id')) {
                $table->foreignId('supervisor_id')->nullable()->after('zone_id')->constrained('users')->onDelete('set null');
            }
            if (!Schema::hasColumn('supervisions', 'is_active')) {
                $table->boolean('is_active')->default(true)->after('description');
            }
        });

        // Update cells
        Schema::table('cells', function (Blueprint $table) {
            if (!Schema::hasColumn('cells', 'timoteo_id')) {
                $table->foreignId('timoteo_id')->nullable()->after('leader_id')->constrained('users')->onDelete('set null');
            }
            if (!Schema::hasColumn('cells', 'is_active')) {
                $table->boolean('is_active')->default(true)->after('member_count');
            }
        });

        // Update contributions
        Schema::table('contributions', function (Blueprint $table) {
            if (!Schema::hasColumn('contributions', 'package_id')) {
                $table->foreignId('package_id')->nullable()->after('zone_id')->constrained('commitment_packages')->onDelete('set null');
            }
            if (!Schema::hasColumn('contributions', 'offering_type_id')) {
                $table->foreignId('offering_type_id')->nullable()->after('amount')->constrained('offering_types')->onDelete('set null');
            }
        });
    }

    public function down(): void
    {
        Schema::table('contributions', function (Blueprint $table) {
            $table->dropForeign(['offering_type_id']);
            $table->dropColumn('offering_type_id');
            if (Schema::hasColumn('contributions', 'package_id')) {
                $table->dropForeign(['package_id']);
                $table->dropColumn('package_id');
            }
        });

        Schema::table('cells', function (Blueprint $table) {
            $table->dropForeign(['timoteo_id']);
            $table->dropColumn(['timoteo_id', 'is_active']);
        });

        Schema::table('supervisions', function (Blueprint $table) {
            $table->dropForeign(['supervisor_id']);
            $table->dropColumn(['supervisor_id', 'is_active']);
        });

        Schema::table('zones', function (Blueprint $table) {
            $table->dropColumn('is_active');
        });
    }
};
