<?php

namespace App\Notifications;

use Filament\Auth\Notifications\ResetPassword as FilamentResetPassword;
use Illuminate\Notifications\Messages\MailMessage;

class ResetPassword extends FilamentResetPassword
{
    public function toMail($notifiable): MailMessage
    {
        return (new MailMessage)
            ->subject('Reset your SAAS password')
            ->greeting('Hi!')
            ->line('You are receiving this email because we received a password reset request for your SAAS account.')
            ->action('Reset password', $this->url)
            ->line('This link will expire in 60 minutes.')
            ->line('If you did not request a password reset, you can safely ignore this email — your password will remain unchanged.')
            ->salutation('Thanks, the SAAS team');
    }
}
