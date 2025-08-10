<?php

declare(strict_types=1);

namespace Modules\Battletech\Tests\Feature\Models;

use Modules\Battletech\Enums\QualityType;
use Modules\Battletech\Models\Quality;
use PHPUnit\Framework\Attributes\Group;
use PHPUnit\Framework\Attributes\Small;
use Tests\TestCase;

#[Group('battletech')]
#[Small]
final class QualityTest extends TestCase
{
    public function testOpposes(): void
    {
        $quality = Quality::findOrFail('animal-antipathy');
        self::assertSame(['animal-empathy'], $quality->opposes);
    }

    public function testOpposesNothing(): void
    {
        $quality = Quality::findOrFail('ambidextrous');
        self::assertSame([], $quality->opposes);
    }

    public function testToString(): void
    {
        $quality = Quality::findOrFail('ambidextrous');
        self::assertSame('Ambidextrous', (string)$quality);
    }

    public function testQualityType(): void
    {
        $quality = Quality::findOrFail('ambidextrous');
        self::assertSame([QualityType::Positive], $quality->types);
        $quality = Quality::findOrFail('animal-antipathy');
        self::assertSame(
            [QualityType::Negative, QualityType::Opposed],
            $quality->types,
        );
    }
}
