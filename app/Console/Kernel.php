<?php

namespace App\Console;

use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Foundation\Console\Kernel as ConsoleKernel;

class Kernel extends ConsoleKernel
{
    /**
     * Comandos Artisan customizados.
     */
    protected $commands = [
        Commands\CheckExpiringCommitments::class,
        Commands\CheckPendingContributions::class,
    ];

    /**
     * Definir agendamento de comandos.
     */
    protected function schedule(Schedule $schedule): void
    {
        // Verificar compromissos expirando - diariamente às 9h
        $schedule->command('commitments:check-expiring')
            ->dailyAt('09:00')
            ->timezone('Africa/Maputo');

        // Verificar contribuições pendentes - 2x por dia (9h e 17h)
        $schedule->command('contributions:check-pending')
            ->twiceDaily(9, 17)
            ->timezone('Africa/Maputo');

        // OPCIONAL: Limpar notificações antigas (mais de 30 dias)
        $schedule->command('notifications:clear-old')
            ->weekly()
            ->sundays()
            ->at('02:00')
            ->timezone('Africa/Maputo');
    }

    /**
     * Registrar comandos da aplicação.
     */
    protected function commands(): void
    {
        $this->load(__DIR__.'/Commands');

        require base_path('routes/console.php');
    }
}