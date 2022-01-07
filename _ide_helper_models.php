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
 * @method static \Database\Factories\BadgeFactory factory(...$parameters)
 * @method static \Illuminate\Database\Eloquent\Builder|Badge newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Badge newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Badge query()
 * @method static \Illuminate\Database\Eloquent\Builder|Badge whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Badge whereGroupId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Badge whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Badge whereMonth($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Badge wherePlayerId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Badge whereType($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Badge whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Badge whereValue($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Badge whereYear($value)
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
 * @property-read \Illuminate\Database\Eloquent\Collection|Comment[] $replies
 * @property-read int|null $replies_count
 * @method static \Database\Factories\CommentFactory factory(...$parameters)
 * @method static \Illuminate\Database\Eloquent\Builder|Comment newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Comment newQuery()
 * @method static \Illuminate\Database\Query\Builder|Comment onlyTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder|Comment query()
 * @method static \Illuminate\Database\Eloquent\Builder|Comment whereBody($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Comment whereCommentableId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Comment whereCommentableType($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Comment whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Comment whereCreatedBy($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Comment whereDeletedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Comment whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Comment whereParentId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Comment whereUpdatedAt($value)
 * @method static \Illuminate\Database\Query\Builder|Comment withTrashed()
 * @method static \Illuminate\Database\Query\Builder|Comment withoutTrashed()
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
 * @method static \Database\Factories\GameFactory factory(...$parameters)
 * @method static \Illuminate\Database\Eloquent\Builder|Game newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Game newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Game query()
 * @method static \Illuminate\Database\Eloquent\Builder|Game whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Game whereCreatedBy($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Game whereDealerIndex($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Game whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Game whereLiveGameId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Game whereMisplay($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Game wherePoints($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Game whereRoundId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Game whereSolo($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Game whereUpdatedAt($value)
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
 * @method static \Illuminate\Database\Eloquent\Builder|GamePlayer newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|GamePlayer newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|GamePlayer query()
 * @method static \Illuminate\Database\Eloquent\Builder|GamePlayer whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|GamePlayer whereGameId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|GamePlayer whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|GamePlayer whereMisplayed($value)
 * @method static \Illuminate\Database\Eloquent\Builder|GamePlayer wherePlayerId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|GamePlayer wherePoints($value)
 * @method static \Illuminate\Database\Eloquent\Builder|GamePlayer whereSoloist($value)
 * @method static \Illuminate\Database\Eloquent\Builder|GamePlayer whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|GamePlayer whereWon($value)
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
 * @method static \Database\Factories\GroupFactory factory(...$parameters)
 * @method static \Illuminate\Database\Eloquent\Builder|Group newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Group newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Group query()
 * @method static \Illuminate\Database\Eloquent\Builder|Group whereClosed($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Group whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Group whereCreatedBy($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Group whereGamesPerRound($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Group whereHighestLosestreak($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Group whereHighestPoints($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Group whereHighestSoloRate($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Group whereHighestSoloWinrate($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Group whereHighestWinrate($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Group whereHighestWinstreak($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Group whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Group whereLowestPoints($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Group whereLowestSoloRate($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Group whereLowestSoloWinrate($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Group whereLowestWinrate($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Group whereMostGames($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Group whereMostGamesDay($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Group whereMostGamesMonth($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Group whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Group wherePointsPerGame($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Group whereQueued($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Group whereRecords($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Group whereStats($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Group whereTotalGames($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Group whereUpdatedAt($value)
 */
	class Group extends \Eloquent {}
}

namespace App{
/**
 * App\LiveGame
 *
 * @property int $id
 * @property int $live_round_id
 * @property bool|null $is_with_ai
 * @property \Illuminate\Support\Collection $spielerIDsInaktiv
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
 * @property \Illuminate\Support\Collection|null $messages
 * @property bool|null $geheiratet
 * @property bool|null $gewinntRe
 * @property int|null $wertungsPunkte
 * @property \Illuminate\Support\Collection $wertung
 * @property int|null $kontraAugen
 * @property int|null $reAugen
 * @property mixed|null $stiche
 * @property \Illuminate\Support\Collection|null $winners
 * @property bool $beendet
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \App\Game|null $game
 * @property-read mixed $aktueller_stich
 * @property-read mixed $letzter_stich
 * @property-read mixed $playing_with_ai
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
 * @method static \Illuminate\Database\Eloquent\Builder|LiveGame whereBeendet($value)
 * @method static \Illuminate\Database\Eloquent\Builder|LiveGame whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|LiveGame whereDran($value)
 * @method static \Illuminate\Database\Eloquent\Builder|LiveGame whereGeheiratet($value)
 * @method static \Illuminate\Database\Eloquent\Builder|LiveGame whereGeschmissen($value)
 * @method static \Illuminate\Database\Eloquent\Builder|LiveGame whereGewinntRe($value)
 * @method static \Illuminate\Database\Eloquent\Builder|LiveGame whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|LiveGame whereIsWithAi($value)
 * @method static \Illuminate\Database\Eloquent\Builder|LiveGame whereKontraAbsage($value)
 * @method static \Illuminate\Database\Eloquent\Builder|LiveGame whereKontraAnsage($value)
 * @method static \Illuminate\Database\Eloquent\Builder|LiveGame whereKontraAugen($value)
 * @method static \Illuminate\Database\Eloquent\Builder|LiveGame whereKontrasOffengelegt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|LiveGame whereLetzterStich($value)
 * @method static \Illuminate\Database\Eloquent\Builder|LiveGame whereLiveRoundId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|LiveGame whereMessages($value)
 * @method static \Illuminate\Database\Eloquent\Builder|LiveGame wherePhase($value)
 * @method static \Illuminate\Database\Eloquent\Builder|LiveGame whereReAbsage($value)
 * @method static \Illuminate\Database\Eloquent\Builder|LiveGame whereReAnsage($value)
 * @method static \Illuminate\Database\Eloquent\Builder|LiveGame whereReAugen($value)
 * @method static \Illuminate\Database\Eloquent\Builder|LiveGame whereResOffengelegt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|LiveGame whereSchweinchen($value)
 * @method static \Illuminate\Database\Eloquent\Builder|LiveGame whereSpieler0($value)
 * @method static \Illuminate\Database\Eloquent\Builder|LiveGame whereSpieler1($value)
 * @method static \Illuminate\Database\Eloquent\Builder|LiveGame whereSpieler2($value)
 * @method static \Illuminate\Database\Eloquent\Builder|LiveGame whereSpieler3($value)
 * @method static \Illuminate\Database\Eloquent\Builder|LiveGame whereSpielerIDsInaktiv($value)
 * @method static \Illuminate\Database\Eloquent\Builder|LiveGame whereSpieltyp($value)
 * @method static \Illuminate\Database\Eloquent\Builder|LiveGame whereStichNr($value)
 * @method static \Illuminate\Database\Eloquent\Builder|LiveGame whereStiche($value)
 * @method static \Illuminate\Database\Eloquent\Builder|LiveGame whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|LiveGame whereVorhand($value)
 * @method static \Illuminate\Database\Eloquent\Builder|LiveGame whereWertung($value)
 * @method static \Illuminate\Database\Eloquent\Builder|LiveGame whereWertungsPunkte($value)
 * @method static \Illuminate\Database\Eloquent\Builder|LiveGame whereWinners($value)
 */
	class LiveGame extends \Eloquent {}
}

namespace App{
/**
 * App\LiveRound
 *
 * @property int $id
 * @property int|null $schweinchen
 * @property int|null $fuchsSticht
 * @property int|null $schweinchenTrumpfsolo
 * @property int|null $koenigsSolo
 * @property int|null $karlchen
 * @property int|null $karlchenFangen
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Game[] $games
 * @property-read int|null $games_count
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\LiveGame[] $liveGames
 * @property-read int|null $live_games_count
 * @property-read \App\Round|null $round
 * @method static \Illuminate\Database\Eloquent\Builder|LiveRound newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|LiveRound newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|LiveRound query()
 * @method static \Illuminate\Database\Eloquent\Builder|LiveRound whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|LiveRound whereFuchsSticht($value)
 * @method static \Illuminate\Database\Eloquent\Builder|LiveRound whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|LiveRound whereKarlchen($value)
 * @method static \Illuminate\Database\Eloquent\Builder|LiveRound whereKarlchenFangen($value)
 * @method static \Illuminate\Database\Eloquent\Builder|LiveRound whereKoenigsSolo($value)
 * @method static \Illuminate\Database\Eloquent\Builder|LiveRound whereSchweinchen($value)
 * @method static \Illuminate\Database\Eloquent\Builder|LiveRound whereSchweinchenTrumpfsolo($value)
 * @method static \Illuminate\Database\Eloquent\Builder|LiveRound whereUpdatedAt($value)
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
 * @property string|null $ai_path
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
 * @property-read mixed $is_ai
 * @property-read mixed $path
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Group[] $groups
 * @property-read int|null $groups_count
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Profile[] $profiles
 * @property-read int|null $profiles_count
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Round[] $rounds
 * @property-read int|null $rounds_count
 * @property-read \App\User|null $user
 * @method static \Database\Factories\PlayerFactory factory(...$parameters)
 * @method static \Illuminate\Database\Eloquent\Builder|Player newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Player newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Player query()
 * @method static \Illuminate\Database\Eloquent\Builder|Player whereAiPath($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Player whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Player whereHide($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Player whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Player whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Player wherePayment($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Player whereSurname($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Player whereUpdatedAt($value)
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
 * @method static \Illuminate\Database\Eloquent\Builder|Profile newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Profile newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Profile query()
 * @method static \Illuminate\Database\Eloquent\Builder|Profile whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Profile whereDefault($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Profile whereGames($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Profile whereGamesLost($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Profile whereGamesPerDay($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Profile whereGamesThisMonth($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Profile whereGamesWon($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Profile whereGroupId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Profile whereHighestPoints($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Profile whereHighestPointsDate($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Profile whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Profile whereLoseStreak($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Profile whereLoseStreakEnd($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Profile whereLoseStreakStart($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Profile whereLowestPoints($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Profile whereLowestPointsDate($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Profile whereMostGamesDay($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Profile whereMostGamesDayDate($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Profile whereMostGamesMonth($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Profile whereMostGamesMonthDate($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Profile wherePlayerId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Profile wherePoints($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Profile wherePointsPerGame($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Profile wherePointsPerLose($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Profile wherePointsPerSolo($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Profile wherePointsPerWin($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Profile wherePointsThisMonth($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Profile whereQueued($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Profile whereSoli($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Profile whereSoliLost($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Profile whereSoliWon($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Profile whereSoloPoints($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Profile whereSoloRate($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Profile whereSoloWinrate($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Profile whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Profile whereWinStreak($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Profile whereWinStreakEnd($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Profile whereWinStreakStart($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Profile whereWinrate($value)
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
 * @method static \Database\Factories\RoundFactory factory(...$parameters)
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
 * @method static \Illuminate\Database\Eloquent\Builder|SocialiteUser newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|SocialiteUser newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|SocialiteUser query()
 * @method static \Illuminate\Database\Eloquent\Builder|SocialiteUser whereAvatar($value)
 * @method static \Illuminate\Database\Eloquent\Builder|SocialiteUser whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|SocialiteUser whereEmail($value)
 * @method static \Illuminate\Database\Eloquent\Builder|SocialiteUser whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|SocialiteUser whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|SocialiteUser whereProvider($value)
 * @method static \Illuminate\Database\Eloquent\Builder|SocialiteUser whereProviderId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|SocialiteUser whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|SocialiteUser whereUserId($value)
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
 * @method static \Database\Factories\UserFactory factory(...$parameters)
 * @method static \Illuminate\Database\Eloquent\Builder|User newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|User newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|User query()
 * @method static \Illuminate\Database\Eloquent\Builder|User whereAvatarPath($value)
 * @method static \Illuminate\Database\Eloquent\Builder|User whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|User whereEmail($value)
 * @method static \Illuminate\Database\Eloquent\Builder|User whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|User wherePassword($value)
 * @method static \Illuminate\Database\Eloquent\Builder|User wherePlayerId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|User whereRememberToken($value)
 * @method static \Illuminate\Database\Eloquent\Builder|User whereUpdatedAt($value)
 */
	class User extends \Eloquent {}
}

