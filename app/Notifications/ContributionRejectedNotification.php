<?php
namespace App\Notifications;

use App\Models\Contribution;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;

class ContributionRejectedNotification extends Notification
{
    use Queueable;
    private $contribution;
    private $reason;

    public function __construct(Contribution $contribution, $reason = null)
    {
        $this->contribution = $contribution;
        $this->reason = $reason ?? 'Documento não conforme';
    }

    public function via(object $notifiable): array
    {
        return ['database'];
    }

    public function toDatabase(object $notifiable): array
    {
        return [
            'title' => 'Contribuição Rejeitada ✗',
            'message' => 'Sua contribuição de ' . number_format($this->contribution->amount, 2, ',', '.') . ' MT foi rejeitada. Motivo: ' . $this->reason,
            'link' => route('contributions.edit', $this->contribution->id),
            'type' => 'contribution_rejected',
            'contribution_id' => $this->contribution->id,
            'reason' => $this->reason,
        ];
    }
}
