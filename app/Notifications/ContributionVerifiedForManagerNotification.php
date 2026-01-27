<?php
namespace App\Notifications;

use App\Models\Contribution;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;

class ContributionVerifiedForManagerNotification extends Notification
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
            'title' => 'Contribuição Verificada',
            'message' => 'A contribuição de ' . $this->contribution->user->name . ' foi verificada com sucesso.',
            'link' => route('contributions.show', $this->contribution->id),
            'type' => 'contribution_verified_manager',
            'contribution_id' => $this->contribution->id,
        ];
    }
}
