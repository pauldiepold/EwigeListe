<?php

namespace Tests\Feature;


use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ManageLiveGamesTest extends TestCase
{
    use RefreshDatabase, WithFaker;

    /** @test */
    public function test()
    {
        $this->assertTrue(true);
    }

}
