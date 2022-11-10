<?php

declare(strict_types=1);

namespace Tests\Feature\Services\Omae;

use App\Models\Shadowrun5e\PartialCharacter;
use App\Services\Omae\Shadowrun5eConverter;
use RuntimeException;
use Tests\TestCase;

/**
 * @small
 */
final class Shadowrun5eConverterTest extends TestCase
{
    /**
     * Test trying to load a file that doesn't exist.
     * @test
     */
    public function testLoadNotFoundFile(): void
    {
        self::expectException(RuntimeException::class);
        self::expectExceptionMessage('Unable to locate Omae file');
        new Shadowrun5eConverter('not found');
    }

    /**
     * Test trying to load a non-text file.
     * @test
     */
    public function testLoadBinaryFile(): void
    {
        self::expectException(RuntimeException::class);
        self::expectExceptionMessage('File does not appear to be an Omae file');
        $path = explode(
            \DIRECTORY_SEPARATOR,
            dirname(dirname(dirname(__DIR__)))
        );
        $path[] = 'Data';
        $path[] = 'HeroLab';
        $path[] = 'Shadowrun5e';
        $path[] = 'brian.por';
        new Shadowrun5eConverter(implode(\DIRECTORY_SEPARATOR, $path));
    }

    /**
     * Test loading a file that is a plain text file, but not an Omae file.
     * @test
     */
    public function testLoadNotOmae(): void
    {
        self::expectException(RuntimeException::class);
        self::expectExceptionMessage('File does not appear to be an Omae file');
        new Shadowrun5eConverter(__FILE__);
    }

    /**
     * Test loading a valid Omae sheet.
     * @test
     */
    public function testLoadOmae(): PartialCharacter
    {
        $path = explode(
            \DIRECTORY_SEPARATOR,
            dirname(dirname(dirname(__DIR__)))
        );
        $path[] = 'Data';
        $path[] = 'Omae';
        $path[] = 'Test.txt';
        $filename = implode(\DIRECTORY_SEPARATOR, $path);
        $character = (new Shadowrun5eConverter($filename))->convert();
        self::assertInstanceOf(PartialCharacter::class, $character);
        return $character;
    }

    /**
     * Test the character's metadata.
     * @depends testLoadOmae
     * @param PartialCharacter $character
     * @test
     */
    public function testMetadata(PartialCharacter $character): void
    {
        self::assertSame('Fastjack', $character->handle);
        self::assertSame('Jack Smith', $character->realName);
        self::assertSame('shadowrun5e', $character->system);
    }

    /**
     * Test the character's priorities.
     * @depends testLoadOmae
     * @param PartialCharacter $character
     * @test
     */
    public function testPriorities(PartialCharacter $character): void
    {
        $priorities = $character->priorities;
        // @phpstan-ignore-next-line
        self::assertSame('D', $priorities['metatypePriority']);
        self::assertSame('A', $priorities['attributePriority']);
        self::assertSame('E', $priorities['magicPriority']);
        self::assertSame('B', $priorities['skillPriority']);
        self::assertSame('C', $priorities['resourcePriority']);
        self::assertSame('human', $priorities['metatype']);
    }

    /**
     * Test the character's remaining karma.
     * @depends testLoadOmae
     * @param PartialCharacter $character
     * @test
     */
    public function testKarma(PartialCharacter $character): void
    {
        self::assertSame(7, $character->karmaCurrent);
        self::assertSame(7, $character->karma);
    }

    /**
     * Test the character's attributes.
     * @depends testLoadOmae
     * @param PartialCharacter $character
     * @test
     */
    public function testAttributes(PartialCharacter $character): void
    {
        self::assertSame(3, $character->body);
        self::assertSame(6, $character->agility);
        self::assertSame(5, $character->reaction);
        self::assertSame(3, $character->strength);
        self::assertSame(3, $character->willpower);
        self::assertSame(3, $character->logic);
        self::assertSame(5, $character->intuition);
        self::assertSame(4, $character->charisma);
        self::assertSame(5, $character->edge);
        self::assertSame(5, $character->edgeCurrent);
        self::assertNull($character->resonance);
        self::assertNull($character->magic);
    }

    /**
     * Test the character's qualities.
     * @depends testLoadOmae
     * @param PartialCharacter $character
     * @test
     */
    public function testQualitites(PartialCharacter $character): void
    {
        /** @var array<string, int|string> */
        $qualities = $character->qualities;
        self::assertCount(3, $qualities);
        self::assertContains(['id' => 'natural-athlete'], $qualities);
        self::assertContains(['id' => 'quick-healer'], $qualities);
        self::assertContains(['id' => 'tough-and-targeted'], $qualities);
    }

    /**
     * Test the character's skills.
     * @depends testLoadOmae
     * @param PartialCharacter $character
     * @test
     */
    public function testSkills(PartialCharacter $character): void
    {
        /** @var array<string, string|int> */
        $skills = $character->skills;
        self::assertContains(
            [
                'id' => 'computer',
                'level' => 5,
                'specialization' => 'Searching',
            ],
            $skills
        );
        self::assertContains(['id' => 'automatics', 'level' => 6], $skills);
        self::assertContains(['id' => 'sneaking', 'level' => 6], $skills);
    }

    /**
     * Test the character's weapons.
     * @depends testLoadOmae
     * @param PartialCharacter $character
     * @test
     */
    public function testWeapons(PartialCharacter $character): void
    {
        /** @var array<string, string|array<int, string>> */
        $weapons = $character->weapons;
        self::assertContains(['id' => 'ares-predator-v'], $weapons);
        self::assertContains(['id' => 'combat-knife'], $weapons);
        self::assertContains(
            [
                'id' => 'ak-98',
                'modifications' => [
                    'smartlink-internal',
                    'bayonet',
                ],
            ],
            $weapons
        );
        self::assertContains(['id' => 'ruger-super-warhawk'], $weapons);
    }

    /**
     * Test the character's armor.
     * @depends testLoadOmae
     * @param PartialCharacter $character
     * @test
     */
    public function testArmor(PartialCharacter $character): void
    {
        /** @var array<string, string|array<int, string>> */
        $armor = $character->armor;
        self::assertCount(2, $armor);
        // One of the armor jackets has modifications.
        self::assertSame(
            [
                'id' => 'armor-jacket',
                'modifications' => [
                    'fire-resistance-2',
                    'gel-packs',
                ],
            ],
            // @phpstan-ignore-next-line
            $armor[0]
        );

        // The other has none.
        self::assertSame(['id' => 'armor-jacket'], $armor[1]);
    }

    /**
     * Test the character's augmentations.
     * @depends testLoadOmae
     * @param PartialCharacter $character
     * @test
     */
    public function testAugmentations(PartialCharacter $character): void
    {
        /** @var array<string, string|array<int, string>> */
        $augmentations = $character->augmentations;
        self::assertCount(3, $augmentations);
        self::assertContains(['id' => 'damper'], $augmentations);
        self::assertContains(['id' => 'cybereyes-1'], $augmentations);
        self::assertContains(['id' => 'bone-lacing-aluminum'], $augmentations);
    }

    /**
     * Test the errors from converting the character.
     * @test
     */
    public function testErrors(): void
    {
        $path = explode(
            \DIRECTORY_SEPARATOR,
            dirname(dirname(dirname(__DIR__)))
        );
        $path[] = 'Data';
        $path[] = 'Omae';
        $path[] = 'Test.txt';
        $filename = implode(\DIRECTORY_SEPARATOR, $path);
        $converter = new Shadowrun5eConverter($filename);
        $converter->convert();
        $errors = $converter->getErrors();
        self::assertCount(11, $errors);
        self::assertContains(
            'Found unhandled section "Unknown section"',
            $errors
        );
        self::assertContains(
            'Quality name "Invalid Quality" was not found',
            $errors
        );
        self::assertContains('Unknown gear category "custom"', $errors);
        self::assertContains(
            'Armor modification "Unknown" was not found',
            $errors
        );
        self::assertContains(
            'Armor modification "Not Found" was not found',
            $errors
        );
        self::assertContains(
            'Weapon modification "Unknown" was not found',
            $errors
        );
        self::assertContains('Augmentation "Not Found" was not found', $errors);
    }

    /**
     * Test loading a character with invalid data.
     * @test
     */
    public function testInvalid(): void
    {
        $path = explode(
            \DIRECTORY_SEPARATOR,
            dirname(dirname(dirname(__DIR__)))
        );
        $path[] = 'Data';
        $path[] = 'Omae';
        $path[] = 'Invalid.txt';
        $filename = implode(\DIRECTORY_SEPARATOR, $path);
        $converter = new Shadowrun5eConverter($filename);
        $converter->convert();

        $errors = $converter->getErrors();
        self::assertContains('Invalid priorities listed', $errors);
        self::assertContains('Unknown skill type "Inactive"', $errors);
    }

    /**
     * Test loading a mage's sheet.
     * @test
     */
    public function testLoadMage(): void
    {
        $path = explode(
            \DIRECTORY_SEPARATOR,
            dirname(dirname(dirname(__DIR__)))
        );
        $path[] = 'Data';
        $path[] = 'Omae';
        $path[] = 'Mage.txt';
        $filename = implode(\DIRECTORY_SEPARATOR, $path);
        $converter = new Shadowrun5eConverter($filename);
        $character = $converter->convert();
        self::assertSame(
            [
                'metatypePriority' => 'D',
                'attributePriority' => 'B',
                'magicPriority' => 'A',
                'skillPriority' => 'C',
                'resourcePriority' => 'E',
                'metatype' => 'human',
            ],
            $character->priorities
        );
    }

    /**
     * Test loading a technomancer's sheet.
     * @test
     */
    public function testLoadTechno(): void
    {
        $path = explode(
            \DIRECTORY_SEPARATOR,
            dirname(dirname(dirname(__DIR__)))
        );
        $path[] = 'Data';
        $path[] = 'Omae';
        $path[] = 'Techno.txt';
        $filename = implode(\DIRECTORY_SEPARATOR, $path);
        $converter = new Shadowrun5eConverter($filename);
        $character = $converter->convert();

        self::assertSame('Unnamed Omae import', $character->handle);
        self::assertSame(
            [
                'metatypePriority' => 'D',
                'attributePriority' => 'B',
                'magicPriority' => 'A',
                'skillPriority' => 'C',
                'resourcePriority' => 'E',
                'metatype' => 'human',
            ],
            $character->priorities
        );
    }
}
