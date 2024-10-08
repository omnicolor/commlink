<?php

declare(strict_types=1);

namespace Modules\Shadowrun5e\Tests\Feature\Models;

use Modules\Shadowrun5e\Models\Program;
use Modules\Shadowrun5e\Models\ProgramArray;
use Modules\Shadowrun5e\Models\Vehicle;
use Modules\Shadowrun5e\Models\Weapon;
use PHPUnit\Framework\Attributes\Group;
use PHPUnit\Framework\Attributes\Small;
use RuntimeException;
use Tests\TestCase;

#[Group('shadowrun')]
#[Group('shadowrun5e')]
#[Small]
final class ProgramTest extends TestCase
{
    /**
     * Test trying to load an invalid program.
     */
    public function testLoadInvalid(): void
    {
        self::expectException(RuntimeException::class);
        self::expectExceptionMessage('Program ID "foo" is invalid');
        new Program('foo');
    }

    /**
     * Test the constructor with a program with a rating.
     */
    public function testConstructor(): void
    {
        $program = new Program('armor');
        self::assertEquals(['cyberdeck', 'rcc'], $program->allowedDevices);
        self::assertEquals('4R', $program->availability);
        self::assertEquals(250, $program->cost);
        self::assertNotEmpty($program->effects);
        self::assertEquals('armor', $program->id);
        self::assertEquals('Armor', $program->name);
        self::assertEquals(245, $program->page);
        self::assertNull($program->rating);
        self::assertFalse($program->running);
        self::assertEquals('core', $program->ruleset);
    }

    /**
     * Test the constructor with a running program with effects.
     */
    public function testConstructorEffects(): void
    {
        $program = new Program('armor', true);
        self::assertSame(['damage-resist' => 2], $program->effects);
        self::assertSame(245, $program->page);
        self::assertTrue($program->running);
        self::assertSame('core', $program->ruleset);
    }

    /**
     * Test the __toString() method.
     */
    public function testToString(): void
    {
        $program = new Program('armor');
        self::assertEquals('Armor', (string)$program);
    }

    /**
     * Test the getCost method.
     */
    public function testGetCost(): void
    {
        $program = new Program('armor');
        self::assertSame(250, $program->getCost());
    }

    /**
     * Test building a program from a string ID that isn't valid.
     */
    public function testBuildFromStringNotFound(): void
    {
        self::expectException(RuntimeException::class);
        self::expectExceptionMessage('Program ID "invalid" is invalid');
        Program::build('invalid', new ProgramArray());
    }

    /**
     * Test building a program from a string.
     */
    public function testBuildFromString(): void
    {
        $program = Program::build('armor', new ProgramArray());
        self::assertSame('Armor', $program->name);
        self::assertFalse($program->running);
    }

    /**
     * Test building a program from a string, that's running.
     */
    public function testBuildFromStringRunning(): void
    {
        $array = new ProgramArray();
        $array[] = new Program('armor');
        $program = Program::build('armor', $array);
        self::assertSame('Armor', $program->name);
        self::assertTrue($program->running);
    }

    /**
     * Test building a program from an array that is used by a vehicle.
     */
    public function testBuildVehicleProgram(): void
    {
        $program = Program::build(
            [
                'id' => 'armor',
                'vehicle' => 'mct-fly-spy',
            ],
            new ProgramArray()
        );
        self::assertFalse(isset($program->weapon));
        self::assertInstanceOf(Vehicle::class, $program->vehicle);
        self::assertFalse($program->running);
    }

    /**
     * Test building a program from an array that is used by a weapon.
     */
    public function testBuildWeaponProgram(): void
    {
        $array = new ProgramArray();
        $array[] = new Program('armor');

        $program = Program::build(
            [
                'id' => 'armor',
                'weapon' => 'ak-98',
            ],
            $array
        );
        self::assertInstanceOf(Weapon::class, $program->weapon);
        self::assertFalse(isset($program->vehicle));
        self::assertTrue($program->running);
    }

    /**
     * Test whether the program is running on a not-running program.
     */
    public function testIsRunningNot(): void
    {
        $program = new Program('armor');
        self::assertFalse($program->isRunning(new ProgramArray()));
    }

    /**
     * Test whether the program is running on a running program.
     */
    public function testIsRunning(): void
    {
        $program = new Program('armor');
        $array = new ProgramArray();
        $array[] = $program;
        self::assertTrue($program->isRunning($array));
    }
}
