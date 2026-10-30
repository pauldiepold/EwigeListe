<?php

namespace Tests\Feature;

use App\Group;
use App\Player;
use App\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

/**
 * Schnellregistrierung aus „Neue Runde“: legt Spieler samt Benutzer mit Platzhalter-E-Mail an
 * und meldet die ID per Inertia-Flash, damit `Rounds/Create` ihn direkt auswählt.
 */
class QuickRegisterTest extends TestCase
{
    use RefreshDatabase;

    #[Test]
    public function guests_cannot_quick_register(): void
    {
        $this->post(route('register.quick.store'), ['surname' => 'Max', 'name' => 'Mustermann'])
            ->assertRedirect(route('login'));

        $this->assertDatabaseMissing('players', ['surname' => 'Max', 'name' => 'Mustermann']);
    }

    #[Test]
    public function it_creates_a_player_with_a_user_in_the_ewige_liste(): void
    {
        $this->actingAs(User::factory()->create());

        $this->post(route('register.quick.store'), ['surname' => 'Max', 'name' => 'Mustermann'])
            ->assertRedirect(route('rounds.create'));

        $player = Player::where('surname', 'Max')->where('name', 'Mustermann')->sole();
        $this->assertNotNull($player->user);
        $this->assertTrue(Group::find(Group::EWIGE_LISTE_ID)->players->contains($player));
    }

    #[Test]
    public function the_user_gets_an_undeliverable_placeholder_email(): void
    {
        $this->actingAs(User::factory()->create());

        $this->post(route('register.quick.store'), ['surname' => 'Max', 'name' => 'Mustermann']);
        $this->post(route('register.quick.store'), ['surname' => 'Erika', 'name' => 'Mustermann']);

        $emails = User::whereHas('player', fn ($query) => $query->where('name', 'Mustermann'))
            ->pluck('email');

        $this->assertCount(2, $emails->unique());
        $emails->each(fn (string $email) => $this->assertStringEndsWith('@ewigeliste.invalid', $email));
    }

    #[Test]
    public function it_flashes_the_new_player_id_for_preselection(): void
    {
        $this->actingAs(User::factory()->create());

        $response = $this->post(route('register.quick.store'), ['surname' => 'Max', 'name' => 'Mustermann']);

        $player = Player::where('surname', 'Max')->sole();
        $response->assertInertiaFlash('addedPlayerId', $player->id);
    }

    #[Test]
    public function surname_and_name_are_required(): void
    {
        $this->actingAs(User::factory()->create());
        $playerCount = Player::count();

        $this->from(route('rounds.create'))
            ->post(route('register.quick.store'), ['surname' => '', 'name' => ''])
            ->assertRedirect(route('rounds.create'))
            ->assertSessionHasErrors(['surname', 'name']);

        $this->assertSame($playerCount, Player::count());
    }
}
