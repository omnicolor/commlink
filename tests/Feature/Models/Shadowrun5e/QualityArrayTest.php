<?php

declare(strict_types=1);

namespace Tests\Feature\Models\Shadowrun5e;

use App\Models\Shadowrun5e\Quality;
use App\Models\Shadowrun5e\QualityArray;
use PHPUnit\Framework\Attributes\Small;
use Tests\TestCase;
use TypeError;
use stdClass;

/**
 * Tests for the QualityArray class.
 * @group shadowrun
 * @group shadowrun5e
 */
#[Small]
final class QualityArrayTest extends TestCase
{
    /**
     * Subject under test.
     * @var QualityArray<Quality>
     */
    protected QualityArray $qualities;

    /**
     * Set up a clean subject.
     */
    public function setUp(): void
    {
        parent::setUp();
        $this->qualities = new QualityArray();
    }

    /**
     * Test an empty QualityArray.
     */
    public function testEmpty(): void
    {
        self::assertEmpty($this->qualities);
    }

    /**
     * Test adding a quality to the array.
     */
    public function testAdd(): void
    {
        $this->qualities[] = new Quality('alpha-junkie');
        self::assertNotEmpty($this->qualities);
    }

    /**
     * Test that adding a non-quality to the array throws an exception.
     */
    public function testAddWrongTypeException(): void
    {
        self::expectException(TypeError::class);
        self::expectExceptionMessage(
            'QualityArray only accepts Quality objects'
        );
        // @phpstan-ignore-next-line
        $this->qualities[] = new stdClass();
    }

    /**
     * Test that adding a non-quality to the array doesn't add it.
     */
    public function testAddWrongTypeDoesntAdd(): void
    {
        try {
            // @phpstan-ignore-next-line
            $this->qualities->offsetSet(quality: new stdClass());
        } catch (TypeError) {
            // Ignored
        }
        self::assertEmpty($this->qualities);
    }
}
