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
Schema::table('supervisions', function (Blueprint $table) {
$table->unsignedBigInteger('sub_supervisor_id')->nullable()->after('supervisor_id');
$table->foreign('sub_supervisor_id')->references('id')->on('users')->nullOnDelete();
});
}

/**
* Reverse the migrations.
*/
public function down(): void
{
Schema::table('supervisions', function (Blueprint $table) {
$table->dropForeign(['sub_supervisor_id']);
$table->dropColumn('sub_supervisor_id');
});
}
};