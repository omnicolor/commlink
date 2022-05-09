<?php

declare(strict_types=1);

namespace Tests\Feature\Models\Shadowrun5E;

use App\Models\Shadowrun5E\MartialArtsTechnique;

/**
 * Unit tests for MartialArtsTechnique class.
 * @group models
 * @group shadowrun
 * @group shadowrun5e
 * @small
 */
final class MartialArtsTechniqueTest extends \Tests\TestCase
{
    /**
     * Test trying to load an invalid MartialArtsTechnique.
     * @test
     */
    public function testLoadInvalid(): void
    {
        self::expectException(\RuntimeException::class);
        self::expectExceptionMessage(
            'Martial Arts Technique ID "foo" is invalid'
        );
        new MartialArtsTechnique('foo');
    }

    /**
     * Test the constructor for a technique without a subname.
     * @test
     */
    public function testConstructor(): void
    {
        $technique = new MartialArtsTechnique('constrictors-crush');
        self::assertNotNull($technique->description);
        self::assertEquals('constrictors-crush', $technique->id);
        self::assertEquals('Constrictor\'s Crush', $technique->name);
        self::assertEquals(137, $technique->page);
        self::assertEquals('run-and-gun', $technique->ruleset);
        self::assertNull($technique->subname);
    }

    /**
     * Test the constructor for a technique with a subname.
     * @test
     */
    public function testConstructorSubname(): void
    {
        $technique = new MartialArtsTechnique('called-shot-disarm');
        self::assertEquals('Disarm', $technique->subname);
    }

    /**
     * Test the __toString() method if the technique doesn't have a subname.
     * @test
     */
    public function testToString(): void
    {
        $technique = new MartialArtsTechnique('constrictors-crush');
        self::assertEquals('Constrictor\'s Crush', (string)$technique);
    }

    /**
     * Test the __toString() method if the technique has a subname.
     * @test
     */
    public function testToStringSubname(): void
    {
        $technique = new MartialArtsTechnique('called-shot-disarm');
        self::assertEquals('Called Shot (Disarm)', (string)$technique);
    }
}
