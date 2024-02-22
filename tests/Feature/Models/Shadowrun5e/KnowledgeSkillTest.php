<?php

declare(strict_types=1);

namespace Tests\Feature\Models\Shadowrun5e;

use App\Models\Shadowrun5e\KnowledgeSkill;
use RuntimeException;
use Tests\TestCase;

/**
 * Unit tests for knowledge skills class.
 * @group shadowrun
 * @group shadowrun5e
 * @small
 */
final class KnowledgeSkillTest extends TestCase
{
    /**
     * @var KnowledgeSkill Subject under test
     */
    protected KnowledgeSkill $skill;

    /**
     * Set up a clean subject under test.
     */
    protected function setUp(): void
    {
        parent::setUp();
        $this->skill = new KnowledgeSkill(
            'Police procedures',
            'professional',
            2
        );
    }

    /**
     * Test creating a knowledge skill with an invalid category.
     * @test
     */
    public function testInvalidCategory(): void
    {
        self::expectException(RuntimeException::class);
        self::expectExceptionMessage(
            'Knowledge skill category "invalid" is invalid'
        );
        new KnowledgeSkill('foo', 'invalid', 1);
    }

    /**
     * Test returning the object as a string.
     * @test
     */
    public function testToString(): void
    {
        self::assertEquals(
            'Police procedures',
            (string)$this->skill,
            'Failed to return knowledge skill name when cast to string'
        );
    }

    /**
     * Test returning the object's attribute.
     * @test
     */
    public function testAttribute(): void
    {
        self::assertEquals(
            'logic',
            $this->skill->attribute,
            'Failed to return attribute for knowledge skill'
        );
    }

    /**
     * Provider for testing category to attribute mappings.
     * @return array<string[]>
     */
    public static function provideCategoryAttributeMappings(): array
    {
        return [
            ['academic', 'logic'],
            ['interests', 'intuition'],
            ['language', 'intuition'],
            ['professional', 'logic'],
            ['street', 'intuition'],
        ];
    }

    /**
     * Test getting attributes linked to each category.
     * @dataProvider provideCategoryAttributeMappings
     * @param string $category Category to test
     * @param string $attribute Attribute it should link to
     * @test
     */
    public function testCategoryAttributeMappings(
        string $category,
        string $attribute
    ): void {
        $skill = new KnowledgeSkill('unused', $category, 1);
        self::assertEquals(
            $attribute,
            $skill->attribute,
            'Failed attribute mapping for ' . $category
        );
    }

    /**
     * Return a mapping of full category names to short names.
     * @return array<int, array<int, string>>
     */
    public static function provideShortCategoryMappings(): array
    {
        return [
            ['academic', 'acad'],
            ['interests', 'int'],
            ['language', 'lang'],
            ['professional', 'prof'],
            ['street', 'str'],
        ];
    }

    /**
     * Test getting the short category names.
     * @dataProvider provideShortCategoryMappings
     * @param string $category
     * @param string $short
     * @test
     */
    public function testShortCategory(string $category, string $short): void
    {
        $skill = new KnowledgeSkill('unused', $category, 1);
        self::assertSame($short, $skill->short_category);
    }

    /**
     * @return array<int, array<int, string>>
     */
    public static function namesProvider(): array
    {
        return [
            ['Or\'zet', 'Orzet'],
            ['Iñtërnâtiônàlizætiøn', 'Itrntinliztin'],
            ['20th Century Comic Books', '20th-Century-Comic-Books'],
            ['Bars (city)', 'Bars-city'],
            ['<script>alert(\'XSS\');</script>', 'scriptalertXSSscript'],
        ];
    }

    /**
     * Test getting a knowledge skill's ID.
     * @dataProvider namesProvider
     * @param string $name
     * @param string $id
     * @test
     */
    public function testNamesToId(string $name, string $id): void
    {
        $skill = new KnowledgeSkill($name, 'academic', 2);
        self::assertSame($id, $skill->id);
    }

    /**
     * Test getting an unknown property.
     * @test
     */
    public function testGetUnknownProperty(): void
    {
        $skill = new KnowledgeSkill('Alcohol', 'interests', 5);
        // @phpstan-ignore-next-line
        self::assertNull($skill->unknown);
    }
}
