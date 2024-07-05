<?php

declare(strict_types=1);

namespace Modules\Shadowrun5e\Tests\Feature\Models;

use Modules\Shadowrun5e\Models\Quality;
use Modules\Shadowrun5e\Models\QualityArray;
use PHPUnit\Framework\Attributes\Group;
use PHPUnit\Framework\Attributes\Small;
use Tests\TestCase;
use TypeError;
use stdClass;

#[Group('shadowrun')]
#[Group('shadowrun5e')]
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
