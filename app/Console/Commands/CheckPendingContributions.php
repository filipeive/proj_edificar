<?php

namespace App\Console\Commands;

use App\Models\Contribution;
use App\Models\User;
use App\Notifications\PendingContributionsNotification;
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
        $this->info('🔍 Verificando contribuições pendentes...');

        // Contar contribuições pendentes
        $pendingCount = Contribution::where('status', 'pendente')->count();

        if ($pendingCount === 0) {
            $this->info('✅ Nenhuma contribuição pendente no momento.');
            return Command::SUCCESS;
        }

        // Buscar todos os administradores ativos
        $admins = User::where('role', 'admin')
            ->where('is_active', true)
            ->get();

        if ($admins->isEmpty()) {
            $this->warn('⚠️  Nenhum administrador ativo encontrado!');
            return Command::FAILURE;
        }

        // Notificar cada admin
        $notifiedCount = 0;
        foreach ($admins as $admin) {
            try {
                if ($admin->wantsNotification('pending_contributions')) {
                    $admin->notify(
                        new PendingContributionsNotification($pendingCount, null, null)
                    );
                }
                
                $this->line("📧 Notificação enviada para {$admin->name}");
                $notifiedCount++;
            } catch (\Exception $e) {
                $this->error("❌ Erro ao notificar {$admin->name}: {$e->getMessage()}");
            }
        }

        if ($notifiedCount > 0) {
            $this->info("✅ {$notifiedCount} administrador(es) notificado(s) sobre {$pendingCount} contribuição(ões) pendente(s)!");
            return Command::SUCCESS;
        }

        $this->error('❌ Nenhuma notificação foi enviada.');
        return Command::FAILURE;
    }
}
