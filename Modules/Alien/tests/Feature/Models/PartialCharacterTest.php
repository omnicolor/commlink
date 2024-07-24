<?php

declare(strict_types=1);

namespace Modules\Alien\Tests\Feature\Models;

use Modules\Alien\Models\PartialCharacter;
use Modules\Alien\Models\Skill;
use PHPUnit\Framework\Attributes\Group;
use PHPUnit\Framework\Attributes\Small;
use Tests\TestCase;

#[Group('alien')]
#[Small]
final class PartialCharacterTest extends TestCase
{
    public function testValidateWithoutCareer(): void
    {
        $character = new PartialCharacter();
        $errors = $character->validate();
        self::assertArrayHasKey('career', $errors);
        self::assertSame('You haven\'t chosen a career', $errors['career']);
    }

    public function testValidateWithoutName(): void
    {
        $character = new PartialCharacter();
        $errors = $character->validate();
        self::assertArrayHasKey('name', $errors);
        self::assertSame('Your character has no name', $errors['name']);
    }

    public function testValidateWithoutAttributes(): void
    {
        $character = new PartialCharacter();
        $errors = $character->validate();
        self::assertArrayHasKey('attributes', $errors);
        self::assertSame(
            'You have not set your attributes',
            $errors['attributes'],
        );
    }

    public function testValidateAttributesTooLow(): void
    {
        $character = new PartialCharacter([
            'agility' => 1,
            'empathy' => 1,
            'strength' => 1,
            'wits' => 1,
        ]);
        $errors = $character->validate();
        self::assertArrayHasKey('agility', $errors);
        self::assertSame(
            'Your agility must be 2 or higher',
            $errors['agility'],
        );
        self::assertArrayHasKey('empathy', $errors);
        self::assertSame(
            'Your empathy must be 2 or higher',
            $errors['empathy'],
        );
        self::assertArrayHasKey('strength', $errors);
        self::assertSame(
            'Your strength must be 2 or higher',
            $errors['strength'],
        );
        self::assertArrayHasKey('wits', $errors);
        self::assertSame(
            'Your wits must be 2 or higher',
            $errors['wits'],
        );
    }

    public function testValidateAttributesTooHighNoCareer(): void
    {
        $character = new PartialCharacter([
            'agility' => 5,
            'empathy' => 5,
            'strength' => 5,
            'wits' => 5,
        ]);
        $errors = $character->validate();
        self::assertArrayHasKey('agility', $errors);
        self::assertArrayHasKey('empathy', $errors);
        self::assertArrayHasKey('strength', $errors);
        self::assertArrayHasKey('wits', $errors);
        self::assertSame(
            'Your agility must be 4 or lower',
            $errors['agility'],
        );
        self::assertSame(
            'Your empathy must be 4 or lower',
            $errors['empathy'],
        );
        self::assertSame(
            'Your strength must be 4 or lower',
            $errors['strength'],
        );
        self::assertSame(
            'Your wits must be 4 or lower',
            $errors['wits'],
        );
    }

    public function testValidateAttributesTooHighWithCareer(): void
    {
        $character = new PartialCharacter([
            'agility' => 5,
            'career' => 'colonial-marine',
            'empathy' => 5,
            'strength' => 5,
            'wits' => 5,
        ]);
        $errors = $character->validate();
        self::assertArrayHasKey('agility', $errors);
        self::assertArrayHasKey('empathy', $errors);
        self::assertArrayNotHasKey('strength', $errors);
        self::assertArrayHasKey('wits', $errors);

        $character->career = 'colonial-marshal';
        $errors = $character->validate();
        self::assertArrayHasKey('agility', $errors);
        self::assertArrayHasKey('empathy', $errors);
        self::assertArrayHasKey('strength', $errors);
        self::assertArrayNotHasKey('wits', $errors);

        $character->wits = 6;
        $errors = $character->validate();
        self::assertSame('Your wits must be 5 or lower', $errors['wits']);
    }

    public function testValidateAttributePointsTooFew(): void
    {
        $character = new PartialCharacter([
            'agility' => 3,
            'empathy' => 3,
            'strength' => 3,
            'wits' => 3,
        ]);
        $errors = $character->validate();
        self::assertArrayHasKey('attributes', $errors);
        self::assertSame(
            'You haven\'t spent all of your attribute points',
            $errors['attributes'],
        );
    }

    public function testValidateAttributePointsTooMany(): void
    {
        $character = new PartialCharacter([
            'agility' => 4,
            'empathy' => 4,
            'strength' => 4,
            'wits' => 4,
        ]);
        $errors = $character->validate();
        self::assertArrayHasKey('attributes', $errors);
        self::assertSame(
            'You haven spent too many attribute points',
            $errors['attributes'],
        );
    }

    public function testValidateSkillPointsUnspent(): void
    {
        $character = new PartialCharacter();
        $errors = $character->validate();
        self::assertArrayHasKey('skills', $errors);
        self::assertSame(
            'You haven\'t spent all of your skill points',
            $errors['skills'],
        );
    }

    public function testValidateSkillPointsTooMany(): void
    {
        $character = new PartialCharacter();
        $skills = [];
        foreach (Skill::all() as $skill) {
            $skill->rank = 1;
            $skills[] = $skill;
        }
        $character->skills = $skills;
        $errors = $character->validate();
        self::assertArrayHasKey('skills', $errors);
        self::assertSame(
            'You have spent too many skill points',
            $errors['skills'],
        );
    }

    public function testValidateSkillRankTooHigh(): void
    {
        // A colonial marine's key skills are close combat, ranged combat, and
        // stamina.
        $character = new PartialCharacter([
            'career' => 'colonial-marine',
            'skills' => [
                'close-combat' => 3, // Not too high for a career skill.
                'command' => 3, // Too high for a non-career skill.
                'comtech' => 0,
                'heavy-machinery' => 0,
                'manipulation' => 0,
                'medical-aid' => 0,
                'mobility' => 0,
                'observation' => 0,
                'piloting' => 0,
                'ranged-combat' => 0,
                'stamina' => 4, // Too high even for a career skill.
                'survival' => 0,
            ],
        ]);
        $errors = $character->validate();

        self::assertArrayHasKey('skill-command', $errors);
        self::assertSame(
            'Your rank in skill "Command" is too high',
            $errors['skill-command'],
        );
        self::assertArrayHasKey('skill-stamina', $errors);
        self::assertArrayNotHasKey('skill-close-combat', $errors);
    }

    public function testValidateNoTalent(): void
    {
        $character = new PartialCharacter();
        $errors = $character->validate();
        self::assertArrayHasKey('talent', $errors);
        self::assertSame('You haven\'t chosen a talent', $errors['talent']);
    }

    public function testValidateNoGear(): void
    {
        $character = new PartialCharacter();
        $errors = $character->validate();
        self::assertArrayHasKey('gear', $errors);
        self::assertSame('You haven\'t chosen your gear', $errors['gear']);
    }

    public function testValidateNoErrors(): void
    {
        $character = new PartialCharacter([
            'agility' => 3,
            'armor' => 'm3-personnel-armor',
            'career' => 'colonial-marine',
            'empathy' => 3,
            'gear' => [
                ['id' =>  'm314-motion-tracker'],
            ],
            'name' => 'Bob King',
            'skills' => [
                'close-combat' => 3,
                'command' => 1,
                'comtech' => 0,
                'heavy-machinery' => 0,
                'manipulation' => 0,
                'medical-aid' => 0,
                'mobility' => 0,
                'observation' => 0,
                'piloting' => 0,
                'ranged-combat' => 3,
                'stamina' => 3,
                'survival' => 0,
            ],
            'strength' => 5,
            'talents' => [
                'banter',
            ],
            'wits' => 3,
        ]);
        self::assertCount(0, $character->validate());
    }

    public function testToCharacter(): void
    {
        $character = new PartialCharacter([
            'agenda' => 'Nuke it all from orbit',
            'agility' => 3,
            'appearance' => 'Short black hair',
            'armor' => 'm3-personnel-armor',
            'buddy' => 'Phil',
            'career' => 'colonial-marine',
            'empathy' => 3,
            'gear' => [
                ['id' =>  'm314-motion-tracker'],
            ],
            'name' => 'Bob King',
            'rival' => 'Also Phil',
            'skills' => [
                'close-combat' => 3,
                'command' => 1,
                'comtech' => 0,
                'heavy-machinery' => 0,
                'manipulation' => 0,
                'medical-aid' => 0,
                'mobility' => 0,
                'observation' => 0,
                'piloting' => 0,
                'ranged-combat' => 3,
                'stamina' => 3,
                'survival' => 0,
            ],
            'strength' => 5,
            'talents' => [
                'banter',
            ],
            'weapons' => [
                'm4a3-service-pistol',
            ],
            'wits' => 3,
        ]);
        $character->toCharacter();
        self::assertSame('Bob King', $character->name);
    }
}
