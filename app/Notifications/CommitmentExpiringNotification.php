<?php
namespace App\Notifications;

use App\Models\UserCommitment;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;

class CommitmentExpiringNotification extends Notification
{
    use Queueable;
    private $userCommitment;
    private $daysRemaining;

    public function __construct(UserCommitment $userCommitment, $daysRemaining = 7)
    {
        $this->userCommitment = $userCommitment;
        $this->daysRemaining = $daysRemaining;
    }

    public function via(object $notifiable): array
    {
        return ['database'];
    }

    public function toDatabase(object $notifiable): array
    {
        $package = $this->userCommitment->package;
        
        return [
            'title' => 'Compromisso Próximo do Vencimento',
            'message' => 'Seu compromisso com ' . $package->name . ' vence em ' . $this->daysRemaining . ' dias.',
            'link' => route('commitments.index'),
            'type' => 'commitment_expiring',
            'commitment_id' => $this->userCommitment->id,
            'days_remaining' => $this->daysRemaining,
        ];
    }
}
