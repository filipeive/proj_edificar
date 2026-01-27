<?php
namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;

class PendingContributionsNotification extends Notification
{
    use Queueable;
    private $count;
    private $packageName;
    private $packageId;

    public function __construct($count = 0, $packageName = null, $packageId = null)
    {
        $this->count = $count;
        $this->packageName = $packageName;
        $this->packageId = $packageId;
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
            'link' => route('contributions.pending'),
            'type' => 'pending_contributions',
            'count' => $this->count,
            'package_id' => $this->packageId,
        ];
    }
}
