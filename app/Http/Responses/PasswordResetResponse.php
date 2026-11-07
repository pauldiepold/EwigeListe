<?php

namespace App\Http\Responses;

use App\Http\Toast;
use Illuminate\Http\RedirectResponse;
use Laravel\Fortify\Contracts\PasswordResetResponse as PasswordResetResponseContract;

/**
 * Wie bei Fortify zurück zum Login, die Meldung aber als Toast statt als Session-`status`.
 */
class PasswordResetResponse implements PasswordResetResponseContract
{
    public function __construct(private string $status) {}

    public function toResponse($request): RedirectResponse
    {
        Toast::success(trans($this->status));

        return redirect()->route('login');
    }
}
