<?php

namespace Tests\Unit;

use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;

class RoundTest extends TestCase
{
    use RefreshDatabase;

    #[Test]
    public function test()
    {
        $this->assertTrue(true);
    }
}
