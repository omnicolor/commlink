<?php

declare(strict_types=1);

namespace Tests\Unit\Models\Shadowrun5E;

use App\Models\Shadowrun5E\Quality;

/**
 * Qualities tests for Quality class.
 * @covers \App\Models\Shadowrun5E\Quality
 * @group shadowrun
 * @group shadowrun5e
 * @group models
 */
final class QualityTest extends \Tests\TestCase
{
    /**
     * Test that loading an invalid quality throws an exception.
     * @test
     */
    public function testLoadingInvalidQualityThrowsException(): void
    {
        Quality::$qualities = null;
        self::expectException(\RuntimeException::class);
        self::expectExceptionMessage('Quality ID "not-found-id" is invalid');
        new Quality('not-found-id');
    }

    /**
     * Test that loading a quality sets the ID.
     * @return Quality
     * @test
     */
    public function testLoadingLuckyId(): Quality
    {
        $quality = new Quality('lucky');
        self::assertEquals('lucky', $quality->id);
        return $quality;
    }

    /**
     * Test that loading a quality sets the description.
     * @depends testLoadingLuckyId
     * @param Quality $quality
     * @test
     */
    public function testLoadingLuckyDescription(Quality $quality): void
    {
        $expected = 'The dice roll and the coin flips this character\'s way '
            . 'more often than not, giving her the chance to drop jaws in '
            . 'amazement at her good fortune. Lucky allows a character to '
            . 'possess an Edge attribute one point higher than his metatype '
            . 'maximum (for example, a human character could raise her Edge to '
            . '8). Note that taking this quality does not actually increase '
            . 'the character\'s current Edge rating, it just allows her the '
            . 'opportunity to do so; the Karma cost for gaining the extra '
            . 'point must still be paid. This quality may only be taken once '
            . 'and must be approved by the gamemaster. The Lucky quality '
            . 'cannot be combined with Exceptional Attribute.';
        self::assertEquals($expected, $quality->description);
    }

    /**
     * Test that loading a quality with effects sets the effects property.
     * @depends testLoadingLuckyId
     * @param Quality $quality
     * @test
     */
    public function testLoadingLuckySetsEffects(Quality $quality): void
    {
        $expected = ['maximum-edge' => 7, 'notoriety' => -1];
        self::assertEquals($expected, $quality->effects);
    }

    /**
     * Test that loading a quality with incompatibilities sets the property.
     * @depends testLoadingLuckyId
     * @param Quality $quality
     * @test
     */
    public function testLoadingLuckySetsIncompatibilities(
        Quality $quality
    ): void {
        $expected = [
            'lucky',
        ];
        self::assertEquals($expected, $quality->incompatibilities);
    }

    /**
     * Test that loading a quality sets the karma value.
     * @depends testLoadingLuckyId
     * @param Quality $quality
     * @test
     */
    public function testLoadingLuckyKarma(Quality $quality): void
    {
        self::assertEquals(-12, $quality->karma);
    }

    /**
     * Test that loading a quality sets the name.
     * @depends testLoadingLuckyId
     * @param Quality $quality
     * @test
     */
    public function testLoadingLuckyName(Quality $quality): void
    {
        self::assertEquals('Lucky', $quality->name);
    }

    /**
     * Test that loading a quality from core rulebook doesn't change the
     * ruleset.
     * @depends testLoadingLuckyId
     * @param Quality $quality
     * @test
     */
    public function testLoadingLuckyRuleset(Quality $quality): void
    {
        self::assertEquals('core', $quality->ruleset);
    }

    /**
     * Test the __toString method.
     * @depends testLoadingLuckyId
     * @param Quality $quality
     * @test
     */
    public function testLoadingLuckyToString(Quality $quality): void
    {
        self::assertEquals('Lucky', (string)$quality);
    }

    /**
     * Test loading Indomitable.
     * @test
     */
    public function testLoadingIndomitable(): void
    {
        $quality = new Quality(
            'indomitable-2',
            ['limits' => ['mental', 'mental']]
        );
        self::assertEquals(2, $quality->effects['mental-limit']);
        self::assertEquals('Indomitable (mental, mental)', (string)$quality);
    }

    /**
     * Test loading an allergy.
     * @test
     */
    public function testLoadingAllergy(): void
    {
        $quality = new Quality(
            'allergy-uncommon-mild',
            ['allergy' => 'alcohol']
        );
        self::assertEquals(
            'Allergy (Uncommon Mild - alcohol)',
            (string)$quality
        );
    }

    /**
     * Test loading an addiction.
     * @test
     */
    public function testLoadingAddiction(): void
    {
        $quality = new Quality(
            'addiction-mild',
            ['addiction' => 'alcohol']
        );
        self::assertEquals(
            'Addiction (Mild - alcohol)',
            (string)$quality
        );
    }

    /**
     * Test findByName() with a quality that isn't found.
     * @test
     */
    public function testFindByNameNotFound(): void
    {
        self::expectException(\RuntimeException::class);
        self::expectExceptionMessage('Quality name "Not Found" was not found');
        Quality::findByName('Not Found');
    }

    /**
     * Test findByName() with a quality that is found.
     * @test
     */
    public function testFindByName(): void
    {
        self::assertInstanceOf(Quality::class, Quality::findByName('Lucky'));
    }

    /**
     * Test initializing the Aptitude quality.
     * @test
     */
    public function testAptitude(): void
    {
        $quality = new Quality('aptitude-alchemy');
        self::assertSame('Aptitude (Alchemy)', (string)$quality);
    }

    /**
     * Test initializing the Exceptional Attribute quality.
     * @test
     */
    public function testExceptionalAttribute(): void
    {
        $quality = new Quality('exceptional-attribute-body');
        self::assertSame('Exceptional Attribute (Body)', (string)$quality);
    }

    /**
     * Test initializing a quality with a severity.
     * @test
     */
    public function testQualityWithSeverity(): void
    {
        $quality = new Quality('fame-local');
        self::assertSame('Fame (Local)', (string)$quality);
    }
}
