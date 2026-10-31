<?php

namespace Tests\Unit;

use App\Models\User;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Foundation\Testing\RefreshDatabase;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

class PlayerTest extends TestCase
{
    use RefreshDatabase;

    #[Test]
    public function a_user_has_groups()
    {
        $user = User::factory()->create();

        $this->assertInstanceOf(Collection::class, $user->player->createdGroups);
    }
}
