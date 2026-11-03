<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('sms_messages', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('congregation_id')->nullable()->index();
            $table->unsignedBigInteger('user_id')->nullable()->index(); // autor do envio (null se sistema)
            $table->string('phone', 30)->index();
            $table->string('recipient_name')->nullable();
            $table->string('recipient_type', 50)->nullable()->index(); // pastor, supervisor, lider, membro, visitante, custom
            $table->unsignedBigInteger('recipient_id')->nullable()->index();
            $table->text('message');
            $table->unsignedSmallInteger('characters_count')->default(0);
            $table->unsignedTinyInteger('parts_count')->default(1);
            $table->string('driver', 30)->default('httpsms');
            $table->string('status', 30)->default('pending')->index(); // sent, failed, pending
            $table->string('context', 60)->nullable()->index(); // visitor_network_pastor, visitor_network_supervisor, visitor_network_leader, visitor_welcome, broadcast, package_reminder, custom
            $table->string('reference_type')->nullable();
            $table->unsignedBigInteger('reference_id')->nullable();
            $table->json('response_payload')->nullable();
            $table->text('error_message')->nullable();
            $table->timestamp('sent_at')->nullable();
            $table->timestamps();

            $table->index(['reference_type', 'reference_id']);
            $table->index('created_at');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('sms_messages');
    }
};
