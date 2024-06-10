<?php

declare(strict_types=1);

namespace Tests\Feature\Models\Shadowrun5e;

use App\Models\Shadowrun5e\AdeptPower;
use App\Models\Shadowrun5e\AdeptPowerArray;
use PHPUnit\Framework\Attributes\Group;
use PHPUnit\Framework\Attributes\Small;
use Tests\TestCase;
use TypeError;
use stdClass;

#[Group('shadowrun')]
#[Group('shadowrun5e')]
#[Small]
final class AdeptPowerArrayTest extends TestCase
{
    /**
     * Subject under test.
     * @var AdeptPowerArray<AdeptPower>
     */
    protected AdeptPowerArray $powers;

    /**
     * Set up a clean subject.
     */
    public function setUp(): void
    {
        parent::setUp();
        $this->powers = new AdeptPowerArray();
    }

    /**
     * Test an empty AdeptPowerArray.
     */
    public function testEmpty(): void
    {
        self::assertEmpty($this->powers);
    }

    /**
     * Test adding a power to the array.
     */
    public function testAdd(): void
    {
        $this->powers[] = new AdeptPower('improved-sense-direction-sense');
        self::assertNotEmpty($this->powers);
    }

    /**
     * Test that adding a non-power to the array throws an exception.
     */
    public function testAddWrongTypeException(): void
    {
        self::expectException(TypeError::class);
        // @phpstan-ignore-next-line
        $this->powers[] = new stdClass();
    }

    /**
     * Test that adding a non-power to the array doesn't add it.
     */
    public function testAddWrongTypeDoesntAdd(): void
    {
        try {
            // @phpstan-ignore-next-line
            $this->powers->offsetSet(power: new stdClass());
        } catch (TypeError) {
            // Ignored
        }
        self::assertEmpty($this->powers);
    }
}
