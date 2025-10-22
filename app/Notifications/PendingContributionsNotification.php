<?php
namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;

class PendingContributionsNotification extends Notification
{
    use Queueable;
    private $count;

    public function __construct($count = 0)
    {
        $this->count = $count;
    }

    public function via(object $notifiable): array
    {
        return ['database'];
    }

    public function toDatabase(object $notifiable): array
    {
        return [
            'title' => 'Contribuições Aguardando Verificação',
            'message' => 'Existem ' . $this->count . ' contribuição' . ($this->count > 1 ? 'ões' : '') . ' pendentes de verificação.',
            'link' => route('contributions.pending'),
            'type' => 'pending_contributions',
            'count' => $this->count,
        ];
    }
}
