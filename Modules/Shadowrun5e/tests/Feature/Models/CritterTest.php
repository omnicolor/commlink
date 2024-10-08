<?php

declare(strict_types=1);

namespace Modules\Shadowrun5e\Tests\Feature\Models;

use Modules\Shadowrun5e\Models\Critter;
use PHPUnit\Framework\Attributes\Group;
use PHPUnit\Framework\Attributes\Small;
use RuntimeException;
use Tests\TestCase;

#[Group('shadowrun')]
#[Group('shadowrun5e')]
#[Small]
final class CritterTest extends TestCase
{
    /**
     * Test trying to load an invalid critter.
     */
    public function testInvalid(): void
    {
        self::expectException(RuntimeException::class);
        self::expectExceptionMessage('Critter ID "not-found" is invalid');
        new Critter('not-found');
    }

    /**
     * Test loading a valid, magical critter.
     */
    public function testLoad(): void
    {
        $critter = new Critter('barghest');

        self::assertSame('Barghest', (string)$critter);

        self::assertSame(8, $critter->body);
        self::assertSame(5, $critter->agility);
        self::assertSame(6, $critter->reaction);
        self::assertSame(6, $critter->strength);
        self::assertSame(4, $critter->willpower);
        self::assertSame(2, $critter->logic);
        self::assertSame(5, $critter->intuition);
        self::assertSame(5, $critter->charisma);
        self::assertSame(4, $critter->edge);
        self::assertSame(6.0, $critter->essence);
        self::assertSame(5, $critter->magic);
        self::assertNull($critter->resonance);
        self::assertSame(11, $critter->initiative_base);
        self::assertSame(2, $critter->initiative_dice);
        self::assertSame(12, $critter->condition_physical);
        self::assertSame(10, $critter->condition_stun);
        self::assertSame(3, $critter->armor);
        self::assertNotNull($critter->habitat);

        self::assertCount(3, $critter->skills);
        self::assertCount(1, $critter->powers);
        self::assertCount(0, $critter->weaknesses);
    }

    /**
     * Test loading a critter with a vulnerability.
     */
    public function testLoadWithVulnerability(): void
    {
        $critter = new Critter('cockatrice');
        self::assertCount(1, $critter->weaknesses);
        self::assertSame(
            'Vulnerability - own gaze',
            (string)$critter->weaknesses[0]
        );
    }
}
