<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

class ClearOldNotifications extends Command
{
    /**
     * Nome e assinatura do comando.
     */
    protected $signature = 'notifications:clear-old {--days=30 : Número de dias}';

    /**
     * Descrição do comando.
     */
    protected $description = 'Remove notificações lidas com mais de X dias';

    /**
     * Executar o comando.
     */
    public function handle()
    {
        $days = $this->option('days');
        $cutoffDate = now()->subDays($days);

        $this->info("🗑️  Removendo notificações lidas com mais de {$days} dias...");

        $deleted = DB::table('notifications')
            ->whereNotNull('read_at')
            ->where('read_at', '<', $cutoffDate)
            ->delete();

        if ($deleted > 0) {
            $this->info("✅ {$deleted} notificações antigas removidas!");
        } else {
            $this->info("✅ Nenhuma notificação antiga encontrada.");
        }

        return Command::SUCCESS;
    }
}