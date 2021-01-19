<?php

declare(strict_types=1);

namespace Tests\Unit\Shadowrun5E;

use App\Models\Shadowrun5E\AdeptPower;
use App\Models\Shadowrun5E\Augmentation;
use App\Models\Shadowrun5E\AugmentationArray;

/**
 * Tests for the AugmentationArray class.
 * @covers \App\Models\Shadowrun5E\AugmentationArray
 */
final class AugmentationArrayTest extends \Tests\TestCase
{
    /**
     * Subject under test.
     * @var AugmentationArray<Augmentation>
     */
    protected AugmentationArray $augmentations;

    /**
     * Set up a clean subject.
     */
    public function setUp(): void
    {
        parent::setUp();
        $this->augmentations = new AugmentationArray();
    }

    /**
     * Test an empty AugmentationArray.
     * @test
     */
    public function testEmpty(): void
    {
        self::assertEmpty($this->augmentations);
    }

    /**
     * Test adding an augmentation to the array.
     * @test
     */
    public function testAdd(): void
    {
        $this->augmentations[] = new Augmentation('cyberears-1');
        self::assertNotEmpty($this->augmentations);
    }

    /**
     * Test that adding a non-augmentation to the array throws an exception.
     * @test
     */
    public function testAddWrongTypeException(): void
    {
        $this->expectException(\TypeError::class);
        // @phpstan-ignore-next-line
        $this->augmentations[]
            = new AdeptPower('improved-sense-direction-sense');
    }

    /**
     * Test that adding a non-augmentation to the array doesn't add it.
     * @test
     */
    public function testAddWrongTypeDoesntAdd(): void
    {
        try {
            // @phpstan-ignore-next-line
            $this->augmentations[]
                = new AdeptPower('improved-sense-direction-sense');
        } catch (\TypeError $e) {
            // Ignored
        }
        self::assertEmpty($this->augmentations);
    }
}
