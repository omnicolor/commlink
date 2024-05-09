<?php

declare(strict_types=1);

namespace Tests\Feature\Models\Subversion;

use App\Models\Subversion\Language;
use RuntimeException;
use Tests\TestCase;

/**
 * @group subversion
 * @small
 */
final class LanguageTest extends TestCase
{
    public function testNotFound(): void
    {
        self::expectException(RuntimeException::class);
        self::expectExceptionMessage('Language "not-found" not found');
        new Language('not-found');
    }

    public function testConstructor(): void
    {
        $language = new Language('fae');
        self::assertSame('Fae', (string)$language);
        self::assertSame(97, $language->page);
    }

    public function testAll(): void
    {
        $languages = Language::all();
        self::assertCount(7, $languages);
    }
}
