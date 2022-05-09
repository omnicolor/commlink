<?php

declare(strict_types=1);

namespace Tests\Feature\Models\Shadowrun5E;

use App\Models\Shadowrun5E\KnowledgeSkill;

/**
 * Unit tests for knowledge skills class.
 * @group models
 * @group shadowrun
 * @group shadowrun5e
 * @small
 */
final class KnowledgeSkillTest extends \Tests\TestCase
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
        self::expectException(\RuntimeException::class);
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
    public function provideCategoryAttributeMappings(): array
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
}
