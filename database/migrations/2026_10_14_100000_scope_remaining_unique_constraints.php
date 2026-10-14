<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('commitment_packages', function (Blueprint $table) {
            $table->dropUnique('commitment_packages_name_unique');
            $table->unique(['congregation_id', 'name'], 'packages_congregation_name_unique');
        });

        Schema::table('event_types', function (Blueprint $table) {
            $table->dropUnique('event_types_code_unique');
            $table->unique(['congregation_id', 'code'], 'event_types_congregation_code_unique');
        });
    }

    public function down(): void
    {
        if (Schema::hasTable('congregations') && \Illuminate\Support\Facades\DB::table('congregations')->where('is_primary', false)->exists()) {
            throw new RuntimeException('Não é seguro reverter constraints tenant depois de criar congregações adicionais.');
        }

        Schema::table('commitment_packages', function (Blueprint $table) {
            $table->dropUnique('packages_congregation_name_unique');
            $table->unique('name');
        });

        Schema::table('event_types', function (Blueprint $table) {
            $table->dropUnique('event_types_congregation_code_unique');
            $table->unique('code');
        });
    }
};