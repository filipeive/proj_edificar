<?php
namespace App\Notifications;

use App\Models\UserCommitment;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;

class CommitmentChosenNotification extends Notification
{
    use Queueable;
    private $userCommitment;

    public function __construct(UserCommitment $userCommitment)
    {
        $this->userCommitment = $userCommitment;
    }

    public function via(object $notifiable): array
    {
        return ['database'];
    }

    public function toDatabase(object $notifiable): array
    {
        $package = $this->userCommitment->package;
        
        return [
            'title' => 'Novo Compromisso',
            'message' => $this->userCommitment->user->name . ' se comprometeu com: ' . 
                        $package->name . ' (' . number_format($package->amount, 2, ',', '.') . ' MT/mês)',
            'link' => route('commitments.index'),
            'type' => 'commitment_chosen',
            'commitment_id' => $this->userCommitment->id,
            'user_name' => $this->userCommitment->user->name,
            'package_name' => $package->name,
        ];
    }
}
