<?php
namespace App\Notifications;

use App\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;

class MemberCreatedNotification extends Notification
{
    use Queueable;
    private $member;
    private $password;

    public function __construct(User $member, $password = null)
    {
        $this->member = $member;
        $this->password = $password;
    }

    public function via(object $notifiable): array
    {
        return ['database'];
    }

    public function toDatabase(object $notifiable): array
    {
        return [
            'title' => 'Bem-vindo ao Edificar!',
            'message' => 'Sua conta foi criada com sucesso. Você pode agora fazer login no sistema.',
            'link' => route('dashboard'),
            'type' => 'member_created',
            'member_id' => $this->member->id,
        ];
    }
}
