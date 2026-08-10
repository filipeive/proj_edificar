<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->string('baptism_status')->default('not_baptized')->after('observations');
            $table->date('baptism_decision_date')->nullable()->after('baptism_status');
            $table->date('baptism_date')->nullable()->after('baptism_decision_date');
            $table->text('baptism_notes')->nullable()->after('baptism_date');
        });
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn([
                'baptism_status',
                'baptism_decision_date',
                'baptism_date',
                'baptism_notes',
            ]);
        });
    }
};
