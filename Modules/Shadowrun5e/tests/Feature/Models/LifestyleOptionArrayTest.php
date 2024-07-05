<?php

declare(strict_types=1);

namespace Modules\Shadowrun5e\Tests\Feature\Models;

use Modules\Shadowrun5e\Models\LifestyleOption;
use Modules\Shadowrun5e\Models\LifestyleOptionArray;
use PHPUnit\Framework\Attributes\Group;
use PHPUnit\Framework\Attributes\Small;
use Tests\TestCase;
use TypeError;
use stdClass;

#[Group('shadowrun')]
#[Group('shadowrun5e')]
#[Small]
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
     */
    public function testEmpty(): void
    {
        self::assertEmpty($this->options);
    }

    /**
     * Test adding an option to the array.
     */
    public function testAdd(): void
    {
        $this->options[] = new LifestyleOption('swimming-pool');
        self::assertCount(1, $this->options);
    }

    /**
     * Test adding the same option twice.
     */
    public function testAddTwice(): void
    {
        $this->options[] = new LifestyleOption('swimming-pool');
        $this->options[] = new LifestyleOption('swimming-pool');
        self::assertCount(2, $this->options);
    }

    /**
     * Test adding a non-LifestyleOption to the array.
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
