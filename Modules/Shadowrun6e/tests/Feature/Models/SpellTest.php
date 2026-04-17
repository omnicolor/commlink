<?php

declare(strict_types=1);

namespace Modules\Shadowrun6e\Tests\Feature\Models;

use DomainException;
use Modules\Shadowrun6e\Enums\DamageType;
use Modules\Shadowrun6e\Enums\SpellAdjustment;
use Modules\Shadowrun6e\Enums\SpellDuration;
use Modules\Shadowrun6e\Models\Character;
use Modules\Shadowrun6e\Models\Spell;
use Modules\Shadowrun6e\ValueObjects\Damage;
use PHPUnit\Framework\Attributes\Group;
use PHPUnit\Framework\Attributes\Small;
use Tests\TestCase;

use const PHP_INT_MAX;

#[Group('shadowrun')]
#[Group('shadowrun6e')]
#[Small]
final class SpellTest extends TestCase
{
    public function testToString(): void
    {
        $spell = Spell::findOrFail('acid-stream');
        self::assertSame('Acid Stream', (string)$spell);
    }

    public function testDamage(): void
    {
        $spell = Spell::findOrFail('acid-stream');
        self::assertSame(
            [
                DamageType::Special,
                DamageType::Physical,
            ],
            $spell->damage,
        );

        $spell = Spell::findOrFail('clout');
        self::assertSame([DamageType::Stun], $spell->damage);

        $spell = Spell::findOrFail('analyze-device');
        self::assertNull($spell->damage);
    }

    public function testDuration(): void
    {
        $spell = Spell::findOrFail('acid-stream');
        self::assertSame(SpellDuration::Instantaneous, $spell->duration);

        $spell = Spell::findOrFail('analyze-device');
        self::assertSame(5, $spell->duration);
    }

    public function testAmpUpNonCombat(): void
    {
        $spell = Spell::findOrFail('analyze-device');
        self::expectException(DomainException::class);
        $spell->adjust(SpellAdjustment::AmpUp);
    }

    public function testCalculateDamageNotCombat(): void
    {
        $spell = Spell::findOrFail('analyze-device');
        self::assertNull(
            $spell->getDamage(new Character(['magic' => 6]), PHP_INT_MAX)
        );
    }

    public function testGetDirectDamage(): void
    {
        $spell = Spell::findOrFail('manaball');
        self::assertEquals(
            new Damage(DamageType::Physical, 4),
            $spell->getDamage(new Character(['magic' => 6]), 4),
        );
    }

    public function testGetDirectDamageAmpedUp(): void
    {
        $spell = Spell::findOrFail('manaball');
        $spell->adjust(SpellAdjustment::AmpUp)
            ->adjust(SpellAdjustment::AmpUp);
        self::assertEquals(
            new Damage(DamageType::Physical, 6),
            $spell->getDamage(new Character(['magic' => 6]), 4),
        );
    }

    public function testIndirectDamageNoNetHits(): void
    {
        $spell = Spell::findOrFail('acid-stream');
        self::assertEquals(
            new Damage(DamageType::Physical, 3),
            $spell->getDamage(new Character(['magic' => 6]), 0),
        );
    }

    public function testIndirectDamageNoNetHitsAmpedUp(): void
    {
        $spell = Spell::findOrFail('acid-stream');
        $spell->adjust(SpellAdjustment::AmpUp);
        self::assertEquals(
            new Damage(DamageType::Physical, 3),
            $spell->getDamage(new Character(['magic' => 4]), 0),
        );
    }
}
