<?php

declare(strict_types=1);

namespace Tests\Feature\Models\Cyberpunkred;

use App\Models\Cyberpunkred\Armor;
use App\Models\Cyberpunkred\CostCategory;
use PHPUnit\Framework\Attributes\Group;
use PHPUnit\Framework\Attributes\Small;
use RuntimeException;
use Tests\TestCase;

#[Group('cyberpunkred')]
#[Small]
final class ArmorTest extends TestCase
{
    /**
     * Test loading an invalid armor.
     */
    public function testLoadNotFound(): void
    {
        self::expectException(RuntimeException::class);
        self::expectExceptionMessage('Armor ID "invalid" is invalid');
        new Armor('invalid');
    }

    /**
     * Test loading a valid armor.
     */
    public function testLoadArmor(): void
    {
        $armor = new Armor('light-armorjack');
        self::assertSame('Light armorjack', (string)$armor);
        self::assertSame(CostCategory::Premium, $armor->cost_category);
        self::assertSame(
            'A combination of Kevlar® and plastic meshes inserted into the '
                . 'weave of the fabric.',
            $armor->description
        );
        self::assertSame('light-armorjack', $armor->id);
        self::assertSame(97, $armor->page);
        self::assertSame(0, $armor->penalty);
        self::assertSame('core', $armor->ruleset);
        self::assertSame(11, $armor->stopping_power);
        self::assertSame('Light armorjack', $armor->type);

        self::assertSame(100, $armor->getCost());
    }
}
