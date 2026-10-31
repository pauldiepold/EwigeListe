<?php

namespace Tests\Feature;

use App\Models\Group;
use App\Models\Player;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use PHPUnit\Framework\Attributes\Test;
use RuntimeException;
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
        $this->post(route('register.quick.store'), $this->attributes())
            ->assertRedirect(route('login'));

        $this->assertDatabaseMissing('players', $this->attributes());
    }

    #[Test]
    public function it_creates_a_player_with_a_user_in_the_ewige_liste(): void
    {
        $this->actingAs(User::factory()->create());

        $this->post(route('register.quick.store'), $this->attributes())
            ->assertRedirect(route('rounds.create'));

        $player = Player::where('surname', 'Max')->where('name', 'Mustermann')->sole();
        $this->assertNotNull($player->user);
        $this->assertTrue(Group::find(Group::EWIGE_LISTE_ID)->players->contains($player));
    }

    #[Test]
    public function the_user_gets_an_undeliverable_placeholder_email(): void
    {
        $this->actingAs(User::factory()->create());

        $this->post(route('register.quick.store'), $this->attributes());
        $this->post(route('register.quick.store'), $this->attributes(['surname' => 'Erika']));

        $emails = User::whereHas('player', fn ($query) => $query->where('name', 'Mustermann'))
            ->pluck('email');

        $this->assertCount(2, $emails->unique());
        $emails->each(fn (string $email) => $this->assertStringEndsWith('@ewigeliste.invalid', $email));
    }

    #[Test]
    public function it_flashes_the_new_player_id_for_preselection(): void
    {
        $this->actingAs(User::factory()->create());

        $response = $this->post(route('register.quick.store'), $this->attributes());

        $player = Player::where('surname', 'Max')->where('name', 'Mustermann')->sole();
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
            ->assertSessionHasErrors([
                'surname' => 'Es muss ein Vorname angegeben werden!',
                'name' => 'Es muss ein Nachname angegeben werden!',
            ]);

        $this->assertSame($playerCount, Player::count());
    }

    #[Test]
    public function a_failed_user_creation_leaves_no_player_behind(): void
    {
        $this->actingAs(User::factory()->create());
        User::creating(fn () => throw new RuntimeException('Benutzer konnte nicht angelegt werden'));

        $this->post(route('register.quick.store'), $this->attributes())->assertServerError();

        $this->assertDatabaseMissing('players', $this->attributes());
    }

    #[Test]
    public function a_failed_list_membership_leaves_neither_player_nor_user_behind(): void
    {
        $this->actingAs(User::factory()->create());
        $userCount = User::count();
        Group::destroy(Group::EWIGE_LISTE_ID);

        $this->post(route('register.quick.store'), $this->attributes())->assertServerError();

        $this->assertDatabaseMissing('players', $this->attributes());
        $this->assertDatabaseCount('users', $userCount);
    }

    /**
     * @param  array{surname?: string, name?: string}  $overrides
     * @return array{surname: string, name: string}
     */
    private function attributes(array $overrides = []): array
    {
        return [...['surname' => 'Max', 'name' => 'Mustermann'], ...$overrides];
    }
}
