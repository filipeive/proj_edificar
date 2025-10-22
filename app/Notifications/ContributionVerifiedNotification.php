<?php
namespace App\Notifications;

use App\Models\Contribution;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;

class ContributionVerifiedNotification extends Notification
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
        return [
            'title' => 'Contribuição Verificada ✓',
            'message' => 'Sua contribuição de ' . number_format($this->contribution->amount, 2, ',', '.') . ' MT foi verificada com sucesso.',
            'link' => route('contributions.show', $this->contribution->id),
            'type' => 'contribution_verified',
            'contribution_id' => $this->contribution->id,
        ];
    }
}
