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
        Schema::table('commitment_packages', function (Blueprint $table) {
            $table->string('whatsapp_link')->nullable()->after('description');
        });
    }

    public function down(): void
    {
        Schema::table('commitment_packages', function (Blueprint $table) {
            $table->dropColumn('whatsapp_link');
        });
    }
};
