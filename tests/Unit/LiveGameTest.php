<?php

namespace Tests\Unit;

use PHPUnit\Framework\Attributes\Test;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class LiveGameTest extends TestCase
{
    use RefreshDatabase;

    #[Test]
    public function test()
    {
        $this->assertTrue(true);
    }

}
