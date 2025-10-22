<?php
namespace App\Notifications;

use App\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;

class UserPromotedNotification extends Notification
{
    use Queueable;
    private $user;
    private $oldRole;
    private $newRole;

    public function __construct(User $user, $oldRole, $newRole)
    {
        $this->user = $user;
        $this->oldRole = $oldRole;
        $this->newRole = $newRole;
    }

    public function via(object $notifiable): array
    {
        return ['database'];
    }

    public function toDatabase(object $notifiable): array
    {
        $roleLabel = $this->getRoleLabel($this->newRole);
        
        return [
            'title' => 'Promoção! 🎉',
            'message' => 'Parabéns! Você foi promovido para ' . $roleLabel . '.',
            'link' => route('dashboard'),
            'type' => 'user_promoted',
            'user_id' => $this->user->id,
            'new_role' => $this->newRole,
        ];
    }

    private function getRoleLabel($role)
    {
        $labels = [
            'membro' => 'Membro',
            'lider_celula' => 'Líder de Célula',
            'supervisor' => 'Supervisor',
            'pastor_zona' => 'Pastor de Zona',
            'admin' => 'Administrador',
        ];
        return $labels[$role] ?? $role;
    }
}
