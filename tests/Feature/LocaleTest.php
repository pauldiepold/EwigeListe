<?php

namespace Tests\Feature;

use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

/**
 * Sichert ab, dass die deutschen Sprachdateien unter `resources/lang/de` wirklich greifen
 * (`app.locale` muss genau ihrem Ordnernamen entsprechen, nicht `de_DE`).
 */
class LocaleTest extends TestCase
{
    #[Test]
    public function framework_messages_are_translated_into_german(): void
    {
        $this->assertSame('de', app()->getLocale());
        $this->assertSame('Vorname muss ausgefüllt sein.', __('validation.required', ['attribute' => 'Vorname']));
    }

    #[Test]
    public function carbon_dates_are_german(): void
    {
        $this->assertSame('November', now()->setMonth(11)->isoFormat('MMMM'));
    }
}
