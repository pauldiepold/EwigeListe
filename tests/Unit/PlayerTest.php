<?php

namespace Tests\Unit;

use Tests\TestCase;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\RefreshDatabase;

class PlayerTest extends TestCase
{

    use RefreshDatabase;

    /** @test */
    public function a_user_has_groups()
    {
        $user = factory('App\User')->create();

        $this->assertInstanceOf(Collection::class, $user->player->createdGroups);
    }
}
