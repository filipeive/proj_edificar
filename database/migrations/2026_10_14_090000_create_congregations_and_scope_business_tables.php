<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    private array $tables = [
        'users', 'zones', 'supervisions', 'cells', 'commitment_packages', 'user_commitments', 'contributions',
        'offering_types', 'services', 'service_offerings', 'cell_meetings', 'cell_meeting_participants',
        'event_types', 'events', 'quarterly_reports', 'quarterly_report_events', 'courses', 'course_enrollments',
        'couple_enrollments', 'attendances', 'cell_visitors', 'discipleships', 'conversions', 'weddings',
        'course_classes', 'course_class_meetings', 'course_class_attendance', 'service_tithes', 'visitors', 'settings',
        'expenses', 'requisitions', 'ministerial_enrollments', 'inventory_items', 'service_zone_participations',
        'service_individual_offerings', 'user_activities',
    ];

    public function up(): void
    {
        Schema::create('congregations', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('slug')->unique();
            $table->string('domain')->nullable()->unique();
            $table->string('subdomain')->nullable()->unique();
            $table->boolean('is_active')->default(true);
            $table->boolean('is_primary')->default(false);
            $table->json('settings')->nullable();
            $table->timestamps();
        });

        $primaryId = DB::table('congregations')->insertGetId([
            'name' => 'Life Church',
            'slug' => 'life-church',
            'is_active' => true,
            'is_primary' => true,
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        foreach ($this->tables as $tableName) {
            if (!Schema::hasTable($tableName)) {
                continue;
            }

            Schema::table($tableName, function (Blueprint $table) {
                $table->unsignedBigInteger('congregation_id')->nullable()->index();
            });

            DB::table($tableName)->whereNull('congregation_id')->update(['congregation_id' => $primaryId]);

            Schema::table($tableName, function (Blueprint $table) use ($tableName) {
                $table->unsignedBigInteger('congregation_id')->nullable(false)->change();
                $table->foreign('congregation_id', 'fk_' . $tableName . '_congregation')
                    ->references('id')->on('congregations')->restrictOnDelete();
            });
        }

        Schema::table('zones', function (Blueprint $table) {
            $table->dropUnique('zones_name_unique');
            $table->unique(['congregation_id', 'name'], 'zones_congregation_name_unique');
        });

        Schema::table('courses', function (Blueprint $table) {
            $table->dropUnique('courses_slug_unique');
            $table->unique(['congregation_id', 'slug'], 'courses_congregation_slug_unique');
        });

        Schema::table('offering_types', function (Blueprint $table) {
            $table->dropUnique('offering_types_code_unique');
            $table->unique(['congregation_id', 'code'], 'offering_types_congregation_code_unique');
        });
    }

    public function down(): void
    {
        if (DB::table('congregations')->where('is_primary', false)->exists()) {
            throw new RuntimeException('Não é seguro reverter tenancy depois de criar congregações adicionais.');
        }

        Schema::table('zones', function (Blueprint $table) {
            $table->dropUnique('zones_congregation_name_unique');
            $table->unique('name');
        });
        Schema::table('courses', function (Blueprint $table) {
            $table->dropUnique('courses_congregation_slug_unique');
            $table->unique('slug');
        });
        Schema::table('offering_types', function (Blueprint $table) {
            $table->dropUnique('offering_types_congregation_code_unique');
            $table->unique('code');
        });

        foreach (array_reverse($this->tables) as $tableName) {
            if (!Schema::hasTable($tableName) || !Schema::hasColumn($tableName, 'congregation_id')) {
                continue;
            }

            Schema::table($tableName, function (Blueprint $table) use ($tableName) {
                $table->dropForeign('fk_' . $tableName . '_congregation');
                $table->dropIndex($tableName . '_congregation_id_index');
                $table->dropColumn('congregation_id');
            });
        }

        Schema::dropIfExists('congregations');
    }
};