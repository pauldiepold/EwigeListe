<?php

namespace Tests\Feature;

use App\Models\Group;
use App\Models\LiveGame;
use App\Models\Round;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Support\Collection;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

class ManageLiveRoundsTest extends TestCase
{
    use RefreshDatabase, WithFaker;

    /** @var Collection<int, User> */
    private Collection $users;

    protected function setUp(): void
    {
        parent::setUp();

        $this->users = User::factory()->count(5)->create();
        $this->actingAs($this->users->get(0));
    }

    /**
     * @param  array<string, mixed>  $overrides
     */
    private function storeRound(array $overrides = [])
    {
        return $this->post(route('rounds.store'), [
            'players' => $this->users->pluck('player_id')->toArray(),
            'groups' => [Group::EWIGE_LISTE_ID],
            'liveGame' => true,
            ...$overrides,
        ]);
    }

    /**
     * @return array<string, bool>
     */
    private function storedRules(): array
    {
        return Round::sole()->liveRound->only([
            'schweinchen',
            'fuchsSticht',
            'schweinchenTrumpfsolo',
            'koenigsSolo',
            'karlchen',
            'karlchenFangen',
        ]);
    }

    #[Test]
    public function a_user_can_create_a_live_round()
    {
        $response = $this->storeRound();

        $round = Round::sole();
        $response->assertRedirect($round->path());
        $this->assertNotNull($round->liveRound);
    }

    #[Test]
    public function an_offline_round_ignores_the_rules(): void
    {
        $this->storeRound(['liveGame' => false, 'schweinchen' => false])->assertSessionDoesntHaveErrors();

        $this->assertNull(Round::sole()->liveRound);
    }

    #[Test]
    public function a_live_round_without_rules_gets_the_standard(): void
    {
        $this->storeRound();

        $this->assertSame([
            'schweinchen' => true,
            'fuchsSticht' => true,
            'schweinchenTrumpfsolo' => false,
            'koenigsSolo' => true,
            'karlchen' => true,
            'karlchenFangen' => true,
        ], $this->storedRules());
    }

    #[Test]
    public function a_live_round_stores_the_chosen_rules(): void
    {
        $this->storeRound([
            'schweinchen' => true,
            'fuchsSticht' => false,
            'schweinchenTrumpfsolo' => true,
            'koenigsSolo' => false,
            'karlchen' => true,
            'karlchenFangen' => false,
        ]);

        $this->assertSame([
            'schweinchen' => true,
            'fuchsSticht' => false,
            'schweinchenTrumpfsolo' => true,
            'koenigsSolo' => false,
            'karlchen' => true,
            'karlchenFangen' => false,
        ], $this->storedRules());
    }

    #[Test]
    public function dependent_rules_are_off_when_their_parent_rule_is_off(): void
    {
        $this->storeRound([
            'schweinchen' => false,
            'fuchsSticht' => true,
            'schweinchenTrumpfsolo' => true,
            'karlchen' => false,
            'karlchenFangen' => true,
        ]);

        $rules = $this->storedRules();

        $this->assertFalse($rules['fuchsSticht']);
        $this->assertFalse($rules['schweinchenTrumpfsolo']);
        $this->assertFalse($rules['karlchenFangen']);
        $this->assertTrue($rules['koenigsSolo']);
    }

    #[Test]
    public function the_rules_must_be_booleans(): void
    {
        $this->storeRound(['koenigsSolo' => 'vielleicht'])->assertSessionHasErrors('koenigsSolo');

        $this->assertSame(0, Round::count());
    }

    #[Test]
    public function a_live_round_with_four_humans_deals_twelve_cards_to_everyone(): void
    {
        $users = User::factory()->count(4)->create();
        $this->actingAs($users->first());
        $this->post(route('rounds.store'), [
            'players' => $users->pluck('player_id')->toArray(),
            'groups' => [Group::EWIGE_LISTE_ID],
            'liveGame' => true,
        ]);
        $liveRound = Round::sole()->liveRound;

        $this->post(route('live.rounds.start', $liveRound))->assertOk();

        $liveGame = LiveGame::sole();
        $this->assertSame(2, $liveGame->phase);
        foreach ([0, 1, 2, 3] as $index) {
            $this->assertCount(12, $liveGame->{'spieler'.$index}->hand);
        }
    }
}
