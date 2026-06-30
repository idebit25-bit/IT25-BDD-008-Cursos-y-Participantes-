<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use App\Models\User;
use Illuminate\Support\Carbon;

class PasswordChangedAdminNotification extends Mailable
{
    use Queueable, SerializesModels;

    public $user;
    public $time;
    public $ip;

    public function __construct(User $user, $time, $ip)
    {
        $this->user = $user;
        $this->time = $time;
        $this->ip = $ip;
    }

    public function build()
    {
        return $this->view('emails.password_changed_admin')
                    ->subject('Notificación de Cambio de Contraseña');
    }
}
