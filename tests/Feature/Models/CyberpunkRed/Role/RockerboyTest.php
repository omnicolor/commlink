<?php

declare(strict_types=1);

namespace Tests\Feature\Models\CyberpunkRed\Role;

use App\Models\CyberpunkRed\Role\Rockerboy;
use OutOfBoundsException;

/**
 * Tests for the Rockerboy role.
 * @covers App\Models\CyberpunkRed\Role
 * @covers App\Models\CyberpunkRed\Role\Rockerboy
 * @group cyberpunkred
 * @group models
 * @small
 */
final class RockerboyTest extends \PHPUnit\Framework\TestCase
{
    /**
     * Subject under test.
     * @var Rockerboy
     */
    protected Rockerboy $role;

    /**
     * Set up the subject under test.
     */
    public function setUp(): void
    {
        parent::setUp();
        $this->role = new Rockerboy([
            'act' => Rockerboy::ACT_SOLO,
            'gunning' => Rockerboy::GUNNING_OLD_GROUP_MEMBER,
            'perform' => Rockerboy::PERFORM_CAFE,
            'rank' => 4,
            'type' => Rockerboy::TYPE_MUSICIAN,
        ]);
    }

    /**
     * Test the toString method.
     * @test
     */
    public function testToString(): void
    {
        self::assertSame('Rockerboy', (string)$this->role);
    }

    /**
     * Test getting the act type for a solo rockerboy.
     * @test
     */
    public function testGetActSolo(): void
    {
        self::assertSame('solo', $this->role->getAct());
    }

    /**
     * Test getting the act type for a rockerboy that's part of a group.
     * @test
     */
    public function testGetActGroup(): void
    {
        $role = new Rockerboy([
            'act' => Rockerboy::ACT_GROUP,
            'gunning' => Rockerboy::GUNNING_OLD_GROUP_MEMBER,
            'perform' => Rockerboy::PERFORM_CAFE,
            'rank' => 4,
            'type' => Rockerboy::TYPE_MUSICIAN,
        ]);
        self::assertSame('group', $role->getAct());
    }

    /**
     * Test getting the act type if the value's out of range.
     * @test
     */
    public function testGetActOutOfRange(): void
    {
        $role = new Rockerboy([
            'act' => 99,
            'gunning' => Rockerboy::GUNNING_OLD_GROUP_MEMBER,
            'perform' => Rockerboy::PERFORM_CAFE,
            'rank' => 4,
            'type' => Rockerboy::TYPE_MUSICIAN,
        ]);
        self::expectException(OutOfBoundsException::class);
        $role->getAct();
    }

    /**
     * Test getting who's gunning for the rockerboy.
     * @test
     */
    public function testGetWhosGunning(): void
    {
        self::assertSame(
            'Old group member who thinks you did them dirty.',
            $this->role->getWhosGunning()
        );
    }

    /**
     * Test getting who's gunning if the value is out of range.
     * @test
     */
    public function testGetWhosGunningOutOfRange(): void
    {
        $role = new Rockerboy([
            'act' => RockerBoy::ACT_SOLO,
            'gunning' => 99,
            'perform' => Rockerboy::PERFORM_CAFE,
            'rank' => 4,
            'type' => Rockerboy::TYPE_MUSICIAN,
        ]);
        self::expectException(OutOfBoundsException::class);
        $role->getWhosGunning();
    }

    /**
     * Test getting the type for a musician.
     * @test
     */
    public function testGetTypeMusician(): void
    {
        self::assertSame('musician', $this->role->getType());
    }

    /**
     * Test getting the type for a slam poet.
     * @test
     */
    public function testGettingSlamPoetRockerboy(): void
    {
        $role = new Rockerboy([
            'act' => Rockerboy::ACT_GROUP,
            'gunning' => Rockerboy::GUNNING_OLD_GROUP_MEMBER,
            'perform' => Rockerboy::PERFORM_CAFE,
            'rank' => 4,
            'type' => Rockerboy::TYPE_SLAM_POET,
        ]);
        self::assertSame('slam poet', $role->getType());
    }

    /**
     * Test getting the type for a street artist.
     * @test
     */
    public function testGettingStreetArtistRockerboy(): void
    {
        $role = new Rockerboy([
            'act' => Rockerboy::ACT_GROUP,
            'gunning' => Rockerboy::GUNNING_OLD_GROUP_MEMBER,
            'perform' => Rockerboy::PERFORM_CAFE,
            'rank' => 4,
            'type' => Rockerboy::TYPE_STREET_ARTIST,
        ]);
        self::assertSame('street artist', $role->getType());
    }

    /**
     * Test getting the type if the value is out of range.
     * @test
     */
    public function testGetTypeOutOfRange(): void
    {
        $role = new Rockerboy([
            'act' => RockerBoy::ACT_SOLO,
            'gunning' => Rockerboy::GUNNING_OLD_GROUP_MEMBER,
            'perform' => Rockerboy::PERFORM_CAFE,
            'rank' => 4,
            'type' => 99,
        ]);
        self::expectException(OutOfBoundsException::class);
        $role->getType();
    }
}
