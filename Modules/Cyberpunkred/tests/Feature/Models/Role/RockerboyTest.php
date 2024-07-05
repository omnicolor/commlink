<?php

declare(strict_types=1);

namespace Modules\Cyberpunkred\Tests\Feature\Models\Role;

use Modules\Cyberpunkred\Models\Role\Rockerboy;
use OutOfBoundsException;
use PHPUnit\Framework\Attributes\Group;
use PHPUnit\Framework\Attributes\Small;
use PHPUnit\Framework\TestCase;

#[Group('cyberpunkred')]
#[Small]
final class RockerboyTest extends TestCase
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

    public function testToString(): void
    {
        self::assertSame('Rockerboy', (string)$this->role);
    }

    /**
     * Test getting the act type for a solo rockerboy.
     */
    public function testGetActSolo(): void
    {
        self::assertSame('solo', $this->role->getAct());
    }

    /**
     * Test getting the act type for a rockerboy that's part of a group.
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
     */
    public function testGetWhosGunningOutOfRange(): void
    {
        $role = new Rockerboy([
            'act' => Rockerboy::ACT_SOLO,
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
     */
    public function testGetTypeMusician(): void
    {
        self::assertSame('musician', $this->role->getType());
    }

    /**
     * Test getting the type for a slam poet.
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
     */
    public function testGetTypeOutOfRange(): void
    {
        $role = new Rockerboy([
            'act' => Rockerboy::ACT_SOLO,
            'gunning' => Rockerboy::GUNNING_OLD_GROUP_MEMBER,
            'perform' => Rockerboy::PERFORM_CAFE,
            'rank' => 4,
            'type' => 99,
        ]);
        self::expectException(OutOfBoundsException::class);
        $role->getType();
    }
}
