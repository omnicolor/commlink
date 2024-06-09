<?php

declare(strict_types=1);

namespace Tests\Feature\Models\Shadowrun5e;

use App\Models\Shadowrun5e\Commlink;
use App\Models\Shadowrun5e\Gear;
use App\Models\Shadowrun5e\GearModification;
use App\Models\Shadowrun5e\Program;
use App\Models\Shadowrun5e\Vehicle;
use App\Models\Shadowrun5e\Weapon;
use PHPUnit\Framework\Attributes\Group;
use PHPUnit\Framework\Attributes\Small;
use RuntimeException;
use Tests\TestCase;

#[Group('shadowrun')]
#[Group('shadowrun5e')]
#[Small]
final class GearTest extends TestCase
{
    /**
     * @var Gear Subject under test
     */
    protected Gear $item;

    /**
     * Set up subject under test.
     */
    public function setUp(): void
    {
        parent::setUp();
        $this->item = new Gear('credstick-gold');
    }

    /**
     * Test that loading an invalid item throws an exception.
     */
    public function testLoadingInvalidItemThrowsException(): void
    {
        self::expectException(RuntimeException::class);
        self::expectExceptionMessage('Item ID "invalid-id" is invalid');
        Gear::$gear = null;
        new Gear('invalid-id');
    }

    /**
     * Test that loading an item sets the availability.
     */
    public function testLoadingSetsAvailability(): void
    {
        self::assertSame('5', $this->item->availability);
    }

    /**
     * Test that loading an item sets the base cost.
     */
    public function testLoadingSetsCost(): void
    {
        self::assertSame(100, $this->item->cost);
    }

    /**
     * Test that loading an item sets the item's description.
     */
    public function testLoadingSetsDescription(): void
    {
        self::assertNotNull($this->item->description);
    }

    /**
     * Test that loading an item sets the ID.
     */
    public function testLoadingSetsId(): void
    {
        self::assertSame('credstick-gold', $this->item->id);
    }

    /**
     * Test that loading an items sets the name.
     */
    public function testLoadingSetsName(): void
    {
        self::assertSame('Certified Credstick', $this->item->name);
    }

    /**
     * Test that loading an item doesn't set rating if the item doesn't have
     * one.
     */
    public function testLoadingDoesNotSetRating(): void
    {
        $item = new Gear('credstick-gold');
        self::assertNull($item->rating);
    }

    /**
     * Test that loading an item sets the rating if the item has one.
     */
    public function testLoadingSetsRating(): void
    {
        $item = new Gear('goggles-2');
        self::assertSame(2, $item->rating);
    }

    /**
     * Test that __toString returns the item's name.
     */
    public function testToString(): void
    {
        $item = new Gear('goggles-2');
        self::assertSame('Goggles', (string)$item);
    }

    /**
     * Test the __toString method for an item with a subname.
     */
    public function testToStringWithSubname(): void
    {
        $item = new Gear('commlink-sony-angel');
        self::assertSame('Commlink - Sony Angel', (string)$item);
    }

    /**
     * Test build() with an invalid item.
     */
    public function testBuildInvalid(): void
    {
        self::expectException(RuntimeException::class);
        Gear::build(['id' => 'invalid']);
    }

    /**
     * Test build() with a normal, simple item.
     */
    public function testBuildSimple(): void
    {
        $gear = Gear::build(['id' => 'credstick-gold']);
        self::assertSame('Certified Credstick', $gear->name);
        self::assertEmpty($gear->modifications);
    }

    /**
     * Test build() with modded gear.
     */
    public function testBuildModded(): void
    {
        $gear = Gear::build([
            'id' => 'goggles-2',
            'modifications' => [
                'flare-compensation',
            ],
        ]);
        self::assertSame('Goggles', $gear->name);
        self::assertCount(1, $gear->modifications);
        self::assertSame(
            'Flare compensation',
            // @phpstan-ignore-next-line
            $gear->modifications[0]->name
        );
    }

    /**
     * Test build() with a matrix device that has taken some damage.
     */
    public function testBuildMatrixDeviceWithDamage(): void
    {
        $array = [
            'id' => 'commlink-sony-angel',
            'damage' => 2,
            'active' => true,
            'sin' => 2,
            'marks' => ['host'],
        ];
        /** @var Commlink $gear */
        $gear = Gear::build($array);
        self::assertInstanceOf(Commlink::class, $gear);
        self::assertSame(2, $gear->damage);
        self::assertTrue($gear->active);
        self::assertEmpty($gear->programs);
        self::assertSame(2, $gear->sin);
        self::assertSame(['host'], $gear->marks);
    }

    /**
     * Test build() with a RCC that has vehicle- and weapon-specific programs.
     */
    public function testBuildRccWithVehicleAndWeaponPrograms(): void
    {
        $array = [
            'id' => 'commlink-sony-angel',
            'active' => false,
            'programsInstalled' => [
                ['id' => 'armor', 'vehicle' => 'mct-fly-spy'],
                ['id' => 'armor', 'weapon' => 'ak-98'],
                ['id' => 'armor'],
            ],
            'programsRunning' => ['armor'],
            'slavedDevices' => ['foo'],
            'subname' => 'Da Deck!',
        ];
        /** @var Commlink $gear */
        $gear = Gear::build($array);
        self::assertInstanceOf(Commlink::class, $gear);
        self::assertFalse($gear->active);
        self::assertCount(3, $gear->programs);
        self::assertInstanceOf(Program::class, $gear->programs[0]);
        // @phpstan-ignore-next-line
        self::assertInstanceOf(Vehicle::class, $gear->programs[2]->vehicle);
        // @phpstan-ignore-next-line
        self::assertInstanceOf(Weapon::class, $gear->programs[1]->weapon);
        self::assertSame(['foo'], $gear->slavedDevices);
        self::assertSame('Commlink - Da Deck!', (string)$gear);
    }

    /**
     * Test findByName() with an item that isn't found.
     */
    public function testFindByNameNotFound(): void
    {
        self::expectException(RuntimeException::class);
        self::expectExceptionMessage('Gear name "Not Found" was not found');
        Gear::$gear = null;
        Gear::findByName('Not Found');
    }

    /**
     * Test findByName() with an item that is found.
     */
    public function testFindByName(): void
    {
        self::assertInstanceOf(Gear::class, Gear::findByName('Goggles'));
    }

    /**
     * Test findByName() with a subname.
     */
    public function testFindByNameSubname(): void
    {
        self::assertInstanceOf(Gear::class, Gear::findByName('Evo Sublime'));
    }

    /**
     * Test getCost() on an unmodified item.
     */
    public function testGetCost(): void
    {
        $item = new Gear('goggles-2');
        self::assertSame(100, $item->getCost());
    }

    /**
     * Test getCost() on a modified item.
     */
    public function testGetCostModified(): void
    {
        $item = new Gear('goggles-2');
        $item->modifications[] = new GearModification('flare-compensation');
        self::assertSame(350, $item->getCost());
    }

    /**
     * Test getCost() on a multiple modified items.
     */
    public function testGetCostMultipleModified(): void
    {
        $item = new Gear('goggles-2', 2);
        $item->modifications[] = new GearModification('flare-compensation');
        self::assertSame(700, $item->getCost());
    }
}
