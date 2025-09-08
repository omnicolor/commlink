<?php

declare(strict_types=1);

namespace Modules\Shadowrun5e\Tests\Feature\Models;

use Modules\Shadowrun5e\Models\Quality;
use PHPUnit\Framework\Attributes\Depends;
use PHPUnit\Framework\Attributes\Group;
use PHPUnit\Framework\Attributes\Small;
use RuntimeException;
use Tests\TestCase;

#[Group('shadowrun')]
#[Group('shadowrun5e')]
#[Small]
final class QualityTest extends TestCase
{
    public function testLoadingInvalidQualityThrowsException(): void
    {
        Quality::$qualities = null;
        self::expectException(RuntimeException::class);
        self::expectExceptionMessage('Quality ID "not-found-id" is invalid');
        new Quality('not-found-id');
    }

    public function testLoadingLuckyId(): Quality
    {
        $quality = new Quality('lucky');
        self::assertSame('lucky', $quality->id);
        return $quality;
    }

    #[Depends('testLoadingLuckyId')]
    public function testLoadingLuckySetsEffects(Quality $quality): void
    {
        $expected = ['maximum-edge' => 1, 'notoriety' => -1];
        self::assertSame($expected, $quality->effects);
    }

    #[Depends('testLoadingLuckyId')]
    public function testLoadingLuckySetsIncompatibilities(
        Quality $quality
    ): void {
        $expected = [
            'lucky',
        ];
        self::assertSame($expected, $quality->incompatibilities);
    }

    /**
     * Test that loading a quality sets the karma value.
     */
    #[Depends('testLoadingLuckyId')]
    public function testLoadingLuckyKarma(Quality $quality): void
    {
        self::assertSame(-12, $quality->karma);
    }

    /**
     * Test that loading a quality sets the name.
     */
    #[Depends('testLoadingLuckyId')]
    public function testLoadingLuckyName(Quality $quality): void
    {
        self::assertSame('Lucky', $quality->name);
    }

    /**
     * Test that loading a quality from core rulebook doesn't change the
     * ruleset.
     */
    #[Depends('testLoadingLuckyId')]
    public function testLoadingLuckyRuleset(Quality $quality): void
    {
        self::assertSame('core', $quality->ruleset);
    }

    #[Depends('testLoadingLuckyId')]
    public function testLoadingLuckyToString(Quality $quality): void
    {
        self::assertSame('Lucky', (string)$quality);
    }

    /**
     * Test loading Indomitable.
     */
    public function testLoadingIndomitable(): void
    {
        $quality = new Quality(
            'indomitable-2',
            ['limits' => ['mental', 'mental']]
        );
        self::assertEquals(2, $quality->effects['mental-limit']);
        self::assertSame('Indomitable (mental, mental)', (string)$quality);
    }

    /**
     * Test loading an allergy.
     */
    public function testLoadingAllergy(): void
    {
        $quality = new Quality(
            'allergy-uncommon-mild',
            ['allergy' => 'alcohol']
        );
        self::assertSame(
            'Allergy (Uncommon Mild - alcohol)',
            (string)$quality
        );
    }

    /**
     * Test loading an addiction.
     */
    public function testLoadingAddiction(): void
    {
        $quality = new Quality(
            'addiction-mild',
            ['addiction' => 'alcohol']
        );
        self::assertSame(
            'Addiction (Mild - alcohol)',
            (string)$quality
        );
    }

    /**
     * Test findByName() with a quality that isn't found.
     */
    public function testFindByNameNotFound(): void
    {
        self::expectException(RuntimeException::class);
        self::expectExceptionMessage('Quality name "Not Found" was not found');
        Quality::findByName('Not Found');
    }

    /**
     * Test findByName() with a quality that is found.
     */
    public function testFindByName(): void
    {
        self::assertSame('lucky', Quality::findByName('Lucky')->id);
    }

    /**
     * Test initializing the Aptitude quality.
     */
    public function testAptitude(): void
    {
        $quality = new Quality('aptitude-alchemy');
        self::assertSame('Aptitude (Alchemy)', (string)$quality);
    }

    /**
     * Test initializing the Exceptional Attribute quality.
     */
    public function testExceptionalAttribute(): void
    {
        $quality = new Quality('exceptional-attribute-body');
        self::assertSame('Exceptional Attribute (Body)', (string)$quality);
    }

    /**
     * Test initializing a quality with a severity.
     */
    public function testQualityWithSeverity(): void
    {
        $quality = new Quality('fame-local');
        self::assertSame('Fame (Local)', (string)$quality);
    }

    /**
     * Test loading a mentor spirit quality.
     */
    public function testMentorSpiritQuality(): void
    {
        $rawQuality = [
            'id' => 'mentor-spirit',
            'severity' => 'bear',
        ];
        $quality = new Quality($rawQuality['id'], $rawQuality);
        self::assertSame('Mentor Spirit - Bear', (string)$quality);
    }
}
