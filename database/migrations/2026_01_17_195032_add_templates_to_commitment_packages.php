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
            $table->text('sms_template')->nullable()->after('whatsapp_link');
            $table->text('whatsapp_template')->nullable()->after('sms_template');
        });
    }

    public function down(): void
    {
        Schema::table('commitment_packages', function (Blueprint $table) {
            $table->dropColumn(['sms_template', 'whatsapp_template']);
        });
    }
};
