<?php

declare(strict_types=1);

namespace Modules\Cyberpunkred\Tests\Feature\Models\Role;

use Modules\Cyberpunkred\Models\Role\Fixer;
use OutOfBoundsException;
use PHPUnit\Framework\Attributes\Group;
use PHPUnit\Framework\Attributes\Small;
use PHPUnit\Framework\TestCase;

#[Group('cyberpunkred')]
#[Small]
final class FixerTest extends TestCase
{
    public function testToString(): void
    {
        $fixer = new Fixer([
            'rank' => 4,
            'type' => Fixer::TYPE_BROKER_DEALS,
        ]);
        self::assertSame('Fixer', (string)$fixer);
    }

    /**
     * Test that all of the different fixer types return information for
     * getType().
     */
    public function testGetType(): void
    {
        $types = [
            Fixer::TYPE_BROKER_DEALS,
            Fixer::TYPE_PROCURE_ATYPICAL,
            Fixer::TYPE_BROKER_SERVICES,
            Fixer::TYPE_SUPPLY_REGULAR,
            Fixer::TYPE_PROCURE_ILLEGAL,
            Fixer::TYPE_SUPPLY_RESOURCES,
            Fixer::TYPE_OPERATE_NIGHT_MARKETS,
            Fixer::TYPE_BROKER_CONTRACTS,
            Fixer::TYPE_BROKER_FENCE,
            Fixer::TYPE_EXCLUSIVE_AGENT,
        ];

        foreach ($types as $type) {
            $fixer = new Fixer([
                'rank' => 4,
                'type' => $type,
            ]);
            self::assertNotSame('', $fixer->getType());
        }
    }

    /**
     * Test setting the fixer's type to an invalid value throws an exception.
     */
    public function testGetTypeInvalid(): void
    {
        $fixer = new Fixer(['rank' => 4, 'type' => 42]);
        self::expectException(OutOfBoundsException::class);
        $fixer->getType();
    }
}
