<?php

namespace Tests\Feature;

use App\Models\Group;
use App\Models\Player;
use App\Models\Round;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Inertia\Testing\AssertableInertia as Assert;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

class GroupIndexTest extends TestCase
{
    use RefreshDatabase;

    #[Test]
    public function guests_are_redirected_to_login(): void
    {
        $this->get(route('groups.index'))->assertRedirect('/login');
    }

    #[Test]
    public function lists_are_ordered_by_their_number_of_rounds(): void
    {
        $this->actingAs(User::factory()->create());

        $fewRounds = Group::factory()->create();
        $manyRounds = Group::factory()->closed()->create();
        Round::factory()->count(2)->create()->each(fn (Round $round) => $round->groups()->attach($manyRounds));
        Round::factory()->create()->groups()->attach($fewRounds);
        $manyRounds->addPlayers(Player::factory()->count(3)->create());

        $this->get(route('groups.index'))
            ->assertOk()
            ->assertInertia(fn (Assert $page) => $page
                ->component('Groups/Index')
                ->has('groups', 3)
                ->where('groups.0', [
                    'id' => $manyRounds->id,
                    'name' => $manyRounds->name,
                    'closed' => true,
                    'players_count' => 3,
                    'rounds_count' => 2,
                ])
                ->where('groups.1.id', $fewRounds->id)
                ->where('groups.2.id', Group::EWIGE_LISTE_ID)
            );
    }
}
