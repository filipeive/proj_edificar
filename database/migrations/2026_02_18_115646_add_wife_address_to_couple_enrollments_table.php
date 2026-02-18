<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('couple_enrollments', function (Blueprint $table) {
            $table->string('wife_address')->nullable()->after('address');
        });
    }

    public function down(): void
    {
        Schema::table('couple_enrollments', function (Blueprint $table) {
            $table->dropColumn('wife_address');
        });
    }
};
