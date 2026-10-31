<?php

namespace Tests\Unit;

use App\Badge;
use App\Game;
use App\Group;
use App\Player;
use App\Round;
use App\User;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Foundation\Testing\RefreshDatabase;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

class FactoriesTest extends TestCase
{
    use RefreshDatabase;

    /**
     * @return array<string, array{class-string<Model>}>
     */
    public static function models(): array
    {
        return [
            'Badge' => [Badge::class],
            'Game' => [Game::class],
            'Group' => [Group::class],
            'Player' => [Player::class],
            'Round' => [Round::class],
            'User' => [User::class],
        ];
    }

    /**
     * @param  class-string<Model>  $model
     */
    #[Test]
    #[DataProvider('models')]
    public function each_factory_creates_a_valid_model(string $model): void
    {
        $this->actingAs(User::factory()->create());

        $instance = $model::factory()->create();

        $this->assertModelExists($instance);
    }
}
