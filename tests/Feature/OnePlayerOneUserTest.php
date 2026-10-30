<?php

namespace Tests\Feature;

use App\Player;
use App\User;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\UniqueConstraintViolationException;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Schema;
use PHPUnit\Framework\Attributes\Test;
use RuntimeException;
use Tests\TestCase;

/**
 * Jeder Spieler gehört zu genau einem Benutzer. Die Migration sichert das ab und bricht ab,
 * wenn die vorhandenen Daten dagegen verstoßen; bereinigt wird dann gezielt von Hand.
 */
class OnePlayerOneUserTest extends TestCase
{
    use RefreshDatabase;

    #[Test]
    public function the_database_rejects_a_second_user_for_the_same_player(): void
    {
        $user = User::factory()->create();

        $this->expectException(UniqueConstraintViolationException::class);

        User::factory()->create(['player_id' => $user->player_id]);
    }

    #[Test]
    public function the_migration_names_players_without_a_user_and_aborts(): void
    {
        $this->revertToCleanStateBeforeMigration();
        $orphan = Player::factory()->create();

        $this->expectException(RuntimeException::class);
        $this->expectExceptionMessage("Spieler ohne Benutzer: {$orphan->id}. Spieler mit mehreren Benutzern: keine.");

        $this->migration()->up();
    }

    #[Test]
    public function the_migration_names_players_with_several_users_and_aborts(): void
    {
        $this->revertToCleanStateBeforeMigration();
        $user = User::factory()->create();
        User::factory()->create(['player_id' => $user->player_id]);

        $this->expectException(RuntimeException::class);
        $this->expectExceptionMessage("Spieler ohne Benutzer: keine. Spieler mit mehreren Benutzern: {$user->player_id}.");

        $this->migration()->up();
    }

    #[Test]
    public function the_migration_adds_the_unique_index_when_the_data_is_clean(): void
    {
        $this->revertToCleanStateBeforeMigration();

        $this->migration()->up();

        $this->assertTrue(Schema::hasIndex('users', 'users_player_id_unique'));
    }

    /**
     * `Player::factory()` legt bewusst keinen Benutzer an, auch nicht für den Ersteller der Ewigen Liste.
     */
    private function revertToCleanStateBeforeMigration(): void
    {
        Schema::table('users', function (Blueprint $table): void {
            $table->dropUnique(['player_id']);
        });

        Player::doesntHave('user')->each(fn (Player $player) => User::factory()->create(['player_id' => $player->id]));
    }

    private function migration(): Migration
    {
        return require database_path('migrations/2026_10_30_214211_make_player_id_unique_on_users_table.php');
    }
}
