<?php

namespace Tests\Feature;

use PHPUnit\Framework\Attributes\Test;

use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ManageLiveGamesTest extends TestCase
{
    use RefreshDatabase, WithFaker;

    #[Test]
    public function test()
    {
        $this->assertTrue(true);
    }

}
