<?php

declare(strict_types=1);

namespace Modules\Shadowrun6e\Tests\Feature\Models;

use Modules\Shadowrun6e\Models\Quality;
use PHPUnit\Framework\Attributes\Group;
use PHPUnit\Framework\Attributes\Small;
use RuntimeException;
use Tests\TestCase;

#[Group('shadowrun')]
#[Group('shadowrun6e')]
#[Small]
final class QualityTest extends TestCase
{
    public function testLoadInvalid(): void
    {
        self::expectException(RuntimeException::class);
        Quality::findOrFail('not-found');
    }

    public function testLoad(): void
    {
        $quality = Quality::findOrFail('ambidextrous');

        self::assertSame('Ambidextrous', (string)$quality);
        self::assertSame('ambidextrous', $quality->id);
        self::assertSame(4, $quality->karma_cost);
        self::assertEmpty($quality->effects);
        self::assertStringContainsString('off-hand', $quality->description);
        self::assertSame('core', $quality->ruleset);
        self::assertSame(70, $quality->page);
        self::assertNull($quality->level);
    }

    public function testEffects(): void
    {
        $quality = Quality::findOrFail('exceptional-attribute-strength');
        // @phpstan-ignore offsetAccess.notFound
        self::assertSame(1, $quality->effects['maximum-strength']);
        $quality = Quality::findOrFail('ambidextrous');
        self::assertEmpty($quality->effects);
    }
}
