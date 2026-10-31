<?php

namespace Tests;

use App\Models\Group;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\TestCase as BaseTestCase;

abstract class TestCase extends BaseTestCase
{
    use CreatesApplication;

    /**
     * Die Ewige Liste (ID 1) existiert in jeder Installation, also auch in jedem Test mit Datenbank.
     */
    protected function setUp(): void
    {
        parent::setUp();

        $this->withoutVite();

        if (in_array(RefreshDatabase::class, class_uses_recursive(static::class), true)) {
            Group::factory()->ewigeListe()->create();
        }
    }
}
