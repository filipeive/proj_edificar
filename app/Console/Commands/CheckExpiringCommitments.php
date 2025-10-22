<?php

namespace App\Console\Commands;

use App\Models\UserCommitment;
use App\Notifications\CommitmentExpiringNotification;
use Illuminate\Console\Command;

class CheckExpiringCommitments extends Command
{
    /**
     * Nome e assinatura do comando.
     */
    protected $signature = 'commitments:check-expiring';

    /**
     * Descrição do comando.
     */
    protected $description = 'Verifica compromissos que estão próximos do vencimento e notifica os usuários';

    /**
     * Executar o comando.
     */
    public function handle()
    {
        $this->info('🔍 Verificando compromissos próximos do vencimento...');

        $expiringCommitments = UserCommitment::active()
            ->whereNotNull('end_date')
            ->where('end_date', '>', now())
            ->where('end_date', '<=', now()->addDays(7))
            ->with('user', 'package')
            ->get();

        if ($expiringCommitments->isEmpty()) {
            $this->info('✅ Nenhum compromisso expirando nos próximos 7 dias.');
            return Command::SUCCESS;
        }

        $count = 0;
        foreach ($expiringCommitments as $commitment) {
            $daysRemaining = $commitment->daysUntilExpiration();
            
            if ($daysRemaining && $daysRemaining <= 7) {
                $commitment->user->notify(
                    new CommitmentExpiringNotification($commitment, $daysRemaining)
                );
                
                $this->line("📧 Notificação enviada para {$commitment->user->name} ({$daysRemaining} dias)");
                $count++;
            }
        }

        $this->info("✅ {$count} notificações enviadas com sucesso!");
        return Command::SUCCESS;
    }
}