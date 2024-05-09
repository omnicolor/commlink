<?php

declare(strict_types=1);

namespace Tests\Feature\Models\Subversion;

use App\Models\Subversion\Language;
use App\Models\Subversion\LanguageArray;
use Tests\TestCase;
use TypeError;
use stdClass;

/**
 * Tests for the LanguageArray class.
 * @group shadowrun
 * @group shadowrun5e
 * @small
 */
final class LanguageArrayTest extends TestCase
{
    /**
     * Subject under test.
     * @var LanguageArray<Language>
     */
    protected LanguageArray $languages;

    /**
     * Set up a clean subject.
     */
    public function setUp(): void
    {
        parent::setUp();
        $this->languages = new LanguageArray();
    }

    /**
     * Test an empty LanguageArray.
     * @test
     */
    public function testEmpty(): void
    {
        self::assertEmpty($this->languages);
    }

    /**
     * Test adding a language to the array.
     * @test
     */
    public function testAdd(): void
    {
        $this->languages[] = new Language('commonur');
        self::assertNotEmpty($this->languages);
    }

    /**
     * Test that adding a non-language to the array throws an exception.
     * @test
     */
    public function testAddWrongTypeException(): void
    {
        self::expectException(TypeError::class);
        // @phpstan-ignore-next-line
        $this->languages[] = new stdClass();
    }

    /**
     * Test that adding a non-language to the array doesn't add it.
     * @test
     */
    public function testAddWrongTypeDoesntAdd(): void
    {
        try {
            // @phpstan-ignore-next-line
            $this->languages->offsetSet(language: new stdClass());
        } catch (TypeError) {
            // Ignored
        }
        self::assertEmpty($this->languages);
    }
}
