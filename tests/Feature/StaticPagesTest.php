<?php

namespace Tests\Feature;

use Inertia\Testing\AssertableInertia as Assert;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

class StaticPagesTest extends TestCase
{
    /**
     * @return array<string, array{string, string}>
     */
    public static function staticPages(): array
    {
        return [
            'Impressum' => ['impressum', 'Static/Impressum'],
            'Datenschutz' => ['datenschutz', 'Static/Datenschutz'],
            'Regeln' => ['regeln', 'Static/Regeln'],
        ];
    }

    #[Test]
    #[DataProvider('staticPages')]
    public function guests_can_view_static_pages(string $routeName, string $component): void
    {
        $this->get(route($routeName))
            ->assertOk()
            ->assertInertia(fn (Assert $page) => $page->component($component));
    }
}
