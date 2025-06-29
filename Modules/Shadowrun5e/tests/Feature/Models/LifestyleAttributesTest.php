<?php

declare(strict_types=1);

namespace Modules\Shadowrun5e\Tests\Feature\Models;

use Modules\Shadowrun5e\Models\LifestyleAttributes;
use PHPUnit\Framework\Attributes\Group;
use PHPUnit\Framework\Attributes\Small;
use PHPUnit\Framework\TestCase;
use RuntimeException;

#[Group('shadowrun')]
#[Group('shadowrun5e')]
#[Small]
final class LifestyleAttributesTest extends TestCase
{
    /**
     * Test trying to initialize attributes without required data.
     */
    public function testMissingAttributes(): void
    {
        self::expectException(RuntimeException::class);
        self::expectExceptionMessage('Lifestyle attributes missing');
        new LifestyleAttributes([]);
    }

    /**
     * Test initializing lifestyle attributes with data.
     */
    public function testAttributes(): void
    {
        $attributes = new LifestyleAttributes([
            'comforts' => 5,
            'comfortsMax' => 7,
            'neighborhood' => 5,
            'neighborhoodMax' => 7,
            'security' => 5,
            'securityMax' => 8,
        ]);
        self::assertSame(5, $attributes->comforts);
        self::assertSame(7, $attributes->comfortsMax);
        self::assertSame(5, $attributes->neighborhood);
        self::assertSame(7, $attributes->neighborhoodMax);
        self::assertSame(5, $attributes->security);
        self::assertSame(8, $attributes->securityMax);
    }
}
