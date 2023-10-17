<?php

declare(strict_types=1);

namespace Tests\Feature\Models\Shadowrun5e;

use App\Models\Shadowrun5e\LifestyleOption;
use App\Models\Shadowrun5e\LifestyleOptionArray;
use Tests\TestCase;
use TypeError;
use stdClass;

/**
 * Tests for LifestyleOptionArray.
 * @group models
 * @group shadowrun
 * @group shadowrun5e
 * @small
 */
final class LifestyleOptionArrayTest extends TestCase
{
    /**
     * Subject under test.
     * @var LifestyleOptionArray<LifestyleOption>
     */
    protected LifestyleOptionArray $options;

    /**
     * Set up a clean subject.
     */
    public function setUp(): void
    {
        parent::setUp();
        $this->options = new LifestyleOptionArray();
    }

    /**
     * Test an empty array.
     * @test
     */
    public function testEmpty(): void
    {
        self::assertEmpty($this->options);
    }

    /**
     * Test adding an option to the array.
     * @test
     */
    public function testAdd(): void
    {
        $this->options[] = new LifestyleOption('swimming-pool');
        self::assertCount(1, $this->options);
    }

    /**
     * Test adding the same option twice.
     * @test
     */
    public function testAddTwice(): void
    {
        $this->options[] = new LifestyleOption('swimming-pool');
        $this->options[] = new LifestyleOption('swimming-pool');
        self::assertCount(2, $this->options);
    }

    /**
     * Test adding a non-LifestyleOption to the array.
     * @test
     */
    public function testAddInvalidObject(): void
    {
        self::expectException(TypeError::class);
        self::expectExceptionMessage(
            'LifestyleOptionArray only accepts LifestyleOption objects'
        );
        // @phpstan-ignore-next-line
        $this->options[] = new stdClass();
    }
}
