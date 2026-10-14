<?php

namespace App\Console\Commands;

use App\Models\UserCommitment;
use App\Models\Congregation;
use App\Notifications\CommitmentExpiringNotification;
use App\Services\CongregationContext;
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
        $context = app(CongregationContext::class);
        $count = 0;

        foreach (Congregation::where('is_active', true)->where('has_edificar_project', true)->get() as $congregation) {
            $count += $context->run($congregation, function () use ($congregation) {
                $this->info("🔍 Verificando compromissos: {$congregation->name}...");
                $expiringCommitments = UserCommitment::active()
                    ->whereNotNull('end_date')
                    ->where('end_date', '>', now())
                    ->where('end_date', '<=', now()->addDays(7))
                    ->with('user', 'package')
                    ->get();

                $notified = 0;
                foreach ($expiringCommitments as $commitment) {
                    $daysRemaining = $commitment->daysUntilExpiration();
                    if (!$daysRemaining || $daysRemaining > 7 || !$commitment->user?->wantsNotification('commitment_expiring')) {
                        continue;
                    }

                    $commitment->user->notify(new CommitmentExpiringNotification($commitment, $daysRemaining));
                    $this->line("📧 {$commitment->user->name} ({$daysRemaining} dias)");
                    $notified++;
                }

                return $notified;
            });
        }

        if ($count === 0) {
            $this->info('✅ Nenhum compromisso expirando nos próximos 7 dias.');
        } else {
            $this->info("✅ {$count} notificações enviadas no total.");
        }

        return Command::SUCCESS;
    }
}
