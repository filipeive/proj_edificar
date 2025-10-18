
<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void {
        Schema::create('cells', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->foreignId('supervision_id')->constrained('supervisions')->onDelete('cascade');
            $table->foreignId('leader_id')->nullable()->constrained('users')->onDelete('set null');
            $table->integer('member_count')->default(0);
            $table->timestamps();
            
            $table->unique(['name', 'supervision_id']);
        });
    }

    public function down(): void {
        Schema::dropIfExists('cells');
    }
};