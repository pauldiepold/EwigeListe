<?php

namespace App\Http;

use Inertia\Inertia;

/**
 * Einmalige Meldung für die nächste Inertia-Seite. Das Frontend zeigt sie zentral als Toast
 * (`resources/js/inertia/Components/Ui/FlashToasts.vue`); Pages kümmern sich nicht darum.
 *
 * Validierungsfehler gehören nicht hierher, sondern bleiben `errors` der Page.
 */
class Toast
{
    public static function success(string $message): void
    {
        self::flash('success', $message);
    }

    public static function error(string $message): void
    {
        self::flash('error', $message);
    }

    /**
     * @param  'success'|'error'  $type
     */
    private static function flash(string $type, string $message): void
    {
        Inertia::flash('toast', ['type' => $type, 'message' => $message]);
    }
}
