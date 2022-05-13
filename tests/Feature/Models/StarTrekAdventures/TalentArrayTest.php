<?php

declare(strict_types=1);

namespace Tests\Feature\Models\StarTrekAdventures;

use App\Models\StarTrekAdventures\Talent;
use App\Models\StarTrekAdventures\TalentArray;
use stdClass;
use Tests\TestCase;
use TypeError;

/**
 * Tests for TalentArray class.
 * @group models
 * @group star-trek-adventures
 * @small
 */
final class TalentArrayTest extends TestCase
{
    /**
     * Subject under test.
     * @var TalentArray<Talent>
     */
    protected TalentArray $talents;

    /**
     * Set up a clean subject.
     */
    public function setUp(): void
    {
        parent::setUp();
        $this->talents = new TalentArray();
    }

    /**
     * Test an empty TalentArray.
     * @test
     */
    public function testEmpty(): void
    {
        self::assertEmpty($this->talents);
    }

    /**
     * Test adding a talent to the array.
     * @test
     */
    public function testAdd(): void
    {
        $this->talents[] = new Talent('bold-command');
        self::assertNotEmpty($this->talents);
    }

    /**
     * Test that adding a non-talent to the array throws an exception.
     * @test
     */
    public function testAddWrongTypeException(): void
    {
        self::expectException(TypeError::class);
        // @phpstan-ignore-next-line
        $this->talents[] = new StdClass();
    }

    /**
     * Test that adding a non-talent to the array doesn't add it.
     * @test
     */
    public function testAddWrongTypeDoesntAdd(): void
    {
        try {
            // @phpstan-ignore-next-line
            $this->talents->offsetSet(talent: new StdClass());
        } catch (TypeError $e) {
            // Ignored
        }
        self::assertEmpty($this->talents);
    }
}
