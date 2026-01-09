<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class AdminPasswordResetNotification extends Notification
{
    use Queueable;

    public $password;

    public function __construct($password)
    {
        $this->password = $password;
    }

    public function via($notifiable)
    {
        return ['mail', 'database'];
    }

    public function toMail($notifiable)
    {
        return (new MailMessage)
            ->subject('Sua senha foi redefinida - Portal Life Church')
            ->greeting('Olá ' . $notifiable->name . ',')
            ->line('Um administrador redefiniu a sua senha de acesso.')
            ->line('Sua nova senha é:')
            ->line($this->password)
            ->action('Acessar Portal', route('login'))
            ->line('Por favor, altere sua senha após o primeiro acesso para sua segurança.')
            ->line('Obrigado,')
            ->line('Equipe Life Church');
    }

    public function toDatabase($notifiable)
    {
        return [
            'title' => 'Senha Redefinida',
            'message' => 'Um administrador redefiniu sua senha. Verifique seu email para as novas credenciais.',
            'link' => route('profile.edit'),
            'type' => 'password_reset',
        ];
    }
}
