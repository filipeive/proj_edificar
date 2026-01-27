<?php
namespace App\Notifications;

use App\Models\Contribution;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;

class ContributionRejectedForManagerNotification extends Notification
{
    use Queueable;

    private $contribution;
    private $reason;

    public function __construct(Contribution $contribution, ?string $reason = null)
    {
        $this->contribution = $contribution;
        $this->reason = $reason;
    }

    public function via(object $notifiable): array
    {
        return ['database'];
    }

    public function toDatabase(object $notifiable): array
    {
        $message = 'A contribuição de ' . $this->contribution->user->name . ' foi rejeitada.';
        if ($this->reason) {
            $message .= ' Motivo: ' . $this->reason;
        }

        return [
            'title' => 'Contribuição Rejeitada',
            'message' => $message,
            'link' => route('contributions.show', $this->contribution->id),
            'type' => 'contribution_rejected_manager',
            'contribution_id' => $this->contribution->id,
        ];
    }
}
