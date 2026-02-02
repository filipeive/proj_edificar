<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('couple_enrollments', function (Blueprint $table) {
            $table->boolean('is_church_member')->default(true)->after('has_pastoral_recommendation');
        });
    }

    public function down(): void
    {
        Schema::table('couple_enrollments', function (Blueprint $table) {
            $table->dropColumn('is_church_member');
        });
    }
};
