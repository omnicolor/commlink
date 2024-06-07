<?php

declare(strict_types=1);

namespace Tests\Feature\Models\Shadowrun5e;

use App\Models\Shadowrun5e\Augmentation;
use App\Models\Shadowrun5e\AugmentationArray;
use PHPUnit\Framework\Attributes\Small;
use Tests\TestCase;
use TypeError;
use stdClass;

/**
 * Tests for the AugmentationArray class.
 * @group shadowrun
 * @group shadowrun5e
 */
#[Small]
final class AugmentationArrayTest extends TestCase
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
     */
    public function testEmpty(): void
    {
        self::assertEmpty($this->augmentations);
    }

    /**
     * Test adding an augmentation to the array.
     */
    public function testAdd(): void
    {
        $this->augmentations[] = new Augmentation('cyberears-1');
        self::assertNotEmpty($this->augmentations);
    }

    /**
     * Test that adding a non-augmentation to the array throws an exception.
     */
    public function testAddWrongTypeException(): void
    {
        $this->expectException(TypeError::class);
        // @phpstan-ignore-next-line
        $this->augmentations[] = new stdClass();
    }

    /**
     * Test that adding a non-augmentation to the array doesn't add it.
     */
    public function testAddWrongTypeDoesntAdd(): void
    {
        try {
            // @phpstan-ignore-next-line
            $this->augmentations->offsetSet(augmentation: new stdClass());
        } catch (TypeError) {
            // Ignored
        }
        self::assertEmpty($this->augmentations);
    }
}
