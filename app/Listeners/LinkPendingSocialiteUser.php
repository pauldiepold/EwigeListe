<?php

namespace App\Listeners;

use App\Models\User;
use App\Services\PendingSocialiteLink;
use Illuminate\Auth\Events\Login;

/**
 * Jeder Login hängt eine gemerkte Google- oder Facebook-Identität an, auch der direkt nach der
 * Registrierung. So braucht weder das Login- noch das Registrierungsformular davon zu wissen.
 */
class LinkPendingSocialiteUser
{
    public function __construct(private PendingSocialiteLink $pendingSocialiteLink) {}

    public function handle(Login $event): void
    {
        if ($event->user instanceof User) {
            $this->pendingSocialiteLink->linkTo($event->user);
        }
    }
}
