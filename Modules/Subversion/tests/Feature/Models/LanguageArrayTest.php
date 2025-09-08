<?php

declare(strict_types=1);

namespace Modules\Subversion\Tests\Feature\Models;

use Modules\Subversion\Models\Language;
use Modules\Subversion\Models\LanguageArray;
use Override;
use PHPUnit\Framework\Attributes\Group;
use PHPUnit\Framework\Attributes\Small;
use Tests\TestCase;
use TypeError;
use stdClass;

#[Group('subversion')]
#[Small]
final class LanguageArrayTest extends TestCase
{
    /** @var LanguageArray<Language> */
    private LanguageArray $languages;

    #[Override]
    public function setUp(): void
    {
        parent::setUp();
        $this->languages = new LanguageArray();
    }

    /**
     * Test an empty LanguageArray.
     */
    public function testEmpty(): void
    {
        self::assertEmpty($this->languages);
    }

    /**
     * Test adding a language to the array.
     */
    public function testAdd(): void
    {
        $this->languages[] = new Language('commonur');
        self::assertNotEmpty($this->languages);
    }

    /**
     * Test that adding a non-language to the array throws an exception.
     */
    public function testAddWrongTypeException(): void
    {
        self::expectException(TypeError::class);
        // @phpstan-ignore offsetAssign.valueType
        $this->languages[] = new stdClass();
    }

    /**
     * Test that adding a non-language to the array doesn't add it.
     */
    public function testAddWrongTypeDoesntAdd(): void
    {
        try {
            // @phpstan-ignore argument.type
            $this->languages->offsetSet(index: 0, language: new stdClass());
        } catch (TypeError) {
            // Ignored
        }
        self::assertEmpty($this->languages);
    }
}
