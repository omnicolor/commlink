<?php

declare(strict_types=1);

namespace Modules\Shadowrun5e\Tests\Feature\Models;

use Modules\Shadowrun5e\Models\Spirit;
use Modules\Shadowrun5e\Models\SpiritArray;
use PHPUnit\Framework\Attributes\Group;
use PHPUnit\Framework\Attributes\Small;
use Tests\TestCase;
use TypeError;
use stdClass;

#[Group('shadowrun')]
#[Group('shadowrun5e')]
#[Small]
final class SpiritArrayTest extends TestCase
{
    /**
     * Subject under test.
     * @var SpiritArray<Spirit>
     */
    protected SpiritArray $spirits;

    /**
     * Set up a clean subject.
     */
    public function setUp(): void
    {
        parent::setUp();
        $this->spirits = new SpiritArray();
    }

    /**
     * Test an empty SpiritArray.
     */
    public function testEmpty(): void
    {
        self::assertEmpty($this->spirits);
    }

    /**
     * Test adding a spirit to the array.
     */
    public function testAdd(): void
    {
        $this->spirits[] = new Spirit('air');
        self::assertNotEmpty($this->spirits);
    }

    /**
     * Test that adding a non-spirit to the array throws an exception.
     */
    public function testAddWrongTypeException(): void
    {
        self::expectException(TypeError::class);
        // @phpstan-ignore-next-line
        $this->spirits[] = new stdClass();
    }

    /**
     * Test that adding a non-spirit to the array doesn't add it.
     */
    public function testAddWrongTypeDoesntAdd(): void
    {
        try {
            // @phpstan-ignore-next-line
            $this->spirits->offsetSet(spirit: new stdClass());
        } catch (TypeError) {
            // Ignored
        }
        self::assertEmpty($this->spirits);
    }
}
