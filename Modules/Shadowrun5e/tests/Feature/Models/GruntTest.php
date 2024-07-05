<?php

declare(strict_types=1);

namespace Modules\Shadowrun5e\Tests\Feature\Models;

use Modules\Shadowrun5e\Models\Grunt;
use PHPUnit\Framework\Attributes\Group;
use PHPUnit\Framework\Attributes\Small;
use RuntimeException;
use Tests\TestCase;

#[Group('shadowrun')]
#[Group('shadowrun5e')]
#[Small]
final class GruntTest extends TestCase
{
    /**
     * Test loading an invalid grunt.
     */
    public function testLoadNotFound(): void
    {
        self::expectException(RuntimeException::class);
        self::expectExceptionMessage('Grunt ID "not-found" was not found');
        new Grunt('not-found');
    }

    /**
     * Test loading a grunt.
     */
    public function testLoadPr0Grunt(): void
    {
        $grunt = new Grunt('pr-0');

        self::assertSame(3, $grunt->body);
        self::assertSame(3, $grunt->agility);
        self::assertSame(3, $grunt->reaction);
        self::assertSame(3, $grunt->strength);
        self::assertSame(3, $grunt->willpower);
        self::assertSame(2, $grunt->logic);
        self::assertSame(3, $grunt->intuition);
        self::assertSame(2, $grunt->charisma);
        self::assertSame(6.0, $grunt->essence);
        self::assertSame(6, $grunt->initiative_base);
        self::assertSame(1, $grunt->initiative_dice);
        self::assertSame(10, $grunt->condition_monitor);
        self::assertSame(0, $grunt->professional_rating);

        self::assertStringContainsString('angry mob', $grunt->description);
        self::assertSame('Thugs & mouth breathers', $grunt->name);
        self::assertSame('core', $grunt->ruleset);
        self::assertSame(381, $grunt->page);

        self::assertNull($grunt->adept_powers);
        self::assertCount(0, $grunt->armor);
        self::assertCount(0, $grunt->augmentations);
        self::assertNull($grunt->complex_forms);
        // The data for meta link isn't included in the default files.
        self::assertCount(0, $grunt->gear);
        self::assertCount(0, $grunt->knowledge);
        self::assertCount(0, $grunt->qualities);
        // The data for clubs isn't include in the default files.
        self::assertCount(2, $grunt->skills);
        self::assertNull($grunt->spells);
        // The data for club isn't included in the default files.
        self::assertCount(1, $grunt->weapons);
        self::assertSame(0, $grunt->getArmorValue());
    }

    /**
     * Test loading a technomancer grunt.
     */
    public function testLoadTechnomancer(): void
    {
        $grunt = new Grunt('pr-4-lieutenant');

        self::assertSame(3, $grunt->body);
        self::assertSame(3, $grunt->agility);
        self::assertSame(4, $grunt->reaction);
        self::assertSame(3, $grunt->strength);
        self::assertSame(5, $grunt->willpower);
        self::assertSame(5, $grunt->logic);
        self::assertSame(5, $grunt->intuition);
        self::assertSame(4, $grunt->charisma);
        self::assertSame(6.0, $grunt->essence);
        self::assertSame(5, $grunt->resonance);
        self::assertNull($grunt->magic);
        self::assertNull($grunt->initiate_grade);
        self::assertSame(9, $grunt->initiative_base);
        self::assertSame(1, $grunt->initiative_dice);
        self::assertSame('core', $grunt->ruleset);
        self::assertSame(383, $grunt->page);

        self::assertSame('Organized crime gang - Lieutenant', (string) $grunt);
        self::assertStringContainsString('technomancer', $grunt->description);

        self::assertNull($grunt->adept_powers);
        self::assertCount(1, $grunt->armor);
        self::assertCount(0, $grunt->augmentations);
        // @phpstan-ignore-next-line
        self::assertCount(1, $grunt->complex_forms);
        self::assertCount(0, $grunt->gear);
        self::assertCount(0, $grunt->knowledge);
        self::assertNull($grunt->magic);
        self::assertCount(1, $grunt->qualities);
        // Most of the skills aren't included in the default files.
        self::assertCount(3, $grunt->skills);
        self::assertNull($grunt->spells);
        // The Beretta 201T isn't included in the default weapon files.
        self::assertCount(0, $grunt->weapons);

        self::assertSame(9, $grunt->getArmorValue());
    }

    /**
     * Test loading an initated adept grunt.
     */
    public function testLoadAdept(): void
    {
        $grunt = new Grunt('pr-6-lieutenant');

        self::assertSame(6, $grunt->body);
        self::assertSame(9, $grunt->agility);
        self::assertSame(9, $grunt->reaction);
        self::assertSame(8, $grunt->strength);
        self::assertSame(5, $grunt->willpower);
        self::assertSame(5, $grunt->logic);
        self::assertSame(6, $grunt->intuition);
        self::assertSame(5, $grunt->charisma);
        self::assertSame(6.0, $grunt->essence);
        self::assertSame(6, $grunt->magic);
        self::assertNull($grunt->resonance);
        self::assertSame(11, $grunt->condition_monitor);
        self::assertSame(2, $grunt->initiate_grade);
        self::assertSame(15, $grunt->initiative_base);
        self::assertSame(4, $grunt->initiative_dice);
        self::assertSame(384, $grunt->page);
        self::assertSame('core', $grunt->ruleset);

        // @phpstan-ignore-next-line
        self::assertCount(1, $grunt->adept_powers);
        self::assertCount(2, $grunt->armor);
        self::assertCount(0, $grunt->augmentations);
        self::assertNull($grunt->complex_forms);
        self::assertCount(1, $grunt->gear);
        self::assertCount(0, $grunt->qualities);
        self::assertCount(1, $grunt->skills);
        self::assertNull($grunt->spells);
        self::assertCount(1, $grunt->weapons);

        self::assertSame(18, $grunt->getArmorValue());
    }

    /**
     * Test loading a grunt with armor that isn't found in the default data
     * files.
     */
    public function testLoadInvalidArmor(): void
    {
        $grunt = new Grunt('citizen-soldier');

        self::assertEmpty($grunt->armor);
    }

    /**
     * Test loading a grunt with some knowledge skills.
     */
    public function testLoadGruntThatKnowsThings(): void
    {
        $grunt = new Grunt('security-mage');

        self::assertCount(1, $grunt->knowledge);
    }

    /**
     * Test getting all grunts.
     */
    public function testLoadAll(): void
    {
        $grunts = Grunt::all();
        self::assertNotEmpty($grunts);
    }
}
