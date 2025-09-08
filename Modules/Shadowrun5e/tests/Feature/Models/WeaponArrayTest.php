<?php

declare(strict_types=1);

namespace Modules\Shadowrun5e\Tests\Feature\Models;

use Modules\Shadowrun5e\Models\Weapon;
use Modules\Shadowrun5e\Models\WeaponArray;
use Override;
use PHPUnit\Framework\Attributes\Group;
use PHPUnit\Framework\Attributes\Small;
use Tests\TestCase;
use TypeError;
use stdClass;

#[Group('shadowrun')]
#[Group('shadowrun5e')]
#[Small]
final class WeaponArrayTest extends TestCase
{
    /**
     * Subject under test.
     * @var WeaponArray<Weapon>
     */
    private WeaponArray $weapons;

    /**
     * Set up a clean subject.
     */
    #[Override]
    public function setUp(): void
    {
        parent::setUp();
        $this->weapons = new WeaponArray();
    }

    /**
     * Test an empty WeaponArray.
     */
    public function testEmpty(): void
    {
        self::assertEmpty($this->weapons);
    }

    /**
     * Test adding a weapon to the array.
     */
    public function testAdd(): void
    {
        $this->weapons[] = new Weapon('ak-98');
        self::assertNotEmpty($this->weapons);
    }

    /**
     * Test that adding a non-weapon to the array throws an exception.
     */
    public function testAddWrongTypeException(): void
    {
        self::expectException(TypeError::class);
        // @phpstan-ignore offsetAssign.valueType
        $this->weapons[] = new stdClass();
        self::assertEmpty($this->weapons);
    }

    /**
     * Test that adding a non-weapon to the array doesn't add it.
     */
    public function testAddWrongTypeDoesntAdd(): void
    {
        try {
            // @phpstan-ignore argument.type
            $this->weapons->offsetSet(weapon: new stdClass());
        } catch (TypeError) {
            // Ignored
        }
        self::assertEmpty($this->weapons);
    }
}
