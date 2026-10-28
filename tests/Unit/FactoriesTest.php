<?php

namespace Tests\Unit;

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
            'Badge' => [\App\Badge::class],
            'Comment' => [\App\Comment::class],
            'Game' => [\App\Game::class],
            'Group' => [\App\Group::class],
            'Player' => [\App\Player::class],
            'Round' => [\App\Round::class],
            'User' => [\App\User::class],
        ];
    }

    /**
     * @param  class-string<Model>  $model
     */
    #[Test]
    #[DataProvider('models')]
    public function each_factory_creates_a_valid_model(string $model): void
    {
        $this->actingAs(\App\User::factory()->create());

        $instance = $model::factory()->create();

        $this->assertModelExists($instance);
    }
}
