<?php

declare(strict_types=1);

namespace Tests\Feature\Models\Cyberpunkred\Role;

use App\Models\Cyberpunkred\Role\Fixer;
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
            // @phpstan-ignore-next-line
            self::assertIsString($fixer->getType());
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
