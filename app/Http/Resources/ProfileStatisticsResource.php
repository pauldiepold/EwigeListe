<?php

namespace App\Http\Resources;

use App\Group;
use App\Profile;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/**
 * Kennzahlen eines Profils als fertige Zeilen, gruppiert in Abschnitte.
 *
 * @mixin Profile
 *
 * @phpstan-type Row array{label: string, value: string, hint?: string}
 */
class ProfileStatisticsResource extends JsonResource
{
    public const MINIMUM_GAMES = 10;

    private const OPPORTUNITY_COSTS_ABOVE_GAMES = 150;

    private const MINUTES_PER_GAME = 8;

    private const HOURLY_WAGE = 11.5;

    public static $wrap = null;

    /**
     * @return array{joinedLabel: string, statistics: ?list<list<Row>>, opportunityCosts: ?list<Row>}
     */
    public function toArray(Request $request): array
    {
        return [
            'joinedLabel' => $this->joinedLabel(),
            'statistics' => $this->games >= self::MINIMUM_GAMES ? $this->statistics() : null,
            'opportunityCosts' => $this->games > self::OPPORTUNITY_COSTS_ABOVE_GAMES ? $this->opportunityCosts() : null,
        ];
    }

    private function joinedLabel(): string
    {
        $prefix = (int) $this->group_id === Group::EWIGE_LISTE_ID ? 'Registriert seit dem' : 'Der Liste beigetreten am';

        return $prefix.' '.$this->created_at->format('d.m.Y');
    }

    /**
     * @return list<list<Row>>
     */
    private function statistics(): array
    {
        return [
            array_values(array_filter([
                $this->row('Aktuelle Punktzahl:', $this->points),
                $this->pointsThisMonth != 0 ? $this->row('Punkte in diesem Monat:', $this->pointsThisMonth) : null,
                $this->row('Punkte pro Spiel:', $this->pointsPerGame),
                $this->row('Punkte pro gewonnenem Spiel:', $this->pointsPerWin),
                $this->row('Punkte pro verlorenem Spiel:', $this->pointsPerLose),
            ])),
            array_values(array_filter([
                $this->row('Spiele:', $this->games),
                $this->gamesThisMonth > 0 ? $this->row('Spiele in diesem Monat:', $this->gamesThisMonth) : null,
                $this->row('Spiele pro Tag:', $this->gamesPerDay),
                $this->row('Gewonnen / Verloren:', "{$this->gamesWon} / {$this->gamesLost}"),
                $this->row('Gewinnrate:', "{$this->winrate}%"),
            ])),
            [
                $this->row('Normalspiele:', $this->normalGames),
                ...($this->normalGames > 0 ? [
                    $this->row('Gewonnen / Verloren:', "{$this->normalGamesWon} / {$this->normalGamesLost}"),
                    $this->row('Normalspiel-Gewinnrate:', "{$this->normalGameWinrate}%"),
                ] : []),
            ],
            [
                $this->row('Soli:', $this->soli),
                ...($this->soli > 0 ? [
                    $this->row('Gewonnen / Verloren:', "{$this->soliWon} / {$this->soliLost}"),
                    $this->row('Spiele bis Solo:', $this->soloRate),
                    $this->row('Solo-Gewinnrate:', "{$this->soloWinrate}%"),
                    $this->row('Punkte durch Soli:', $this->soloPoints),
                    $this->row('Punkte pro Solo:', $this->pointsPerSolo),
                ] : []),
            ],
            [
                $this->row('Meiste Spiele an einem Tag:', $this->mostGamesDay, $this->onDay($this->mostGamesDayDate)),
                $this->row(
                    'Meiste Spiele in einem Monat:',
                    $this->mostGamesMonth,
                    $this->mostGamesMonthDate ? "(im {$this->mostGamesMonthDate->isoFormat('MMMM YYYY')})" : null,
                ),
            ],
            [
                $this->row('Höchste Punktzahl:', $this->highestPoints, $this->highestPoints != 0 ? $this->onDay($this->highestPointsDate) : null),
                $this->row('Niedrigste Punktzahl:', $this->lowestPoints, $this->lowestPoints != 0 ? $this->onDay($this->lowestPointsDate) : null),
            ],
            [
                $this->row('Längste Sieges-Strähne:', $this->winStreak, $this->streakPeriod($this->winStreakStart, $this->winStreakEnd)),
                $this->row('Längste Pech-Strähne:', $this->loseStreak, $this->streakPeriod($this->loseStreakStart, $this->loseStreakEnd)),
            ],
            [
                $this->row('Eingetragene Spiele:', $this->gamesCreated),
                $this->row(
                    'Anteil eingetragener Spiele:',
                    "{$this->gamesCreateRate}%",
                    '(eingetragene Spiele inklusive Mehrspielerrunden pro Anzahl eigener Spiele)',
                ),
            ],
        ];
    }

    /**
     * @return list<Row>
     */
    private function opportunityCosts(): array
    {
        $minutes = $this->games * self::MINUTES_PER_GAME;
        $days = intdiv($minutes, 60 * 24);
        $hours = intdiv($minutes, 60) % 24;

        return [
            $this->row('Zeit:', ($days > 0 ? "{$days}d " : '')."{$hours}h ".($minutes % 60).'min'),
            $this->row('Entgangenes Hiwi-Gehalt:', number_format($minutes / 60 * self::HOURLY_WAGE, 2, ',', '.').'€'),
            $this->row('ECTS:', number_format($minutes / 60 / 30, 1, ',', '.')),
            $this->row('Vorlesungsdoppelstunden:', number_format($minutes / 90, 0, ',', '.')),
        ];
    }

    /**
     * @return Row
     */
    private function row(string $label, mixed $value, ?string $hint = null): array
    {
        return [
            'label' => $label,
            'value' => (string) $value,
            ...($hint !== null ? ['hint' => $hint] : []),
        ];
    }

    private function onDay(?Carbon $date): ?string
    {
        return $date ? "(am {$date->format('j.n.Y')})" : null;
    }

    private function streakPeriod(?Carbon $start, ?Carbon $end): ?string
    {
        if ($start === null || $end === null) {
            return null;
        }

        if ($start->isSameDay($end)) {
            return $this->onDay($start);
        }

        return "({$start->format('j.n')} - {$end->format('j.n.Y')})";
    }
}
