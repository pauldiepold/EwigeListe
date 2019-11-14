<?php

namespace App\Notifications;

use Closure;
use Illuminate\Support\Facades\Lang;
use Illuminate\Notifications\Notification;
use Illuminate\Notifications\Messages\MailMessage;

class MyOwnResetPassword extends Notification {

    /**
     * The password reset token.
     *
     * @var string
     */
    public $token;

    /**
     * The callback that should be used to build the mail message.
     *
     * @var Closure|null
     */
    public static $toMailCallback;

    /**
     * Create a notification instance.
     *
     * @param string $token
     * @return void
     */
    public function __construct($token)
    {
        $this->token = $token;
    }

    /**
     * Get the notification's channels.
     *
     * @param mixed $notifiable
     * @return array|string
     */
    public function via($notifiable)
    {
        return ['mail'];
    }

    /**
     * Build the mail representation of the notification.
     *
     * @param mixed $notifiable
     * @return MailMessage
     */
    public function toMail($notifiable)
    {
        if (static::$toMailCallback)
        {
            return call_user_func(static::$toMailCallback, $notifiable, $this->token);
        }

        return (new MailMessage)
            ->greeting('Hallo!')
            ->subject('Passwort Zurücksetzen')
            ->line('warum hast du Trottel dein Passwort vergessen? Um mit einem neuen Passwort weiterhin Doppelkopf zu spielen, setze dein Passwort zurück:')
            ->action('Passwort zurücksetzen', url(config('app.url') . route('password.reset', ['token' => $this->token], false)))
            ->line('Dieser Link ist ' . config('auth.passwords.users.expire') . ' Minuten gültig.');
    }

    /**
     * Set a callback that should be used when building the notification mail message.
     *
     * @param Closure $callback
     * @return void
     */
    public static function toMailUsing($callback)
    {
        static::$toMailCallback = $callback;
    }
}
