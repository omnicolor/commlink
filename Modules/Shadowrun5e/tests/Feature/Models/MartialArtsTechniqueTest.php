<?php

declare(strict_types=1);

namespace Modules\Shadowrun5e\Tests\Feature\Models;

use Modules\Shadowrun5e\Models\MartialArtsTechnique;
use PHPUnit\Framework\Attributes\Group;
use PHPUnit\Framework\Attributes\Small;
use RuntimeException;
use Tests\TestCase;

#[Group('shadowrun')]
#[Group('shadowrun5e')]
#[Small]
final class MartialArtsTechniqueTest extends TestCase
{
    /**
     * Test trying to load an invalid MartialArtsTechnique.
     */
    public function testLoadInvalid(): void
    {
        self::expectException(RuntimeException::class);
        self::expectExceptionMessage(
            'Martial Arts Technique ID "foo" is invalid'
        );
        new MartialArtsTechnique('foo');
    }

    /**
     * Test the constructor for a technique without a subname.
     */
    public function testConstructor(): void
    {
        $technique = new MartialArtsTechnique('constrictors-crush');
        self::assertEquals('constrictors-crush', $technique->id);
        self::assertEquals('Constrictor\'s Crush', $technique->name);
        self::assertEquals(137, $technique->page);
        self::assertEquals('run-and-gun', $technique->ruleset);
        self::assertNull($technique->subname);
    }

    /**
     * Test the constructor for a technique with a subname.
     */
    public function testConstructorSubname(): void
    {
        $technique = new MartialArtsTechnique('called-shot-disarm');
        self::assertEquals('Disarm', $technique->subname);
    }

    /**
     * Test the __toString() method if the technique doesn't have a subname.
     */
    public function testToString(): void
    {
        $technique = new MartialArtsTechnique('constrictors-crush');
        self::assertEquals('Constrictor\'s Crush', (string)$technique);
    }

    /**
     * Test the __toString() method if the technique has a subname.
     */
    public function testToStringSubname(): void
    {
        $technique = new MartialArtsTechnique('called-shot-disarm');
        self::assertEquals('Called Shot (Disarm)', (string)$technique);
    }
}
