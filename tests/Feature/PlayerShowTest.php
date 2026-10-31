<?php

namespace Tests\Feature;

use App\Badge;
use App\Game;
use App\Group;
use App\Player;
use App\Profile;
use App\Round;
use App\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Inertia\Inertia;
use Inertia\Testing\AssertableInertia as Assert;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

class PlayerShowTest extends TestCase
{
    use RefreshDatabase;

    private User $admin;

    private User $viewer;

    protected function setUp(): void
    {
        parent::setUp();

        $this->admin = User::factory()->create();
        $this->viewer = User::factory()->create();
        $this->actingAs($this->viewer);
    }

    #[Test]
    public function guests_are_redirected_to_the_login(): void
    {
        auth()->logout();
        $player = $this->memberOfEwigeListe();

        $this->get(route('players.show', $player))->assertRedirect(route('login'));
    }

    #[Test]
    public function without_a_list_the_profile_of_the_ewige_liste_is_shown(): void
    {
        $player = $this->memberOfEwigeListe(['surname' => 'Anna', 'name' => 'Muster']);
        $otherList = Group::factory()->create(['name' => 'Andere Liste']);
        $otherList->addPlayer($player);
        Group::factory()->create(['name' => 'Fremde Liste']);

        $this->get(route('players.show', $player))
            ->assertOk()
            ->assertInertia(fn (Assert $page) => $page
                ->component('Players/Show')
                ->where('player.id', $player->id)
                ->where('player.name', 'Anna Muster')
                ->where('selectedGroupId', Group::EWIGE_LISTE_ID)
                ->has('groupOptions', 2)
                ->where('groupOptions', fn ($options) => collect($options)->pluck('name')->doesntContain('Fremde Liste'))
            );
    }

    #[Test]
    public function a_list_in_the_url_selects_the_profile_of_that_list(): void
    {
        $player = $this->memberOfEwigeListe();
        $list = Group::factory()->create();
        $list->addPlayer($player);
        Profile::where(['player_id' => $player->id, 'group_id' => $list->id])->update(['games' => 12, 'points' => 77]);

        $this->get(route('players.show', [$player, $list]))
            ->assertOk()
            ->assertInertia(fn (Assert $page) => $page
                ->where('selectedGroupId', $list->id)
                ->where('profile.statistics.0.0', ['label' => 'Aktuelle Punktzahl:', 'value' => '77'])
            );
    }

    #[Test]
    public function a_list_the_player_is_not_a_member_of_is_not_found(): void
    {
        $player = $this->memberOfEwigeListe();
        $foreignList = Group::factory()->create();

        $this->get(route('players.show', [$player, $foreignList]))->assertNotFound();
    }

    #[Test]
    public function the_profile_of_a_hidden_player_is_forbidden(): void
    {
        $hidden = Player::factory()->hidden()->create();
        Group::findOrFail(Group::EWIGE_LISTE_ID)->addPlayer($hidden);

        $this->get(route('players.show', $hidden))->assertForbidden();
    }

    #[Test]
    public function only_the_admin_may_view_hidden_and_ai_players(): void
    {
        $regular = Player::factory()->create();
        $hidden = Player::factory()->hidden()->create();
        $ai = Player::factory()->create();
        $ai->forceFill(['ai_path' => 'lib_doko.so'])->save();

        $this->assertTrue($this->viewer->can('view', $regular));
        $this->assertFalse($this->viewer->can('view', $hidden));
        $this->assertFalse($this->viewer->can('view', $ai));

        $this->assertTrue($this->admin->can('view', $hidden));
        $this->assertTrue($this->admin->can('view', $ai));
    }

    #[Test]
    public function the_account_settings_are_offered_for_the_own_player_and_to_the_admin(): void
    {
        $player = $this->memberOfEwigeListe();
        $ownUser = $player->user;

        $this->get(route('players.show', $player))
            ->assertInertia(fn (Assert $page) => $page->where('player.editableUserId', null));

        $this->actingAs($ownUser)->get(route('players.show', $player))
            ->assertInertia(fn (Assert $page) => $page->where('player.editableUserId', $ownUser->id));

        $this->actingAs($this->admin)->get(route('players.show', $player))
            ->assertInertia(fn (Assert $page) => $page->where('player.editableUserId', $ownUser->id));
    }

    #[Test]
    public function statistics_appear_from_the_tenth_game(): void
    {
        $player = $this->memberOfEwigeListe();

        $this->setProfile($player, ['games' => 9]);
        $this->get(route('players.show', $player))
            ->assertInertia(fn (Assert $page) => $page->where('profile.statistics', null));

        $this->setProfile($player, ['games' => 10]);
        $this->get(route('players.show', $player))
            ->assertInertia(fn (Assert $page) => $page->has('profile.statistics', 8));
    }

    #[Test]
    public function the_statistics_show_all_key_figures_of_the_profile(): void
    {
        $player = $this->memberOfEwigeListe();
        $this->setProfile($player, [
            'games' => 40,
            'gamesThisMonth' => 4,
            'gamesPerDay' => 1.5,
            'gamesWon' => 30,
            'gamesLost' => 10,
            'winrate' => 75,
            'points' => 120,
            'pointsThisMonth' => -6,
            'pointsPerGame' => 3,
            'pointsPerWin' => 5,
            'pointsPerLose' => -3,
            'normalGames' => 38,
            'normalGamesWon' => 28,
            'normalGamesLost' => 10,
            'normalGameWinrate' => 73.7,
            'soli' => 2,
            'soliWon' => 2,
            'soliLost' => 0,
            'soloRate' => 20,
            'soloWinrate' => 100,
            'soloPoints' => 18,
            'pointsPerSolo' => 9,
            'mostGamesDay' => 12,
            'mostGamesDayDate' => '2023-01-15',
            'mostGamesMonth' => 25,
            'mostGamesMonthDate' => '2023-05-01',
            'highestPoints' => 130,
            'highestPointsDate' => '2023-06-09',
            'lowestPoints' => 0,
            'lowestPointsDate' => '2023-01-02',
            'winStreak' => 7,
            'winStreakStart' => '2023-02-03',
            'winStreakEnd' => '2023-03-05',
            'loseStreak' => 3,
            'loseStreakStart' => '2023-04-07',
            'loseStreakEnd' => '2023-04-07',
            'gamesCreated' => 11,
            'gamesCreateRate' => 27.5,
        ]);

        $this->get(route('players.show', $player))
            ->assertInertia(fn (Assert $page) => $page->where('profile.statistics', [
                [
                    ['label' => 'Aktuelle Punktzahl:', 'value' => '120'],
                    ['label' => 'Punkte in diesem Monat:', 'value' => '-6'],
                    ['label' => 'Punkte pro Spiel:', 'value' => '3.00'],
                    ['label' => 'Punkte pro gewonnenem Spiel:', 'value' => '5.00'],
                    ['label' => 'Punkte pro verlorenem Spiel:', 'value' => '-3.00'],
                ],
                [
                    ['label' => 'Spiele:', 'value' => '40'],
                    ['label' => 'Spiele in diesem Monat:', 'value' => '4'],
                    ['label' => 'Spiele pro Tag:', 'value' => '1.50'],
                    ['label' => 'Gewonnen / Verloren:', 'value' => '30 / 10'],
                    ['label' => 'Gewinnrate:', 'value' => '75.0%'],
                ],
                [
                    ['label' => 'Normalspiele:', 'value' => '38'],
                    ['label' => 'Gewonnen / Verloren:', 'value' => '28 / 10'],
                    ['label' => 'Normalspiel-Gewinnrate:', 'value' => '73.7%'],
                ],
                [
                    ['label' => 'Soli:', 'value' => '2'],
                    ['label' => 'Gewonnen / Verloren:', 'value' => '2 / 0'],
                    ['label' => 'Spiele bis Solo:', 'value' => '20'],
                    ['label' => 'Solo-Gewinnrate:', 'value' => '100.0%'],
                    ['label' => 'Punkte durch Soli:', 'value' => '18'],
                    ['label' => 'Punkte pro Solo:', 'value' => '9.00'],
                ],
                [
                    ['label' => 'Meiste Spiele an einem Tag:', 'value' => '12', 'hint' => '(am 15.1.2023)'],
                    ['label' => 'Meiste Spiele in einem Monat:', 'value' => '25', 'hint' => '(im Mai 2023)'],
                ],
                [
                    ['label' => 'Höchste Punktzahl:', 'value' => '130', 'hint' => '(am 9.6.2023)'],
                    ['label' => 'Niedrigste Punktzahl:', 'value' => '0'],
                ],
                [
                    ['label' => 'Längste Sieges-Strähne:', 'value' => '7', 'hint' => '(3.2 - 5.3.2023)'],
                    ['label' => 'Längste Pech-Strähne:', 'value' => '3', 'hint' => '(am 7.4.2023)'],
                ],
                [
                    ['label' => 'Eingetragene Spiele:', 'value' => '11'],
                    [
                        'label' => 'Anteil eingetragener Spiele:',
                        'value' => '27.5%',
                        'hint' => '(eingetragene Spiele inklusive Mehrspielerrunden pro Anzahl eigener Spiele)',
                    ],
                ],
            ]));
    }

    #[Test]
    public function rows_without_a_value_are_left_out(): void
    {
        $player = $this->memberOfEwigeListe();
        $this->setProfile($player, ['games' => 10, 'pointsThisMonth' => 0, 'gamesThisMonth' => 0, 'normalGames' => 0, 'soli' => 0]);

        $this->get(route('players.show', $player))
            ->assertInertia(fn (Assert $page) => $page
                ->where('profile.statistics.2', [['label' => 'Normalspiele:', 'value' => '0']])
                ->where('profile.statistics.3', [['label' => 'Soli:', 'value' => '0']])
                ->where('profile.statistics.0', fn ($rows) => collect($rows)->doesntContain('label', 'Punkte in diesem Monat:'))
                ->where('profile.statistics.1', fn ($rows) => collect($rows)->doesntContain('label', 'Spiele in diesem Monat:'))
            );
    }

    #[Test]
    public function opportunity_costs_start_above_150_games(): void
    {
        $player = $this->memberOfEwigeListe();

        $this->setProfile($player, ['games' => 150]);
        $this->get(route('players.show', $player))
            ->assertInertia(fn (Assert $page) => $page->where('profile.opportunityCosts', null));

        $this->setProfile($player, ['games' => 200]);
        $this->get(route('players.show', $player))
            ->assertInertia(fn (Assert $page) => $page->where('profile.opportunityCosts', [
                ['label' => 'Zeit:', 'value' => '1d 2h 40min'],
                ['label' => 'Entgangenes Hiwi-Gehalt:', 'value' => '306,67€'],
                ['label' => 'ECTS:', 'value' => '0,9'],
                ['label' => 'Vorlesungsdoppelstunden:', 'value' => '18'],
            ]));
    }

    #[Test]
    public function the_joined_label_depends_on_the_list(): void
    {
        $player = $this->memberOfEwigeListe();
        $list = Group::factory()->create();
        $list->addPlayer($player);
        Profile::where('player_id', $player->id)->update(['created_at' => '2021-03-04 12:00:00']);

        $this->get(route('players.show', $player))
            ->assertInertia(fn (Assert $page) => $page->where('profile.joinedLabel', 'Registriert seit dem 04.03.2021'));

        $this->get(route('players.show', [$player, $list]))
            ->assertInertia(fn (Assert $page) => $page->where('profile.joinedLabel', 'Der Liste beigetreten am 04.03.2021'));
    }

    #[Test]
    public function the_archive_shows_only_rounds_of_the_player_in_the_selected_list(): void
    {
        $player = $this->memberOfEwigeListe();
        $list = Group::factory()->create();
        $list->addPlayer($player);

        $ownRound = Round::factory()->create();
        $ownRound->groups()->attach($list);
        $ownRound->players()->attach($player, ['index' => 0]);

        $roundOfOthers = Round::factory()->create();
        $roundOfOthers->groups()->attach($list);

        $ownRoundInOtherList = Round::factory()->create();
        $ownRoundInOtherList->groups()->attach(Group::EWIGE_LISTE_ID);
        $ownRoundInOtherList->players()->attach($player, ['index' => 0]);

        $this->get(route('players.show', [$player, $list]))
            ->assertInertia(fn (Assert $page) => $page
                ->where('rounds.total', 1)
                ->where('rounds.data.0.id', $ownRound->id)
                ->where('archiveSort', ['column' => 'date', 'direction' => 'desc'])
            );
    }

    #[Test]
    public function the_archive_can_be_paged(): void
    {
        $player = $this->memberOfEwigeListe();
        foreach (range(1, 21) as $ignored) {
            $round = Round::factory()->create();
            $round->groups()->attach(Group::EWIGE_LISTE_ID);
            $round->players()->attach($player, ['index' => 0]);
        }

        $this->get(route('players.show', ['player' => $player, 'page' => 2]))
            ->assertInertia(fn (Assert $page) => $page
                ->where('rounds.total', 21)
                ->where('rounds.current_page', 2)
                ->has('rounds.data', 1)
            );
    }

    #[Test]
    public function the_badges_of_the_selected_list_are_grouped_and_not_linked(): void
    {
        $player = $this->memberOfEwigeListe(['surname' => 'Anna']);
        $list = Group::factory()->create();
        $list->addPlayer($player);
        Badge::factory()->create(['player_id' => $player->id, 'group_id' => $list->id, 'type' => 'points', 'year' => 2024, 'month' => 7, 'value' => 42]);
        Badge::factory()->create(['player_id' => $player->id, 'group_id' => $list->id, 'type' => 'games', 'year' => 2024, 'month' => 8, 'value' => 30]);
        Badge::factory()->create(['player_id' => $player->id, 'group_id' => Group::EWIGE_LISTE_ID, 'type' => 'points', 'year' => 2020, 'month' => 1, 'value' => 5]);

        $this->get(route('players.show', [$player, $list]))
            ->assertInertia(fn (Assert $page) => $page
                ->has('badges', 2)
                ->where('badgeCount', 2)
                ->where('badges.0.type', 'games')
                ->where('badges.1.type', 'points')
                ->where('badges.1.years.0', [
                    'year' => 2024,
                    'badges' => [['name' => 'Anna', 'value' => 42, 'date' => 'Juli 2024', 'path' => null]],
                ])
            );
    }

    #[Test]
    public function the_charts_load_on_demand(): void
    {
        $player = $this->memberOfEwigeListe();
        $this->setProfile($player, ['games' => 10]);
        $round = Round::factory()->create();
        $round->groups()->attach(Group::EWIGE_LISTE_ID);
        $this->gameOn($round, $player, '2023-01-01 20:00:00', 3);
        $this->gameOn($round, $player, '2023-01-01 21:00:00', -1);
        $this->gameOn($round, $player, '2023-01-03 20:00:00', 4);

        $this->get(route('players.show', $player))
            ->assertInertia(fn (Assert $page) => $page
                ->missing('profileChart')
                ->reloadOnly('profileChart', fn (Assert $reload) => $reload
                    ->where('profileChart', [
                        'dates' => ['1. Jan 2023', '1. Jan 2023', '3. Jan 2023'],
                        'points' => [3, 2, 6],
                        'gameDates' => ['1. Jan 2023', '1. Jan 2023', '2. Jan 2023', '3. Jan 2023'],
                        'gameCounter' => [0, 2, 2, 3],
                    ])
                )
            );
    }

    #[Test]
    public function the_charts_stay_empty_when_the_stored_game_count_is_outdated(): void
    {
        $player = $this->memberOfEwigeListe();
        $this->setProfile($player, ['games' => 10]);

        $this->get(route('players.show', $player))
            ->assertInertia(fn (Assert $page) => $page
                ->reloadOnly('profileChart', fn (Assert $reload) => $reload
                    ->where('profileChart', ['dates' => [], 'points' => [], 'gameDates' => [], 'gameCounter' => []])
                )
            );
    }

    #[Test]
    public function thinned_out_charts_keep_the_newest_game(): void
    {
        $player = $this->memberOfEwigeListe();
        $this->setProfile($player, ['games' => 702]);
        $round = Round::factory()->create();
        $round->groups()->attach(Group::EWIGE_LISTE_ID);
        $games = Game::factory()->count(702)->create(['round_id' => $round->id]);
        $player->games()->attach($games->pluck('id'), ['points' => 1, 'soloist' => false, 'won' => true, 'misplayed' => false]);

        $this->get(route('players.show', $player))
            ->assertInertia(fn (Assert $page) => $page
                ->reloadOnly('profileChart', fn (Assert $reload) => $reload
                    ->where('profileChart.points', fn ($points) => count($points) <= 700 + 1 && collect($points)->last() === 702)
                    ->where('profileChart.gameCounter', fn ($counter) => collect($counter)->last() === 702)
                )
            );
    }

    #[Test]
    public function below_ten_games_there_are_no_charts(): void
    {
        $player = $this->memberOfEwigeListe();
        $this->get(route('players.show', $player))->assertOk();

        $this->withHeaders([
            'X-Inertia' => 'true',
            'X-Inertia-Version' => (string) Inertia::getVersion(),
            'X-Inertia-Partial-Component' => 'Players/Show',
            'X-Inertia-Partial-Data' => 'profileChart',
        ])->get(route('players.show', $player))
            ->assertOk()
            ->assertJsonMissingPath('props.profileChart');
    }

    #[Test]
    public function the_legacy_profile_endpoints_are_gone(): void
    {
        $player = $this->memberOfEwigeListe();
        $profile = $player->profiles()->firstOrFail();

        $this->get("/charts/profile/{$profile->id}")->assertNotFound();
        $this->get('/rounds/ajax/'.Group::EWIGE_LISTE_ID."/{$player->id}")->assertNotFound();
    }

    private function gameOn(Round $round, Player $player, string $createdAt, int $points): void
    {
        $game = Game::factory()->create(['round_id' => $round->id]);
        $game->forceFill(['created_at' => $createdAt])->save();
        $game->players()->attach($player, ['points' => $points, 'soloist' => false, 'won' => $points > 0, 'misplayed' => false]);
    }

    /**
     * @param  array<string, mixed>  $statistics
     */
    private function setProfile(Player $player, array $statistics): void
    {
        Profile::where(['player_id' => $player->id, 'group_id' => Group::EWIGE_LISTE_ID])->update($statistics);
    }

    /**
     * @param  array<string, string>  $attributes
     */
    private function memberOfEwigeListe(array $attributes = []): Player
    {
        $player = Player::factory()->create($attributes);
        User::factory()->create(['player_id' => $player->id]);
        Group::findOrFail(Group::EWIGE_LISTE_ID)->addPlayer($player);

        return $player;
    }
}
