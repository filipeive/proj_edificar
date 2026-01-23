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
        Schema::table('offering_types', function (Blueprint $table) {
            $table->string('scope')->default('eclesiastico')->after('order');
        });

        Schema::table('expenses', function (Blueprint $table) {
            $table->string('scope')->default('eclesiastico')->after('amount');
        });

        Schema::table('requisitions', function (Blueprint $table) {
            $table->string('scope')->default('eclesiastico')->after('amount');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('offering_types', function (Blueprint $table) {
            $table->dropColumn('scope');
        });

        Schema::table('expenses', function (Blueprint $table) {
            $table->dropColumn('scope');
        });

        Schema::table('requisitions', function (Blueprint $table) {
            $table->dropColumn('scope');
        });
    }
};
