<?php

declare(strict_types=1);

namespace Modules\Shadowrun5e\Tests\Feature\Models;

use Modules\Shadowrun5e\Models\KnowledgeSkill;
use Override;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\Attributes\Group;
use PHPUnit\Framework\Attributes\Small;
use RuntimeException;
use Tests\TestCase;

#[Group('shadowrun')]
#[Group('shadowrun5e')]
#[Small]
final class KnowledgeSkillTest extends TestCase
{
    /**
     * @var KnowledgeSkill Subject under test
     */
    protected KnowledgeSkill $skill;

    /**
     * Set up a clean subject under test.
     */
    #[Override]
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
     */
    #[DataProvider('provideCategoryAttributeMappings')]
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
     */
    #[DataProvider('provideShortCategoryMappings')]
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
     */
    #[DataProvider('namesProvider')]
    public function testNamesToId(string $name, string $id): void
    {
        $skill = new KnowledgeSkill($name, 'academic', 2);
        self::assertSame($id, $skill->id);
    }

    /**
     * Test getting an unknown property.
     */
    public function testGetUnknownProperty(): void
    {
        $skill = new KnowledgeSkill('Alcohol', 'interests', 5);
        // @phpstan-ignore property.notFound
        self::assertNull($skill->unknown);
    }
}
