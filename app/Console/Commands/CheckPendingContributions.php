<?php

namespace App\Console\Commands;

use App\Models\Contribution;
use App\Models\Congregation;
use App\Models\User;
use App\Notifications\PendingContributionsNotification;
use App\Services\CongregationContext;
use Illuminate\Console\Command;

class CheckPendingContributions extends Command
{
    /**
     * Nome e assinatura do comando.
     *
     * @var string
     */
    protected $signature = 'contributions:check-pending';

    /**
     * Descrição do comando.
     *
     * @var string
     */
    protected $description = 'Verifica contribuições pendentes e notifica administradores';

    /**
     * Execute o console command.
     *
     * @return int
     */
    public function handle()
    {
        $context = app(CongregationContext::class);
        $overallNotified = 0;

        foreach (Congregation::where('is_active', true)->get() as $congregation) {
            $overallNotified += $context->run($congregation, function () use ($congregation) {
                $this->info("🔍 Verificando contribuições: {$congregation->name}...");
                $pendingCount = Contribution::where('status', 'pendente')->count();
                if ($pendingCount === 0) {
                    return 0;
                }

                $admins = User::whereIn('role', ['admin', 'super_admin'])
                    ->where('is_active', true)
                    ->get();

                foreach ($admins as $admin) {
                    if ($admin->wantsNotification('pending_contributions')) {
                        $admin->notify(new PendingContributionsNotification($pendingCount, null, null));
                    }
                }

                $this->line("📧 {$admins->count()} administradores notificados sobre {$pendingCount} contribuições.");
                return $admins->count();
            });
        }

        $this->info("✅ {$overallNotified} notificações administrativas enviadas.");
        return Command::SUCCESS;
    }
}
