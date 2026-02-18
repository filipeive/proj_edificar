<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('couple_enrollments', function (Blueprint $table) {
            $table->string('husband_phone', 30)->nullable()->after('contacts');
            $table->string('wife_phone', 30)->nullable()->after('husband_phone');
        });
    }

    public function down(): void
    {
        Schema::table('couple_enrollments', function (Blueprint $table) {
            $table->dropColumn(['husband_phone', 'wife_phone']);
        });
    }
};
