<?php

declare(strict_types=1);

namespace Modules\Shadowrun6e\Tests\Feature\Models;

use Modules\Shadowrun6e\Models\ActiveSkill;
use Modules\Shadowrun6e\Models\Sprite;
use Modules\Shadowrun6e\Models\SpritePower;
use PHPUnit\Framework\Attributes\Group;
use PHPUnit\Framework\Attributes\Small;
use RangeException;
use Tests\TestCase;

#[Group('shadowrun')]
#[Group('shadowrun6e')]
#[Small]
final class SpriteTest extends TestCase
{
    public function testToString(): void
    {
        $sprite = Sprite::findOrFail('courier');
        self::assertSame('Courier', (string)$sprite);
    }

    public function testSetLevelTooLow(): void
    {
        $sprite = new Sprite();
        self::expectException(RangeException::class);
        $sprite->level = -1;
    }

    public function testAttack(): void
    {
        $sprite = Sprite::findOrFail('courier');
        self::assertSame('L', $sprite->attack);

        $sprite->level = 4;
        self::assertSame(4, $sprite->attack);
    }

    public function testDataProcessing(): void
    {
        $sprite = Sprite::findOrFail('courier');
        self::assertSame('L+1', $sprite->data_processing);

        $sprite->level = 4;
        self::assertSame(5, $sprite->data_processing);
    }

    public function testFirewall(): void
    {
        $sprite = Sprite::findOrFail('courier');
        self::assertSame('L+2', $sprite->firewall);

        $sprite->level = 4;
        self::assertSame(6, $sprite->firewall);
    }

    public function testInitiative(): void
    {
        $sprite = Sprite::findOrFail('courier');
        self::assertSame('(L*2)+1+4d6', $sprite->initiative);

        $sprite->level = 4;
        self::assertSame('9+4d6', $sprite->initiative);
    }

    public function testPowers(): void
    {
        $sprite = Sprite::findOrFail('courier');
        self::assertEquals(
            [
                SpritePower::findOrFail('cookie'),
                SpritePower::findOrFail('hash'),
            ],
            $sprite->powers,
        );
    }

    public function testSkills(): void
    {
        $sprite = Sprite::findOrFail('courier');
        self::assertEquals(
            [
                ActiveSkill::findOrFail('electronics'),
                ActiveSkill::findOrFail('cracking'),
            ],
            $sprite->skills,
        );
    }

    public function testSleaze(): void
    {
        $sprite = Sprite::findOrFail('courier');
        self::assertSame('L+3', $sprite->sleaze);

        $sprite->level = 4;
        self::assertSame(7, $sprite->sleaze);
    }
}
