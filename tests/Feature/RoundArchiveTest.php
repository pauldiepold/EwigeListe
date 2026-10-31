<?php

namespace Tests\Feature;

use App\Models\Game;
use App\Models\Group;
use App\Models\LiveRound;
use App\Models\Round;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Inertia\Testing\AssertableInertia as Assert;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

class RoundArchiveTest extends TestCase
{
    use RefreshDatabase;

    #[Test]
    public function guests_are_redirected_to_login(): void
    {
        $this->get(route('rounds.index'))->assertRedirect('/login');
    }

    #[Test]
    public function the_archive_shows_the_ewige_liste_by_default(): void
    {
        $this->actingAs(User::factory()->create());
        $ewigeListe = Group::findOrFail(Group::EWIGE_LISTE_ID);
        $otherList = Group::factory()->create(['name' => 'Aaa-Liste']);
        $round = $this->roundInList($ewigeListe);
        $this->roundInList($otherList);

        $this->get(route('rounds.index'))
            ->assertOk()
            ->assertInertia(fn (Assert $page) => $page
                ->component('Rounds/Index')
                ->where('selectedGroup', ['id' => Group::EWIGE_LISTE_ID, 'name' => 'Ewige Liste'])
                ->where('groupOptions', [
                    ['id' => $otherList->id, 'name' => 'Aaa-Liste'],
                    ['id' => Group::EWIGE_LISTE_ID, 'name' => 'Ewige Liste'],
                ])
                ->where('rounds.total', 1)
                ->where('rounds.data.0.id', $round->id)
                ->where('archiveSort', ['column' => 'date', 'direction' => 'desc'])
            );
    }

    #[Test]
    public function the_archive_shows_only_rounds_of_the_selected_list(): void
    {
        $this->actingAs(User::factory()->create());
        $list = Group::factory()->create();
        $round = $this->roundInList($list);
        $this->roundInList(Group::findOrFail(Group::EWIGE_LISTE_ID));

        $this->get(route('rounds.index', ['group' => $list->id]))
            ->assertInertia(fn (Assert $page) => $page
                ->where('selectedGroup.id', $list->id)
                ->where('rounds.total', 1)
                ->where('rounds.data.0.id', $round->id)
            );
    }

    /**
     * @return array<string, array{string, string}>
     */
    public static function sortings(): array
    {
        return [
            'date asc' => ['date', 'asc'],
            'date desc' => ['date', 'desc'],
            'games asc' => ['games', 'asc'],
            'games desc' => ['games', 'desc'],
            'online asc' => ['online', 'asc'],
            'online desc' => ['online', 'desc'],
        ];
    }

    #[Test]
    #[DataProvider('sortings')]
    public function the_archive_sorts_rounds_by_the_chosen_column(string $column, string $direction): void
    {
        $this->freezeTime();
        $this->actingAs(User::factory()->create());
        $ewigeListe = Group::findOrFail(Group::EWIGE_LISTE_ID);

        [$lower, $higher] = match ($column) {
            'date' => $this->roundsDifferingInDate($ewigeListe),
            'games' => $this->roundsDifferingInGames($ewigeListe),
            'online' => $this->roundsDifferingInOnline($ewigeListe),
        };
        [$first, $second] = $direction === 'asc' ? [$lower, $higher] : [$higher, $lower];

        $this->get(route('rounds.index', ['sort' => $column, 'direction' => $direction]))
            ->assertInertia(fn (Assert $page) => $page
                ->where('archiveSort', ['column' => $column, 'direction' => $direction])
                ->has('rounds.data', 2)
                ->where('rounds.data.0.id', $first->id)
                ->where('rounds.data.1.id', $second->id)
            );
    }

    #[Test]
    public function an_unknown_sort_column_falls_back_to_date(): void
    {
        $this->freezeTime();
        $this->actingAs(User::factory()->create());
        [$older, $newer] = $this->roundsDifferingInDate(Group::findOrFail(Group::EWIGE_LISTE_ID));

        $this->get(route('rounds.index', ['sort' => 'players', 'direction' => 'asc']))
            ->assertInertia(fn (Assert $page) => $page
                ->where('archiveSort', ['column' => 'date', 'direction' => 'asc'])
                ->where('rounds.data.0.id', $older->id)
                ->where('rounds.data.1.id', $newer->id)
            );
    }

    #[Test]
    public function the_archive_shows_20_rounds_per_page(): void
    {
        $this->actingAs(User::factory()->create());
        $ewigeListe = Group::findOrFail(Group::EWIGE_LISTE_ID);
        Round::factory()->count(21)->create()->each(fn (Round $round) => $round->groups()->attach($ewigeListe));

        $this->get(route('rounds.index'))
            ->assertInertia(fn (Assert $page) => $page
                ->where('rounds.per_page', 20)
                ->where('rounds.total', 21)
                ->where('rounds.last_page', 2)
                ->has('rounds.data', 20)
            );

        $this->get(route('rounds.index', ['page' => 2]))
            ->assertInertia(fn (Assert $page) => $page
                ->where('rounds.current_page', 2)
                ->has('rounds.data', 1)
            );
    }

    #[Test]
    public function a_list_without_rounds_shows_an_empty_archive(): void
    {
        $this->actingAs(User::factory()->create());
        $list = Group::factory()->create();

        $this->get(route('rounds.index', ['group' => $list->id]))
            ->assertOk()
            ->assertInertia(fn (Assert $page) => $page
                ->where('rounds.total', 0)
                ->has('rounds.data', 0)
            );
    }

    private function roundInList(Group $list): Round
    {
        $round = Round::factory()->create();
        $round->groups()->attach($list);

        return $round;
    }

    /**
     * @return array{Round, Round}
     */
    private function roundsDifferingInDate(Group $list): array
    {
        $older = $this->roundInList($list);
        $this->travel(1)->day();
        $newer = $this->roundInList($list);

        return [$older, $newer];
    }

    /**
     * @return array{Round, Round}
     */
    private function roundsDifferingInGames(Group $list): array
    {
        $fewerGames = $this->roundInList($list);
        $moreGames = $this->roundInList($list);
        Game::factory()->create(['round_id' => $fewerGames->id]);
        Game::factory()->count(2)->create(['round_id' => $moreGames->id]);

        return [$fewerGames, $moreGames];
    }

    /**
     * @return array{Round, Round}
     */
    private function roundsDifferingInOnline(Group $list): array
    {
        $offline = $this->roundInList($list);
        $online = $this->roundInList($list);
        $online->liveRound()->associate(LiveRound::create())->save();

        return [$offline, $online];
    }
}
