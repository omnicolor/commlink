<?php

declare(strict_types=1);

namespace Modules\Shadowrun5e\Tests\Feature\Models;

use Modules\Shadowrun5e\Models\MartialArtsTechnique;
use Modules\Shadowrun5e\Models\MartialArtsTechniqueArray;
use Override;
use PHPUnit\Framework\Attributes\Group;
use PHPUnit\Framework\Attributes\Small;
use Tests\TestCase;
use TypeError;
use stdClass;

#[Group('shadowrun')]
#[Group('shadowrun5e')]
#[Small]
final class MartialArtsTechniqueArrayTest extends TestCase
{
    /**
     * Subject under test.
     * @var MartialArtsTechniqueArray<MartialArtsTechnique>
     */
    protected MartialArtsTechniqueArray $techniques;

    /**
     * Set up a clean subject.
     */
    #[Override]
    public function setUp(): void
    {
        parent::setUp();
        $this->techniques = new MartialArtsTechniqueArray();
    }

    /**
     * Test an empty MartialArtsTechniqueArray.
     */
    public function testEmpty(): void
    {
        self::assertEmpty($this->techniques);
    }

    /**
     * Test adding a technique to the array.
     */
    public function testAdd(): void
    {
        $this->techniques[] = new MartialArtsTechnique('constrictors-crush');
        self::assertNotEmpty($this->techniques);
    }

    /**
     * Test that adding a non-technique to the array throws an exception.
     */
    public function testAddWrongTypeException(): void
    {
        self::expectException(TypeError::class);
        // @phpstan-ignore offsetAssign.valueType
        $this->techniques[] = new stdClass();
    }

    /**
     * Test that adding a non-technique to the array doesn't add it.
     */
    public function testAddWrongTypeDoesntAdd(): void
    {
        try {
            // @phpstan-ignore argument.type
            $this->techniques->offsetSet(technique: new stdClass());
        } catch (TypeError) {
            // Ignored
        }
        self::assertEmpty($this->techniques);
    }
}
