<?php

namespace App\Casts;

use Illuminate\Contracts\Database\Eloquent\CastsAttributes;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Collection;
use InvalidArgumentException;

/**
 * Meldungen eines Live-Spiels als Collection von `{ spielerId: int|null, text: string }`.
 *
 * Vor der Umstellung lagen die Meldungen als HTML-Strings in der Datenbank. Beim Lesen werden sie
 * ohne Tags und ohne Spieler übernommen, damit ein laufendes Spiel lesbar bleibt.
 *
 * @implements CastsAttributes<Collection<int, array{spielerId: int|null, text: string}>, Collection<int, array{spielerId: int|null, text: string}>>
 */
class LiveMessagesCast implements CastsAttributes
{
    /**
     * @param  array<string, mixed>  $attributes
     * @return Collection<int, array{spielerId: int|null, text: string}>
     */
    public function get(Model $model, string $key, mixed $value, array $attributes): Collection
    {
        $items = json_decode($value ?? '[]', true);

        return collect(is_array($items) ? array_values($items) : [])->map(
            fn (mixed $item): array => is_array($item)
                ? ['spielerId' => $item['spielerId'] ?? null, 'text' => (string) ($item['text'] ?? '')]
                : ['spielerId' => null, 'text' => strip_tags((string) $item)],
        );
    }

    /**
     * @param  array<string, mixed>  $attributes
     */
    public function set(Model $model, string $key, mixed $value, array $attributes): string
    {
        if (! $value instanceof Collection) {
            throw new InvalidArgumentException('The given value is not an Collection instance.');
        }

        return json_encode($value->values());
    }
}
