<?php

declare(strict_types=1);

namespace Modules\Shadowrun6e\Tests\Feature\ValueObjects;

use Illuminate\Foundation\Testing\WithFaker;
use Modules\Shadowrun6e\Enums\LanguageLevel;
use Modules\Shadowrun6e\ValueObjects\Language;
use PHPUnit\Framework\Attributes\Group;
use PHPUnit\Framework\Attributes\Small;
use Tests\TestCase;

#[Group('shadowrun')]
#[Group('shadowrun6e')]
#[Small]
final class LanguageTest extends TestCase
{
    use WithFaker;

    public function testToString(): void
    {
        $language = new Language('English');
        self::assertSame('English', (string)$language);

        $language = new Language('Spanish', LanguageLevel::Specialist);
        self::assertSame('Spanish (S)', (string)$language);

        $language = new Language('Or\'zet', LanguageLevel::Expert);
        self::assertSame('Or\'zet (E)', (string)$language);

        $language = new Language('Japanese', LanguageLevel::Native);
        self::assertSame('Japanese (N)', (string)$language);
    }
}
