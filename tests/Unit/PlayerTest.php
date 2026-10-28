<?php

namespace Tests\Unit;

use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\RefreshDatabase;

class PlayerTest extends TestCase
{

    use RefreshDatabase;

    #[Test]
    public function a_user_has_groups()
    {
        $user = \App\User::factory()->create();

        $this->assertInstanceOf(Collection::class, $user->player->createdGroups);
    }
}
