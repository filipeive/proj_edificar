<?php

namespace App\Notifications;

use App\Models\Visitor;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;

class VisitorAssignedNotification extends Notification
{
    use Queueable;

    public Visitor $visitor;
    public string $actionRole; // 'pastor', 'supervisor', 'lider'
    public string $actionText;

    public function __construct(Visitor $visitor, string $actionRole, string $actionText)
    {
        $this->visitor = $visitor;
        $this->actionRole = $actionRole;
        $this->actionText = $actionText;
    }

    public function via(object $notifiable): array
    {
        return ['database'];
    }

    public function toDatabase(object $notifiable): array
    {
        $cellName = $this->visitor->cell ? $this->visitor->cell->name : ($this->visitor->zone ? $this->visitor->zone->name : 'Geral');

        return [
            'title' => 'Rede Pastoral: ' . ($this->actionRole === 'lider' ? 'Novo Visitante na Célula' : ($this->actionRole === 'supervisor' ? 'Acompanhar Visitante' : 'Informe de Visitante')),
            'message' => $this->actionText,
            'link' => route('visitors.show', $this->visitor->id),
            'type' => 'visitor_assigned_' . $this->actionRole,
            'visitor_id' => $this->visitor->id,
            'visitor_name' => $this->visitor->name,
            'cell_name' => $cellName,
        ];
    }
}
