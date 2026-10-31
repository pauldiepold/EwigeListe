<?php

// @formatter:off
// phpcs:ignoreFile
/**
 * A helper file for your Eloquent Models
 * Copy the phpDocs from this file to the correct Model,
 * And remove them from this file, to prevent double declarations.
 *
 * @author Barry vd. Heuvel <barryvdh@gmail.com>
 */


namespace App\Models{
/**
 * @property int $id
 * @property int $group_id
 * @property int $year
 * @property int $month
 * @property string $type
 * @property int|null $player_id
 * @property int|null $value
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read mixed $date
 * @property-read \App\Models\Group $group
 * @property-read \App\Models\Player|null $player
 * @method static \Database\Factories\BadgeFactory factory($count = null, $state = [])
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Badge newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Badge newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Badge query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Badge whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Badge whereGroupId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Badge whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Badge whereMonth($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Badge wherePlayerId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Badge whereType($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Badge whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Badge whereValue($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Badge whereYear($value)
 */
	class Badge extends \Eloquent {}
}

namespace App\Models{
/**
 * @property int $id
 * @property int $round_id
 * @property int|null $live_game_id
 * @property int|null $points
 * @property int $solo
 * @property int $misplay
 * @property int|null $dealerIndex
 * @property int|null $created_by
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \App\Models\Player|null $createdBy
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\GamePlayer> $gamePlayers
 * @property-read int|null $game_players_count
 * @property-read \App\Models\LiveGame|null $liveGame
 * @property-read \App\Models\GamePlayer|null $pivot
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\Player> $players
 * @property-read int|null $players_count
 * @property-read \App\Models\Round $round
 * @method static \Database\Factories\GameFactory factory($count = null, $state = [])
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Game newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Game newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Game query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Game whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Game whereCreatedBy($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Game whereDealerIndex($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Game whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Game whereLiveGameId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Game whereMisplay($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Game wherePoints($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Game whereRoundId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Game whereSolo($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Game whereUpdatedAt($value)
 */
	class Game extends \Eloquent {}
}

namespace App\Models{
/**
 * @property int $id
 * @property int $game_id
 * @property int $player_id
 * @property int|null $points
 * @property int $soloist
 * @property int $won
 * @property int $misplayed
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \App\Models\Game $game
 * @property-read \App\Models\Player $player
 * @method static \Illuminate\Database\Eloquent\Builder<static>|GamePlayer newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|GamePlayer newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|GamePlayer query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|GamePlayer whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|GamePlayer whereGameId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|GamePlayer whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|GamePlayer whereMisplayed($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|GamePlayer wherePlayerId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|GamePlayer wherePoints($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|GamePlayer whereSoloist($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|GamePlayer whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|GamePlayer whereWon($value)
 */
	class GamePlayer extends \Eloquent {}
}

namespace App\Models{
/**
 * @property int $id
 * @property string $name
 * @property int $created_by
 * @property int|null $queued
 * @property int|null $closed
 * @property string|null $records
 * @property string|null $stats
 * @property string|null $mostGames
 * @property string|null $highestPoints
 * @property string|null $lowestPoints
 * @property string|null $highestWinrate
 * @property string|null $lowestWinrate
 * @property string|null $highestSoloWinrate
 * @property string|null $lowestSoloWinrate
 * @property string|null $highestSoloRate
 * @property string|null $lowestSoloRate
 * @property string|null $highestWinstreak
 * @property string|null $highestLosestreak
 * @property string|null $mostGamesDay
 * @property string|null $mostGamesMonth
 * @property int|null $totalGames
 * @property numeric|null $pointsPerGame
 * @property numeric|null $gamesPerRound
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\Badge> $badges
 * @property-read int|null $badges_count
 * @property-read \App\Models\Player $creator
 * @property-read \App\Models\Profile|null $pivot
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\Player> $players
 * @property-read int|null $players_count
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\Profile> $profiles
 * @property-read int|null $profiles_count
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\Round> $rounds
 * @property-read int|null $rounds_count
 * @method static \Database\Factories\GroupFactory factory($count = null, $state = [])
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Group newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Group newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Group query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Group whereClosed($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Group whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Group whereCreatedBy($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Group whereGamesPerRound($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Group whereHighestLosestreak($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Group whereHighestPoints($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Group whereHighestSoloRate($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Group whereHighestSoloWinrate($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Group whereHighestWinrate($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Group whereHighestWinstreak($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Group whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Group whereLowestPoints($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Group whereLowestSoloRate($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Group whereLowestSoloWinrate($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Group whereLowestWinrate($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Group whereMostGames($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Group whereMostGamesDay($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Group whereMostGamesMonth($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Group whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Group wherePointsPerGame($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Group whereQueued($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Group whereRecords($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Group whereStats($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Group whereTotalGames($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Group whereUpdatedAt($value)
 */
	class Group extends \Eloquent {}
}

namespace App\Models{
/**
 * @property int $id
 * @property int $live_round_id
 * @property bool|null $is_with_ai
 * @property \Illuminate\Support\Collection<array-key, mixed> $spielerIDsInaktiv
 * @property object $spieler0
 * @property object $spieler1
 * @property object $spieler2
 * @property object $spieler3
 * @property object $anzeige
 * @property int $phase
 * @property int $stichNr
 * @property int $vorhand
 * @property int $dran
 * @property string $spieltyp
 * @property object $letzterStich
 * @property object $aktuellerStich
 * @property bool|null $reAnsage
 * @property bool|null $kontraAnsage
 * @property int|null $reAbsage
 * @property int|null $kontraAbsage
 * @property int|null $resOffengelegt
 * @property int|null $kontrasOffengelegt
 * @property int|null $schweinchen
 * @property bool|null $geschmissen
 * @property \Illuminate\Support\Collection<array-key, mixed>|null $messages
 * @property bool|null $geheiratet
 * @property bool|null $gewinntRe
 * @property int|null $wertungsPunkte
 * @property \Illuminate\Support\Collection<array-key, mixed> $wertung
 * @property int|null $kontraAugen
 * @property int|null $reAugen
 * @property mixed|null $stiche
 * @property \Illuminate\Support\Collection<array-key, mixed>|null $winners
 * @property bool $beendet
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \App\Models\Game|null $game
 * @property-read mixed $aktueller_stich
 * @property-read mixed $letzter_stich
 * @property-read mixed $playing_with_ai
 * @property-read mixed $res
 * @property-read mixed $spieler
 * @property-read mixed $spieler_i_ds
 * @property-read mixed $spieler_indize
 * @property-read mixed $vorbehalte
 * @property-read \App\Models\LiveRound $liveRound
 * @property-read \App\Models\Round|null $round
 * @method static \Illuminate\Database\Eloquent\Builder<static>|LiveGame newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|LiveGame newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|LiveGame query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|LiveGame whereAktuellerStich($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|LiveGame whereAnzeige($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|LiveGame whereBeendet($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|LiveGame whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|LiveGame whereDran($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|LiveGame whereGeheiratet($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|LiveGame whereGeschmissen($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|LiveGame whereGewinntRe($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|LiveGame whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|LiveGame whereIsWithAi($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|LiveGame whereKontraAbsage($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|LiveGame whereKontraAnsage($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|LiveGame whereKontraAugen($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|LiveGame whereKontrasOffengelegt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|LiveGame whereLetzterStich($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|LiveGame whereLiveRoundId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|LiveGame whereMessages($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|LiveGame wherePhase($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|LiveGame whereReAbsage($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|LiveGame whereReAnsage($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|LiveGame whereReAugen($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|LiveGame whereResOffengelegt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|LiveGame whereSchweinchen($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|LiveGame whereSpieler0($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|LiveGame whereSpieler1($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|LiveGame whereSpieler2($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|LiveGame whereSpieler3($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|LiveGame whereSpielerIDsInaktiv($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|LiveGame whereSpieltyp($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|LiveGame whereStichNr($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|LiveGame whereStiche($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|LiveGame whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|LiveGame whereVorhand($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|LiveGame whereWertung($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|LiveGame whereWertungsPunkte($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|LiveGame whereWinners($value)
 */
	class LiveGame extends \Eloquent {}
}

namespace App\Models{
/**
 * @property int $id
 * @property int|null $schweinchen
 * @property int|null $fuchsSticht
 * @property int|null $schweinchenTrumpfsolo
 * @property int|null $koenigsSolo
 * @property int|null $karlchen
 * @property int|null $karlchenFangen
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\Game> $games
 * @property-read int|null $games_count
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\LiveGame> $liveGames
 * @property-read int|null $live_games_count
 * @property-read \App\Models\Round|null $round
 * @method static \Illuminate\Database\Eloquent\Builder<static>|LiveRound newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|LiveRound newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|LiveRound query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|LiveRound whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|LiveRound whereFuchsSticht($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|LiveRound whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|LiveRound whereKarlchen($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|LiveRound whereKarlchenFangen($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|LiveRound whereKoenigsSolo($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|LiveRound whereSchweinchen($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|LiveRound whereSchweinchenTrumpfsolo($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|LiveRound whereUpdatedAt($value)
 */
	class LiveRound extends \Eloquent {}
}

namespace App\Models{
/**
 * @property int $id
 * @property string $surname
 * @property string $name
 * @property string|null $ai_path
 * @property int|null $payment
 * @property int $hide
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\Badge> $badges
 * @property-read int|null $badges_count
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\Game> $createdGames
 * @property-read int|null $created_games_count
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\Group> $createdGroups
 * @property-read int|null $created_groups_count
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\Round> $createdRounds
 * @property-read int|null $created_rounds_count
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\GamePlayer> $gamePlayers
 * @property-read int|null $game_players_count
 * @property-read \App\Models\Profile|\App\Models\GamePlayer|null $pivot
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\Game> $games
 * @property-read int|null $games_count
 * @property-read mixed $avatar_path
 * @property-read mixed $is_ai
 * @property-read mixed $path
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\Group> $groups
 * @property-read int|null $groups_count
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\Profile> $profiles
 * @property-read int|null $profiles_count
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\Round> $rounds
 * @property-read int|null $rounds_count
 * @property-read \App\Models\User|null $user
 * @method static \Database\Factories\PlayerFactory factory($count = null, $state = [])
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Player newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Player newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Player query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Player whereAiPath($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Player whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Player whereHide($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Player whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Player whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Player wherePayment($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Player whereSurname($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Player whereUpdatedAt($value)
 */
	class Player extends \Eloquent {}
}

namespace App\Models{
/**
 * @property int $id
 * @property int $player_id
 * @property int $group_id
 * @property int|null $queued
 * @property int|null $default
 * @property int|null $points
 * @property int|null $pointsThisMonth
 * @property numeric|null $pointsPerGame
 * @property numeric|null $pointsPerWin
 * @property numeric|null $pointsPerLose
 * @property int|null $games
 * @property int|null $gamesThisMonth
 * @property numeric|null $gamesPerDay
 * @property int|null $gamesWon
 * @property int|null $gamesLost
 * @property numeric|null $winrate
 * @property int|null $normalGames
 * @property int|null $normalGamesWon
 * @property int|null $normalGamesLost
 * @property numeric|null $normalGameWinrate
 * @property int|null $soli
 * @property int|null $soliWon
 * @property int|null $soliLost
 * @property int|null $soloRate
 * @property numeric|null $soloWinrate
 * @property numeric|null $pointsPerSolo
 * @property int|null $soloPoints
 * @property int|null $mostGamesDay
 * @property \Illuminate\Support\Carbon|null $mostGamesDayDate
 * @property int|null $mostGamesMonth
 * @property \Illuminate\Support\Carbon|null $mostGamesMonthDate
 * @property int|null $highestPoints
 * @property \Illuminate\Support\Carbon|null $highestPointsDate
 * @property int|null $lowestPoints
 * @property \Illuminate\Support\Carbon|null $lowestPointsDate
 * @property int|null $winStreak
 * @property \Illuminate\Support\Carbon|null $winStreakStart
 * @property \Illuminate\Support\Carbon|null $winStreakEnd
 * @property int|null $loseStreak
 * @property \Illuminate\Support\Carbon|null $loseStreakStart
 * @property \Illuminate\Support\Carbon|null $loseStreakEnd
 * @property int|null $gamesCreated
 * @property numeric|null $gamesCreateRate
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \App\Models\Group $group
 * @property-read \App\Models\Player $player
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Profile newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Profile newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Profile query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Profile whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Profile whereDefault($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Profile whereGames($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Profile whereGamesCreateRate($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Profile whereGamesCreated($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Profile whereGamesLost($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Profile whereGamesPerDay($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Profile whereGamesThisMonth($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Profile whereGamesWon($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Profile whereGroupId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Profile whereHighestPoints($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Profile whereHighestPointsDate($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Profile whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Profile whereLoseStreak($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Profile whereLoseStreakEnd($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Profile whereLoseStreakStart($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Profile whereLowestPoints($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Profile whereLowestPointsDate($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Profile whereMostGamesDay($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Profile whereMostGamesDayDate($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Profile whereMostGamesMonth($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Profile whereMostGamesMonthDate($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Profile whereNormalGameWinrate($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Profile whereNormalGames($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Profile whereNormalGamesLost($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Profile whereNormalGamesWon($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Profile wherePlayerId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Profile wherePoints($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Profile wherePointsPerGame($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Profile wherePointsPerLose($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Profile wherePointsPerSolo($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Profile wherePointsPerWin($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Profile wherePointsThisMonth($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Profile whereQueued($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Profile whereSoli($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Profile whereSoliLost($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Profile whereSoliWon($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Profile whereSoloPoints($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Profile whereSoloRate($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Profile whereSoloWinrate($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Profile whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Profile whereWinStreak($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Profile whereWinStreakEnd($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Profile whereWinStreakStart($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Profile whereWinrate($value)
 */
	class Profile extends \Eloquent {}
}

namespace App\Models{
/**
 * App\Models\Round
 *
 * @property int $id
 * @property int|null $live_round_id
 * @property int $created_by
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * @property-read Player $createdBy
 * @property-read Collection|Game[] $games
 * @property-read int|null $games_count
 * @property-read mixed $path
 * @property-read mixed $players_string
 * @property-read Collection|Group[] $groups
 * @property-read int|null $groups_count
 * @property-read LiveRound|null $liveRound
 * @property-read Collection|Player[] $players
 * @property-read int|null $players_count
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Round newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Round newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Round query()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Round whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Round whereCreatedBy($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Round whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Round whereLiveRoundId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Round whereUpdatedAt($value)
 * @mixin \Eloquent
 * @property-read mixed $active_players
 * @property-read mixed $dealer_index
 * @property-read mixed $inactive_players
 * @method static \Database\Factories\RoundFactory factory($count = null, $state = [])
 */
	class Round extends \Eloquent {}
}

namespace App\Models{
/**
 * @property int $id
 * @property int|null $user_id
 * @property string $provider
 * @property string $provider_id
 * @property string $name
 * @property string $email
 * @property string|null $avatar
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \App\Models\User|null $user
 * @method static \Illuminate\Database\Eloquent\Builder<static>|SocialiteUser newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|SocialiteUser newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|SocialiteUser query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|SocialiteUser whereAvatar($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|SocialiteUser whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|SocialiteUser whereEmail($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|SocialiteUser whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|SocialiteUser whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|SocialiteUser whereProvider($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|SocialiteUser whereProviderId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|SocialiteUser whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|SocialiteUser whereUserId($value)
 */
	class SocialiteUser extends \Eloquent {}
}

namespace App\Models{
/**
 * @property int $id
 * @property int $player_id
 * @property string $email
 * @property string|null $avatar_path
 * @property string $password
 * @property string|null $remember_token
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \Illuminate\Notifications\DatabaseNotificationCollection<int, \Illuminate\Notifications\DatabaseNotification> $notifications
 * @property-read int|null $notifications_count
 * @property-read \App\Models\Player $player
 * @method static \Database\Factories\UserFactory factory($count = null, $state = [])
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User whereAvatarPath($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User whereEmail($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User wherePassword($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User wherePlayerId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User whereRememberToken($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User whereUpdatedAt($value)
 */
	class User extends \Eloquent {}
}

