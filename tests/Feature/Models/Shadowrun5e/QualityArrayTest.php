<?php

declare(strict_types=1);

namespace Tests\Feature\Models\Shadowrun5e;

use App\Models\Shadowrun5e\Quality;
use App\Models\Shadowrun5e\QualityArray;
use Tests\TestCase;
use TypeError;
use stdClass;

/**
 * Tests for the QualityArray class.
 * @group shadowrun
 * @group shadowrun5e
 * @small
 */
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
     * @test
     */
    public function testEmpty(): void
    {
        self::assertEmpty($this->qualities);
    }

    /**
     * Test adding a quality to the array.
     * @test
     */
    public function testAdd(): void
    {
        $this->qualities[] = new Quality('alpha-junkie');
        self::assertNotEmpty($this->qualities);
    }

    /**
     * Test that adding a non-quality to the array throws an exception.
     * @test
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
     * @test
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
