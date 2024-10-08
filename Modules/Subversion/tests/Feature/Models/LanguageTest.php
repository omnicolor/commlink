<?php

declare(strict_types=1);

namespace Modules\Subversion\Tests\Feature\Models;

use Modules\Subversion\Models\Language;
use PHPUnit\Framework\Attributes\Group;
use PHPUnit\Framework\Attributes\Small;
use RuntimeException;
use Tests\TestCase;

#[Group('subversion')]
#[Small]
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
