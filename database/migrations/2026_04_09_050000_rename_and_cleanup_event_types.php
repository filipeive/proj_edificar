<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        // Rename existing event types
        DB::table('event_types')->where('code', 'training')->update(['name' => 'Treinamentos']);
        DB::table('event_types')->where('code', 'fellowship')->update(['name' => 'Confraternizações']);
        DB::table('event_types')->where('code', 'community_service')->update(['name' => 'Servindo a Comunidade']);

        // Remove unwanted event types (if they exist)
        // First, delete associated quarterly_report_events to avoid FK constraints
        $typesToRemove = DB::table('event_types')
            ->whereIn('name', ['Encontro de Mulheres', 'Jejum e Oração', 'Encontro de mulheres', 'Jejum e oração'])
            ->pluck('id');

        if ($typesToRemove->isNotEmpty()) {
            DB::table('quarterly_report_events')
                ->whereIn('event_type_id', $typesToRemove)
                ->delete();

            DB::table('event_types')
                ->whereIn('id', $typesToRemove)
                ->delete();
        }
    }

    public function down(): void
    {
        // Revert renames
        DB::table('event_types')->where('code', 'training')->update(['name' => 'Treinamento']);
        DB::table('event_types')->where('code', 'fellowship')->update(['name' => 'Confraternização']);
        DB::table('event_types')->where('code', 'community_service')->update(['name' => 'Ação Comunitária']);
    }
};
