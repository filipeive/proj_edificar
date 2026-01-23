<?php
namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;

class PendingContributionsNotification extends Notification
{
    use Queueable;
    private $count;
    private $packageName;

    public function __construct($count = 0, $packageName = null)
    {
        $this->count = $count;
        $this->packageName = $packageName;
    }

    public function via(object $notifiable): array
    {
        return ['database'];
    }

    public function toDatabase(object $notifiable): array
    {
        $packageInfo = $this->packageName ? " para o pacote {$this->packageName}" : "";
        return [
            'title' => 'Novas Contribuições para Validar',
            'message' => 'Existem ' . $this->count . ' contribuição' . ($this->count > 1 ? 'ões' : '') . $packageInfo . ' aguardando validação.',
            'link' => route('contributions.index', ['status' => 'pendente']),
            'type' => 'pending_contributions',
            'count' => $this->count,
        ];
    }
}
