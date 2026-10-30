<?php

namespace App\Http\Middleware;

use Illuminate\Http\Request;
use Inertia\Middleware;
use LogicException;

class HandleInertiaRequests extends Middleware
{
    /**
     * The root template that is loaded on the first page visit.
     *
     * @var string
     */
    protected $rootView = 'app';

    /**
     * Determine the current asset version.
     */
    public function version(Request $request): ?string
    {
        return parent::version($request);
    }

    /**
     * Define the props that are shared by default.
     *
     * @return array<string, mixed>
     */
    public function share(Request $request): array
    {
        return array_merge(parent::share($request), [
            'auth' => [
                'user' => $this->authUserPayload($request),
                'player' => $this->authPlayerPayload($request),
            ],
            'navigation' => [
                'showCurrentRound' => fn (): bool => (bool) $request->user()?->player?->rounds()->exists(),
            ],
            'flash' => [
                'success' => fn () => $request->session()->get('success'),
                'error' => fn () => $request->session()->get('error'),
                'status' => fn () => $request->session()->get('status'),
            ],
        ]);
    }

    /**
     * @return array{id: int}|null
     */
    private function authUserPayload(Request $request): ?array
    {
        $user = $request->user();

        if ($user === null) {
            return null;
        }

        return [
            'id' => (int) $user->id,
        ];
    }

    /**
     * Jeder Benutzer gehört genau einem Spieler; fehlt er, ist das ein Datenfehler.
     *
     * @return array{id: int, surname: string, name: string}|null
     */
    private function authPlayerPayload(Request $request): ?array
    {
        $user = $request->user();

        if ($user === null) {
            return null;
        }

        $player = $user->player ?? throw new LogicException("Benutzer {$user->id} hat keinen Spieler.");

        return [
            'id' => (int) $player->id,
            'surname' => $player->surname,
            'name' => $player->name,
        ];
    }
}
