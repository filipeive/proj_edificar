<?php
namespace App\Notifications;

use App\Models\Contribution;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;

class ContributionCreatedNotification extends Notification
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
            'title' => 'Nova Contribuição Registrada',
            'message' => $this->contribution->user->name . ' registrou uma contribuição de ' . 
                        number_format($this->contribution->amount, 2, ',', '.') . ' MT',
            'link' => route('contributions.show', $this->contribution->id),
            'type' => 'contribution_created',
            'contribution_id' => $this->contribution->id,
            'user_name' => $this->contribution->user->name,
        ];
    }
}
