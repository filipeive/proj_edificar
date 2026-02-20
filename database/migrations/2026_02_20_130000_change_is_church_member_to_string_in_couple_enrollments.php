<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        // 1. Add temporary string column
        Schema::table('couple_enrollments', function (Blueprint $table) {
            $table->string('is_church_member_new', 10)->default('both')->after('is_church_member');
        });

        // 2. Migrate existing data: true(1) -> 'both', false(0) -> 'none'
        DB::table('couple_enrollments')
            ->where('is_church_member', 1)
            ->update(['is_church_member_new' => 'both']);

        DB::table('couple_enrollments')
            ->where('is_church_member', 0)
            ->update(['is_church_member_new' => 'none']);

        // 3. Drop old boolean column, rename new one
        Schema::table('couple_enrollments', function (Blueprint $table) {
            $table->dropColumn('is_church_member');
        });

        Schema::table('couple_enrollments', function (Blueprint $table) {
            $table->renameColumn('is_church_member_new', 'is_church_member');
        });
    }

    public function down(): void
    {
        Schema::table('couple_enrollments', function (Blueprint $table) {
            $table->boolean('is_church_member_old')->default(true)->after('is_church_member');
        });

        DB::table('couple_enrollments')
            ->where('is_church_member', 'both')
            ->orWhere('is_church_member', 'one')
            ->update(['is_church_member_old' => true]);

        DB::table('couple_enrollments')
            ->where('is_church_member', 'none')
            ->update(['is_church_member_old' => false]);

        Schema::table('couple_enrollments', function (Blueprint $table) {
            $table->dropColumn('is_church_member');
        });

        Schema::table('couple_enrollments', function (Blueprint $table) {
            $table->renameColumn('is_church_member_old', 'is_church_member');
        });
    }
};
