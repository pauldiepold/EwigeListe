<?php

namespace Tests\Feature;

use App\Badge;
use App\Group;
use App\Player;
use App\Profile;
use App\Round;
use App\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Inertia\Testing\AssertableInertia as Assert;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

class GroupShowTest extends TestCase
{
    use RefreshDatabase;

    private User $user;

    protected function setUp(): void
    {
        parent::setUp();

        User::factory()->create(); // Benutzer 1 ist Admin
        $this->user = User::factory()->create();
        $this->actingAs($this->user);
    }

    #[Test]
    public function guests_are_redirected_to_login(): void
    {
        auth()->logout();

        $this->get(route('ewigeListe'))->assertRedirect('/login');
    }

    #[Test]
    public function without_a_selected_list_the_ewige_liste_is_shown(): void
    {
        $this->get(route('ewigeListe'))
            ->assertOk()
            ->assertInertia(fn (Assert $page) => $page
                ->component('Groups/Show')
                ->where('group.id', Group::EWIGE_LISTE_ID)
                ->where('group.name', 'Ewige Liste')
            );
    }

    #[Test]
    public function a_list_shows_its_members_with_most_games_first(): void
    {
        $group = Group::factory()->create();
        $rarely = $this->memberOf($group, ['games' => 2, 'points' => 7]);
        $often = $this->memberOf($group, ['games' => 9, 'points' => -3, 'pointsPerGame' => -0.33, 'soli' => 1]);

        $this->get(route('groups.show', $group))
            ->assertOk()
            ->assertInertia(fn (Assert $page) => $page
                ->component('Groups/Show')
                ->where('group.id', $group->id)
                ->has('members', 2)
                ->where('members.1.id', $rarely->id)
                ->has('members.0', fn (Assert $member) => $member
                    ->where('id', $often->id)
                    ->where('name', "{$often->surname} {$often->name}")
                    ->where('surname', $often->surname)
                    ->where('path', "/profil/{$often->id}/{$group->id}")
                    ->where('avatarPath', $often->avatar_path)
                    ->where('games', 9)
                    ->where('points', -3)
                    ->where('pointsPerGame', -0.33)
                    ->where('soli', 1)
                )
            );
    }

    #[Test]
    public function a_list_shows_records_and_statistics_of_this_list(): void
    {
        $group = Group::factory()->create();
        $this->memberOf($group, ['games' => 4]);

        $this->get(route('groups.show', $group))
            ->assertInertia(fn (Assert $page) => $page
                ->where('statistics.records.0.value', '4')
                ->has('statistics.stats', 3)
            );
    }

    #[Test]
    public function badges_are_grouped_by_type_and_year(): void
    {
        $group = Group::factory()->create();
        $player = $this->memberOf($group, []);
        foreach ([[2025, 3], [2024, 7]] as [$year, $month]) {
            Badge::create([
                'group_id' => $group->id,
                'player_id' => $player->id,
                'type' => 'points',
                'year' => $year,
                'month' => $month,
                'value' => 42,
            ]);
        }

        $this->get(route('groups.show', $group))
            ->assertInertia(fn (Assert $page) => $page
                ->has('badges', 1)
                ->where('badges.0.type', 'points')
                ->where('badges.0.years.0.year', 2025)
                ->where('badges.0.years.1.year', 2024)
                ->where('badges.0.years.1.badges.0', [
                    'name' => $player->surname,
                    'value' => 42,
                    'date' => 'Juli 2024',
                    'path' => "/profil/{$player->id}/{$group->id}#abzeichen",
                ])
            );
    }

    #[Test]
    public function the_games_chart_loads_on_demand_and_needs_five_rounds(): void
    {
        $group = Group::factory()->create();
        $this->roundsIn($group, 4);

        $this->get(route('groups.show', $group))
            ->assertInertia(fn (Assert $page) => $page
                ->where('hasGamesChart', false)
                ->missing('gamesChart')
            );

        $this->roundsIn($group, 1);

        $this->get(route('groups.show', $group))
            ->assertInertia(fn (Assert $page) => $page
                ->where('hasGamesChart', true)
                ->missing('gamesChart')
                ->reloadOnly('gamesChart', fn (Assert $reload) => $reload
                    ->where('gamesChart', ['gameDates' => [], 'gameCounter' => []])
                )
            );
    }

    #[Test]
    public function the_archive_only_contains_rounds_of_this_list(): void
    {
        $group = Group::factory()->create();
        [$round] = $this->roundsIn($group, 1);
        Round::factory()->create();

        $this->get(route('groups.show', $group))
            ->assertInertia(fn (Assert $page) => $page
                ->where('rounds.total', 1)
                ->where('rounds.data.0.id', $round->id)
                ->where('archiveSort', ['column' => 'date', 'direction' => 'desc'])
            );
    }

    #[Test]
    public function the_archive_can_be_paged_and_sorted(): void
    {
        $group = Group::factory()->create();
        $this->roundsIn($group, 21);

        $this->get(route('groups.show', ['group' => $group, 'page' => 2, 'sort' => 'games', 'direction' => 'asc']))
            ->assertInertia(fn (Assert $page) => $page
                ->where('rounds.current_page', 2)
                ->has('rounds.data', 1)
                ->where('archiveSort', ['column' => 'games', 'direction' => 'asc'])
            );
    }

    #[Test]
    public function the_page_tells_what_the_player_may_do(): void
    {
        $group = Group::factory()->create(['created_by' => $this->user->player->id]);
        $this->memberOf($group, []);

        $this->get(route('groups.show', $group))
            ->assertInertia(fn (Assert $page) => $page
                ->where('canJoin', true)
                ->where('canLeave', false)
                ->where('canClose', true)
            );
    }

    /**
     * @param  array<string, int|float>  $statistics
     */
    private function memberOf(Group $group, array $statistics): Player
    {
        $player = Player::factory()->create();
        $group->addPlayer($player);

        if ($statistics !== []) {
            Profile::where('player_id', $player->id)->where('group_id', $group->id)->update($statistics);
        }

        return $player;
    }

    /**
     * @return list<Round>
     */
    private function roundsIn(Group $group, int $count): array
    {
        return Round::factory()->count($count)->create()
            ->each(fn (Round $round) => $round->groups()->attach($group))
            ->all();
    }
}
