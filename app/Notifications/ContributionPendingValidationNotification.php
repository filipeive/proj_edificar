<?php
namespace App\Notifications;

use App\Models\Contribution;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;

class ContributionPendingValidationNotification extends Notification
{
    use Queueable;

    private $contribution;

    public function __construct(Contribution $contribution)
    {
        $this->contribution = $contribution;
    }

    public function via(object $notifiable): array
    {
        return ['database'];
    }

    public function toDatabase(object $notifiable): array
    {
        $packageName = $this->contribution->package?->name;
        $packageLabel = $packageName ? " ({$packageName})" : '';

        return [
            'title' => 'Contribuição Pendente para Validar',
            'message' => 'Nova contribuição de ' . $this->contribution->user->name . ' no valor de ' .
                number_format($this->contribution->amount, 2, ',', '.') . ' MT' . $packageLabel . '.',
            'link' => route('contributions.pending'),
            'type' => 'contribution_pending_validation',
            'contribution_id' => $this->contribution->id,
        ];
    }
}
