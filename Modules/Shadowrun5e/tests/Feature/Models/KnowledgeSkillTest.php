<?php

declare(strict_types=1);

namespace Modules\Shadowrun5e\Tests\Feature\Models;

use Iterator;
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
    private KnowledgeSkill $skill;

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
        self::assertSame(
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
        self::assertSame(
            'logic',
            $this->skill->attribute,
            'Failed to return attribute for knowledge skill'
        );
    }

    /**
     * Provider for testing category to attribute mappings.
     * @return Iterator<(int | string), array<string>>
     */
    public static function provideCategoryAttributeMappings(): Iterator
    {
        yield ['academic', 'logic'];
        yield ['interests', 'intuition'];
        yield ['language', 'intuition'];
        yield ['professional', 'logic'];
        yield ['street', 'intuition'];
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
        self::assertSame(
            $attribute,
            $skill->attribute,
            'Failed attribute mapping for ' . $category
        );
    }

    /**
     * Return a mapping of full category names to short names.
     * @return Iterator<int, array<int, string>>
     */
    public static function provideShortCategoryMappings(): Iterator
    {
        yield ['academic', 'acad'];
        yield ['interests', 'int'];
        yield ['language', 'lang'];
        yield ['professional', 'prof'];
        yield ['street', 'str'];
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
     * @return Iterator<int, array<int, string>>
     */
    public static function namesProvider(): Iterator
    {
        yield ['Or\'zet', 'Orzet'];
        yield ['Iñtërnâtiônàlizætiøn', 'Itrntinliztin'];
        yield ['20th Century Comic Books', '20th-Century-Comic-Books'];
        yield ['Bars (city)', 'Bars-city'];
        yield ['<script>alert(\'XSS\');</script>', 'scriptalertXSSscript'];
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
