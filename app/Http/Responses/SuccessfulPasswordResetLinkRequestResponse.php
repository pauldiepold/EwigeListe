<?php

namespace App\Http\Responses;

use App\Http\Toast;
use Illuminate\Http\RedirectResponse;
use Laravel\Fortify\Contracts\SuccessfulPasswordResetLinkRequestResponse as SuccessfulPasswordResetLinkRequestResponseContract;

/**
 * Wie bei Fortify zurück zum Formular, die Meldung aber als Toast statt als Session-`status`.
 */
class SuccessfulPasswordResetLinkRequestResponse implements SuccessfulPasswordResetLinkRequestResponseContract
{
    public function __construct(private string $status) {}

    public function toResponse($request): RedirectResponse
    {
        Toast::success(trans($this->status));

        return back();
    }
}
