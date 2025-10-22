<?php
namespace App\Notifications;

use App\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;

class MemberAddedToCellNotification extends Notification
{
    use Queueable;
    private $member;

    public function __construct(User $member)
    {
        $this->member = $member;
    }

    public function via(object $notifiable): array
    {
        return ['database'];
    }

    public function toDatabase(object $notifiable): array
    {
        return [
            'title' => 'Novo Membro na Célula',
            'message' => $this->member->name . ' foi adicionado à sua célula.',
            'link' => route('members.create'),
            'type' => 'member_added_to_cell',
            'member_id' => $this->member->id,
            'member_name' => $this->member->name,
        ];
    }
}
