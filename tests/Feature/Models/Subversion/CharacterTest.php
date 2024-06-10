<?php

declare(strict_types=1);

namespace Tests\Feature\Models\Subversion;

use App\Models\Subversion\Background;
use App\Models\Subversion\Caste;
use App\Models\Subversion\Character;
use App\Models\Subversion\Gear;
use App\Models\Subversion\GearArray;
use App\Models\Subversion\Ideology;
use App\Models\Subversion\Impulse;
use App\Models\Subversion\Language;
use App\Models\Subversion\LanguageArray;
use App\Models\Subversion\Lineage;
use App\Models\Subversion\Origin;
use App\Models\Subversion\Skill;
use PHPUnit\Framework\Attributes\Group;
use PHPUnit\Framework\Attributes\Small;
use Tests\TestCase;

#[Group('subversion')]
#[Small]
final class CharacterTest extends TestCase
{
    public function testToStringNoName(): void
    {
        $character = new Character();
        self::assertSame('Unnamed character', (string)$character);
    }

    public function testToString(): void
    {
        $character = new Character(['name' => 'Derf']);
        self::assertSame('Derf', (string)$character);
    }

    public function testGritStarting(): void
    {
        $character = new Character(['will' => 5]);
        self::assertSame(11, $character->grit_starting);
        $character = new Character(['will' => 2]);
        self::assertSame(8, $character->grit_starting);
    }

    public function testAegisAttribute(): void
    {
        $character = new Character(['awareness' => 5]);
        self::assertSame(18, $character->aegis);
        $character->awareness = 1;
        self::assertSame(10, $character->aegis);
    }

    public function testAnimityMaximumAttribute(): void
    {
        $character = new Character(['charisma' => 1]);
        self::assertSame(16, $character->animity_maximum);
        $character->charisma = 5;
        self::assertSame(32, $character->animity_maximum);
    }

    public function testBackgroundNotSet(): void
    {
        $character = new Character();
        self::assertNull($character->background);
    }

    public function testBackground(): void
    {
        $character = new Character(['background' => 'agriculturist']);
        self::assertSame('Agriculturist', (string)$character->background);
    }

    public function testSetBackground(): void
    {
        $character = new Character();
        $background = new Background('agriculturist');
        $character->background = $background;
        self::assertSame('Agriculturist', (string)$character->background);
    }

    public function testCasteNotSet(): void
    {
        $character = new Character();
        self::assertNull($character->caste);
    }

    public function testCaste(): void
    {
        $character = new Character(['caste' => 'lower-middle']);
        self::assertSame('Lower-middle caste', (string)$character->caste);
    }

    public function testSetCaste(): void
    {
        $character = new Character();
        $caste = new Caste('lower-middle');
        $character->caste = $caste;
        self::assertSame('Lower-middle caste', (string)$character->caste);
    }

    public function testEmptyGear(): void
    {
        $character = new Character();
        self::assertCount(0, $character->gear);
    }

    public function testGetGear(): void
    {
        $character = new Character([
            'gear' => [
                ['id' => 'paylo'],
            ],
        ]);
        self::assertCount(1, $character->gear);
    }

    public function testSetGear(): void
    {
        $gear = new GearArray();
        $gear[] = new Gear('auto-intruder');
        $character = new Character();
        $character->gear = $gear;
        self::assertCount(1, $character->gear);
    }

    public function testGuardAttribute(): void
    {
        $character = new Character(['agility' => 5]);
        self::assertSame(18, $character->guard_defense);
        $character->agility = 3;
        self::assertSame(14, $character->guard_defense);
    }

    public function testHealthMaximum(): void
    {
        $character = new Character(['brawn' => 5]);
        self::assertSame(32, $character->health_maximum);
        $character->brawn = 1;
        self::assertSame(16, $character->health_maximum);
    }

    public function testIdeologyNotSet(): void
    {
        $character = new Character();
        self::assertNull($character->ideology);
    }

    public function testIdeology(): void
    {
        $character = new Character(['ideology' => 'neo-anarchist']);
        self::assertSame('Neo-anarchist', (string)$character->ideology);
        self::assertSame(88, $character->ideology?->page);
    }

    public function testSetIdeologyObject(): void
    {
        $character = new Character();
        $character->ideology = new Ideology('neo-anarchist');
        self::assertSame('Neo-anarchist', (string)$character->ideology);
    }

    public function testSetIdeologyString(): void
    {
        $character = new Character();
        $character->ideology = 'neo-anarchist';
        self::assertSame('Neo-anarchist', (string)$character->ideology);
    }

    public function testImpulseNotSet(): void
    {
        $character = new Character();
        self::assertNull($character->impulse);
    }

    public function testImpulse(): void
    {
        $character = new Character(['impulse' => 'indulgence']);
        self::assertSame('Indulgence', (string)$character->impulse);
    }

    public function testSetImpulseObject(): void
    {
        $character = new Character();
        $impulse = new Impulse('indulgence');
        $character->impulse = $impulse;
        self::assertSame('Indulgence', (string)$character->impulse);
    }

    public function testSetImpulseString(): void
    {
        $character = new Character();
        $character->impulse = 'indulgence';
        self::assertSame('Indulgence', (string)$character->impulse);
    }

    public function testInitiative(): void
    {
        $character = new Character([
            'agility' => 3,
            'awareness' => 2,
            'wit' => 1,
        ]);
        self::assertSame(14, $character->initiative);
        $character = new Character([
            'agility' => 5,
            'awareness' => 5,
            'wit' => 5,
        ]);
        self::assertSame(30, $character->initiative);
    }

    public function testEmptyLanguages(): void
    {
        $character = new Character();
        self::assertCount(0, $character->languages);
    }

    public function testGetLanguages(): void
    {
        $character = new Character([
            'languages' => ['commonur', 'fae'],
        ]);
        self::assertCount(2, $character->languages);
    }

    public function testSetLanguagesLanguageArray(): void
    {
        $languages = new LanguageArray();
        $languages[] = new Language('commonur');
        $languages[] = new Language('fae');
        $character = new Character();
        $character->languages = $languages;
        self::assertCount(2, $character->languages);
    }

    public function testLineageNotSet(): void
    {
        $character = new Character();
        self::assertNull($character->lineage);
    }

    public function testLineage(): void
    {
        $character = new Character([
            'lineage' => 'dwarven',
            'lineage_option' => 'small',
        ]);

        self::assertSame('Dwarven', $character->lineage?->name);
        // @phpstan-ignore-next-line
        self::assertSame('Small', $character->lineage?->option?->name);
    }

    public function testSetLineageObject(): void
    {
        $character = new Character();
        $character->lineage = new Lineage('dwarven', 'toxin-resistant');

        self::assertSame('Dwarven', $character->lineage?->name);
        // @phpstan-ignore-next-line
        self::assertSame('Toxin resistant', $character->lineage->option->name);
    }

    public function testSetLineageString(): void
    {
        $character = new Character();
        $character->lineage = 'dwarven';
        self::assertSame('Dwarven', $character->lineage?->name);
    }

    public function testOriginNotSet(): void
    {
        $character = new Character();
        self::assertNull($character->origin);
    }

    public function testOrigin(): void
    {
        $character = new Character(['origin' => 'altaipheran']);
        self::assertSame('Altaipheran', $character->origin?->name);
    }

    public function testSetOrigin(): void
    {
        $character = new Character();
        $character->origin = new Origin('altaipheran');
        self::assertSame('Altaipheran', $character->origin?->name);
    }

    public function testSkillsEmpty(): void
    {
        $character = new Character();
        $skills = $character->skills;
        self::assertCount(12, $skills);
        foreach ($skills as $skill) {
            self::assertNull($skill->rank);
        }
    }

    public function testSkillsSetArray(): void
    {
        $character = new Character();
        $character->skills = [
            ['id' => 'arts', 'rank' => 0],
        ];

        $skill = $character->skills['arts'];
        self::assertSame(0, $skill->rank);
    }

    public function testSkillsFromConstructor(): void
    {
        $character = new Character([
            'skills' => [
                ['id' => 'arts', 'rank' => 1],
            ],
        ]);

        self::assertSame(1, $character->skills['arts']->rank);
        self::assertNull($character->skills['deception']->rank);
    }

    public function testSkillsSetObject(): void
    {
        $character = new Character();
        $character->skills = [
            new Skill('deception', 2),
        ];
        self::assertNull($character->skills['arts']->rank);
        self::assertSame(2, $character->skills['deception']->rank);
    }

    public function testVigilianceAttribute(): void
    {
        $character = new Character(['wit' => 5]);
        self::assertSame(18, $character->vigilance);
        $character->wit = 2;
        self::assertSame(12, $character->vigilance);
    }
}
