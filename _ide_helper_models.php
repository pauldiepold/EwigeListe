<?php

// @formatter:off
/**
 * A helper file for your Eloquent Models
 * Copy the phpDocs from this file to the correct Model,
 * And remove them from this file, to prevent double declarations.
 *
 * @author Barry vd. Heuvel <barryvdh@gmail.com>
 */


namespace App{
/**
 * App\Badge
 *
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
 * @property-read \App\Group $group
 * @property-read \App\Player|null $player
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Badge newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Badge newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Badge query()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Badge whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Badge whereGroupId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Badge whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Badge whereMonth($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Badge wherePlayerId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Badge whereType($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Badge whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Badge whereValue($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Badge whereYear($value)
 * @mixin \Eloquent
 */
	class Badge extends \Eloquent {}
}

namespace App{
/**
 * App\Comment
 *
 * @property int $id
 * @property string $body
 * @property int|null $commentable_id
 * @property string|null $commentable_type
 * @property int|null $parent_id
 * @property int|null $created_by
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property \Illuminate\Support\Carbon|null $deleted_at
 * @property-read \Illuminate\Database\Eloquent\Model|\Eloquent $commentable
 * @property-read \App\Player|null $createdBy
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Comment[] $replies
 * @property-read int|null $replies_count
 * @method static bool|null forceDelete()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Comment newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Comment newQuery()
 * @method static \Illuminate\Database\Query\Builder|\App\Comment onlyTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Comment query()
 * @method static bool|null restore()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Comment whereBody($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Comment whereCommentableId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Comment whereCommentableType($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Comment whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Comment whereCreatedBy($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Comment whereDeletedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Comment whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Comment whereParentId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Comment whereUpdatedAt($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Comment withTrashed()
 * @method static \Illuminate\Database\Query\Builder|\App\Comment withoutTrashed()
 * @mixin \Eloquent
 */
	class Comment extends \Eloquent {}
}

namespace App{
/**
 * App\Game
 *
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
 * @property-read \App\Player|null $createdBy
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\GamePlayer[] $gamePlayers
 * @property-read int|null $game_players_count
 * @property-read \App\LiveGame|null $liveGame
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Player[] $players
 * @property-read int|null $players_count
 * @property-read \App\Round $round
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Game newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Game newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Game query()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Game whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Game whereCreatedBy($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Game whereDealerIndex($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Game whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Game whereLiveGameId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Game whereMisplay($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Game wherePoints($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Game whereRoundId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Game whereSolo($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Game whereUpdatedAt($value)
 * @mixin \Eloquent
 */
	class Game extends \Eloquent {}
}

namespace App{
/**
 * App\GamePlayer
 *
 * @property int $id
 * @property int $game_id
 * @property int $player_id
 * @property int|null $points
 * @property int $soloist
 * @property int $won
 * @property int $misplayed
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \App\Game $game
 * @property-read \App\Player $player
 * @method static \Illuminate\Database\Eloquent\Builder|\App\GamePlayer newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\GamePlayer newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\GamePlayer query()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\GamePlayer whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\GamePlayer whereGameId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\GamePlayer whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\GamePlayer whereMisplayed($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\GamePlayer wherePlayerId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\GamePlayer wherePoints($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\GamePlayer whereSoloist($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\GamePlayer whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\GamePlayer whereWon($value)
 * @mixin \Eloquent
 */
	class GamePlayer extends \Eloquent {}
}

namespace App{
/**
 * App\Group
 *
 * @property int $id
 * @property string $name
 * @property int $created_by
 * @property int|null $queued
 * @property int|null $closed
 * @property \Illuminate\Support\Collection|null $records
 * @property \Illuminate\Support\Collection|null $stats
 * @property \Illuminate\Support\Collection|null $mostGames
 * @property \Illuminate\Support\Collection|null $highestPoints
 * @property \Illuminate\Support\Collection|null $lowestPoints
 * @property \Illuminate\Support\Collection|null $highestWinrate
 * @property \Illuminate\Support\Collection|null $lowestWinrate
 * @property \Illuminate\Support\Collection|null $highestSoloWinrate
 * @property \Illuminate\Support\Collection|null $lowestSoloWinrate
 * @property \Illuminate\Support\Collection|null $highestSoloRate
 * @property \Illuminate\Support\Collection|null $lowestSoloRate
 * @property \Illuminate\Support\Collection|null $highestWinstreak
 * @property \Illuminate\Support\Collection|null $highestLosestreak
 * @property \Illuminate\Support\Collection|null $mostGamesDay
 * @property \Illuminate\Support\Collection|null $mostGamesMonth
 * @property int|null $totalGames
 * @property mixed|null $pointsPerGame
 * @property mixed|null $gamesPerRound
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Badge[] $badges
 * @property-read int|null $badges_count
 * @property-read \App\Player $creator
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Player[] $players
 * @property-read int|null $players_count
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Profile[] $profiles
 * @property-read int|null $profiles_count
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Round[] $rounds
 * @property-read int|null $rounds_count
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Group newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Group newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Group query()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Group whereClosed($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Group whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Group whereCreatedBy($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Group whereGamesPerRound($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Group whereHighestLosestreak($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Group whereHighestPoints($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Group whereHighestSoloRate($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Group whereHighestSoloWinrate($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Group whereHighestWinrate($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Group whereHighestWinstreak($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Group whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Group whereLowestPoints($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Group whereLowestSoloRate($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Group whereLowestSoloWinrate($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Group whereLowestWinrate($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Group whereMostGames($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Group whereMostGamesDay($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Group whereMostGamesMonth($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Group whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Group wherePointsPerGame($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Group whereQueued($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Group whereRecords($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Group whereStats($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Group whereTotalGames($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Group whereUpdatedAt($value)
 * @mixin \Eloquent
 */
	class Group extends \Eloquent {}
}

namespace App{
/**
 * App\LiveGame
 *
 * @property int $id
 * @property int $live_round_id
 * @property Collection $spielerIDsInaktiv
 * @property object $spieler0
 * @property object $spieler1
 * @property object $spieler2
 * @property object $spieler3
 * @property object $anzeige
 * @property int $phase
 * @property int $stichNr
 * @property int $vorhand
 * @property int $dran
 * @property Collection|null $augen
 * @property Collection|null $winners
 * @property bool|null $geschmissen
 * @property Collection|null $messages
 * @property bool|null $geheiratet
 * @property int|null $kontrasOffengelegt
 * @property int|null $resOffengelegt
 * @property string $spieltyp
 * @property object $letzterStich
 * @property object $aktuellerStich
 * @property bool|null $gewinntRe
 * @property int|null $wertungsPunkte
 * @property Collection $wertung
 * @property bool $beendet
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \App\Game|null $game
 * @property-read mixed $absagen
 * @property-read mixed $aktueller_stich
 * @property-read mixed $ansagen
 * @property-read mixed $armut_karten
 * @property-read mixed $letzter_stich
 * @property-read mixed $res
 * @property-read mixed $spieler
 * @property-read mixed $spieler_i_ds
 * @property-read mixed $spieler_indize
 * @property-read mixed $vorbehalte
 * @property-read \App\LiveRound $liveRound
 * @property-read \App\Round|null $round
 * @method static \Illuminate\Database\Eloquent\Builder|LiveGame newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|LiveGame newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|LiveGame query()
 * @method static \Illuminate\Database\Eloquent\Builder|LiveGame whereAktuellerStich($value)
 * @method static \Illuminate\Database\Eloquent\Builder|LiveGame whereAnzeige($value)
 * @method static \Illuminate\Database\Eloquent\Builder|LiveGame whereAugen($value)
 * @method static \Illuminate\Database\Eloquent\Builder|LiveGame whereBeendet($value)
 * @method static \Illuminate\Database\Eloquent\Builder|LiveGame whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|LiveGame whereDran($value)
 * @method static \Illuminate\Database\Eloquent\Builder|LiveGame whereGeheiratet($value)
 * @method static \Illuminate\Database\Eloquent\Builder|LiveGame whereGeschmissen($value)
 * @method static \Illuminate\Database\Eloquent\Builder|LiveGame whereGewinntRe($value)
 * @method static \Illuminate\Database\Eloquent\Builder|LiveGame whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|LiveGame whereKontrasOffengelegt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|LiveGame whereLetzterStich($value)
 * @method static \Illuminate\Database\Eloquent\Builder|LiveGame whereLiveRoundId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|LiveGame whereMessages($value)
 * @method static \Illuminate\Database\Eloquent\Builder|LiveGame wherePhase($value)
 * @method static \Illuminate\Database\Eloquent\Builder|LiveGame whereResOffengelegt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|LiveGame whereSpieler0($value)
 * @method static \Illuminate\Database\Eloquent\Builder|LiveGame whereSpieler1($value)
 * @method static \Illuminate\Database\Eloquent\Builder|LiveGame whereSpieler2($value)
 * @method static \Illuminate\Database\Eloquent\Builder|LiveGame whereSpieler3($value)
 * @method static \Illuminate\Database\Eloquent\Builder|LiveGame whereSpielerIDsInaktiv($value)
 * @method static \Illuminate\Database\Eloquent\Builder|LiveGame whereSpieltyp($value)
 * @method static \Illuminate\Database\Eloquent\Builder|LiveGame whereStichNr($value)
 * @method static \Illuminate\Database\Eloquent\Builder|LiveGame whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|LiveGame whereVorhand($value)
 * @method static \Illuminate\Database\Eloquent\Builder|LiveGame whereWertung($value)
 * @method static \Illuminate\Database\Eloquent\Builder|LiveGame whereWertungsPunkte($value)
 * @method static \Illuminate\Database\Eloquent\Builder|LiveGame whereWinners($value)
 * @mixin \Eloquent
 * @property mixed|null $stiche
 * @method static \Illuminate\Database\Eloquent\Builder|LiveGame whereStiche($value)
 * @property bool|null $reAnsage
 * @property bool|null $kontraAnsage
 * @property int|null $reAbsage
 * @property int|null $kontraAbsage
 * @property int|null $kontraAugen
 * @property int|null $reAugen
 * @method static \Illuminate\Database\Eloquent\Builder|LiveGame whereKontraAbsage($value)
 * @method static \Illuminate\Database\Eloquent\Builder|LiveGame whereKontraAnsage($value)
 * @method static \Illuminate\Database\Eloquent\Builder|LiveGame whereKontraAugen($value)
 * @method static \Illuminate\Database\Eloquent\Builder|LiveGame whereReAbsage($value)
 * @method static \Illuminate\Database\Eloquent\Builder|LiveGame whereReAnsage($value)
 * @method static \Illuminate\Database\Eloquent\Builder|LiveGame whereReAugen($value)
 * @property int|null $schweinchen
 * @method static \Illuminate\Database\Eloquent\Builder|LiveGame whereSchweinchen($value)
 */
	class LiveGame extends \Eloquent {}
}

namespace App{
/**
 * App\LiveRound
 *
 * @property int $id
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Game[] $games
 * @property-read int|null $games_count
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\LiveGame[] $liveGames
 * @property-read int|null $live_games_count
 * @property-read \App\Round $round
 * @method static \Illuminate\Database\Eloquent\Builder|\App\LiveRound newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\LiveRound newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\LiveRound query()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\LiveRound whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\LiveRound whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\LiveRound whereUpdatedAt($value)
 * @mixin \Eloquent
 * @property int|null $schweinchen
 * @property int|null $fuchsSticht
 * @property int|null $schweinchenTrumpfsolo
 * @property int|null $koenigsSolo
 * @property int|null $karlchen
 * @property int|null $karlchenFangen
 * @method static \Illuminate\Database\Eloquent\Builder|LiveRound whereFuchsSticht($value)
 * @method static \Illuminate\Database\Eloquent\Builder|LiveRound whereKarlchen($value)
 * @method static \Illuminate\Database\Eloquent\Builder|LiveRound whereKarlchenFangen($value)
 * @method static \Illuminate\Database\Eloquent\Builder|LiveRound whereKoenigsSolo($value)
 * @method static \Illuminate\Database\Eloquent\Builder|LiveRound whereSchweinchen($value)
 * @method static \Illuminate\Database\Eloquent\Builder|LiveRound whereSchweinchenTrumpfsolo($value)
 */
	class LiveRound extends \Eloquent {}
}

namespace App{
/**
 * App\Player
 *
 * @property int $id
 * @property string $surname
 * @property string $name
 * @property int|null $payment
 * @property int $hide
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Badge[] $badges
 * @property-read int|null $badges_count
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Comment[] $comments
 * @property-read int|null $comments_count
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Game[] $createdGames
 * @property-read int|null $created_games_count
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Group[] $createdGroups
 * @property-read int|null $created_groups_count
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Round[] $createdRounds
 * @property-read int|null $created_rounds_count
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\GamePlayer[] $gamePlayers
 * @property-read int|null $game_players_count
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Game[] $games
 * @property-read int|null $games_count
 * @property-read mixed $avatar_path
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Group[] $groups
 * @property-read int|null $groups_count
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Profile[] $profiles
 * @property-read int|null $profiles_count
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Round[] $rounds
 * @property-read int|null $rounds_count
 * @property-read \App\User $user
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Player newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Player newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Player query()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Player whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Player whereHide($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Player whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Player whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Player wherePayment($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Player whereSurname($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Player whereUpdatedAt($value)
 * @mixin \Eloquent
 * @property-read mixed $path
 */
	class Player extends \Eloquent {}
}

namespace App{
/**
 * App\Profile
 *
 * @property int $id
 * @property int $player_id
 * @property int $group_id
 * @property int|null $queued
 * @property int|null $default
 * @property int|null $points
 * @property int|null $pointsThisMonth
 * @property mixed|null $pointsPerGame
 * @property mixed|null $pointsPerWin
 * @property mixed|null $pointsPerLose
 * @property int|null $games
 * @property int|null $gamesThisMonth
 * @property mixed|null $gamesPerDay
 * @property int|null $gamesWon
 * @property int|null $gamesLost
 * @property mixed|null $winrate
 * @property int|null $soli
 * @property int|null $soliWon
 * @property int|null $soliLost
 * @property int|null $soloRate
 * @property mixed|null $soloWinrate
 * @property mixed|null $pointsPerSolo
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
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \App\Group $group
 * @property-read \App\Player $player
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Profile newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Profile newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Profile query()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Profile whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Profile whereDefault($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Profile whereGames($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Profile whereGamesLost($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Profile whereGamesPerDay($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Profile whereGamesThisMonth($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Profile whereGamesWon($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Profile whereGroupId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Profile whereHighestPoints($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Profile whereHighestPointsDate($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Profile whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Profile whereLoseStreak($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Profile whereLoseStreakEnd($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Profile whereLoseStreakStart($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Profile whereLowestPoints($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Profile whereLowestPointsDate($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Profile whereMostGamesDay($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Profile whereMostGamesDayDate($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Profile whereMostGamesMonth($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Profile whereMostGamesMonthDate($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Profile wherePlayerId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Profile wherePoints($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Profile wherePointsPerGame($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Profile wherePointsPerLose($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Profile wherePointsPerSolo($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Profile wherePointsPerWin($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Profile wherePointsThisMonth($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Profile whereQueued($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Profile whereSoli($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Profile whereSoliLost($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Profile whereSoliWon($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Profile whereSoloPoints($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Profile whereSoloRate($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Profile whereSoloWinrate($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Profile whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Profile whereWinStreak($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Profile whereWinStreakEnd($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Profile whereWinStreakStart($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Profile whereWinrate($value)
 * @mixin \Eloquent
 */
	class Profile extends \Eloquent {}
}

namespace App{
/**
 * App\Round
 *
 * @property int $id
 * @property int|null $live_round_id
 * @property int $created_by
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Comment[] $comments
 * @property-read int|null $comments_count
 * @property-read \App\Player $createdBy
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Game[] $games
 * @property-read int|null $games_count
 * @property-read mixed $path
 * @property-read mixed $players_string
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Group[] $groups
 * @property-read int|null $groups_count
 * @property-read \App\LiveRound|null $liveRound
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Player[] $players
 * @property-read int|null $players_count
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Round newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Round newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Round query()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Round whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Round whereCreatedBy($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Round whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Round whereLiveRoundId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Round whereUpdatedAt($value)
 * @mixin \Eloquent
 * @property-read mixed $active_players
 * @property-read mixed $dealer_index
 * @property-read mixed $inactive_players
 */
	class Round extends \Eloquent {}
}

namespace App{
/**
 * App\SocialiteUser
 *
 * @property int $id
 * @property int|null $user_id
 * @property string $provider
 * @property string $provider_id
 * @property string $name
 * @property string $email
 * @property string|null $avatar
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \App\User|null $user
 * @method static \Illuminate\Database\Eloquent\Builder|\App\SocialiteUser newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\SocialiteUser newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\SocialiteUser query()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\SocialiteUser whereAvatar($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\SocialiteUser whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\SocialiteUser whereEmail($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\SocialiteUser whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\SocialiteUser whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\SocialiteUser whereProvider($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\SocialiteUser whereProviderId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\SocialiteUser whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\SocialiteUser whereUserId($value)
 * @mixin \Eloquent
 */
	class SocialiteUser extends \Eloquent {}
}

namespace App{
/**
 * App\User
 *
 * @property int $id
 * @property int $player_id
 * @property string $email
 * @property string|null $avatar_path
 * @property string $password
 * @property string|null $remember_token
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \Illuminate\Notifications\DatabaseNotificationCollection|\Illuminate\Notifications\DatabaseNotification[] $notifications
 * @property-read int|null $notifications_count
 * @property-read \App\Player $player
 * @method static \Illuminate\Database\Eloquent\Builder|\App\User newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\User newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\User query()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\User whereAvatarPath($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\User whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\User whereEmail($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\User whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\User wherePassword($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\User wherePlayerId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\User whereRememberToken($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\User whereUpdatedAt($value)
 * @mixin \Eloquent
 */
	class User extends \Eloquent {}
}

