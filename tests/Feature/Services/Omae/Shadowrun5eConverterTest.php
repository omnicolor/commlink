<?php

declare(strict_types=1);

namespace Tests\Feature\Services\Omae;

use App\Services\Omae\Shadowrun5eConverter;
use Modules\Shadowrun5e\Models\PartialCharacter;
use PHPUnit\Framework\Attributes\Depends;
use PHPUnit\Framework\Attributes\Small;
use RuntimeException;
use Tests\TestCase;

use function dirname;

use const DIRECTORY_SEPARATOR;

#[Small]
final class Shadowrun5eConverterTest extends TestCase
{
    public function testLoadNotFoundFile(): void
    {
        self::expectException(RuntimeException::class);
        self::expectExceptionMessage('Unable to locate Omae file');
        new Shadowrun5eConverter('not found');
    }

    public function testLoadBinaryFile(): void
    {
        self::expectException(RuntimeException::class);
        self::expectExceptionMessage('File does not appear to be an Omae file');
        $path = explode(
            DIRECTORY_SEPARATOR,
            dirname(__DIR__, 3)
        );
        $path[] = 'Data';
        $path[] = 'HeroLab';
        $path[] = 'Shadowrun5e';
        $path[] = 'valid-portfolio1.por';
        new Shadowrun5eConverter(implode(DIRECTORY_SEPARATOR, $path));
    }

    public function testLoadNotOmae(): void
    {
        self::expectException(RuntimeException::class);
        self::expectExceptionMessage('File does not appear to be an Omae file');
        new Shadowrun5eConverter(__FILE__);
    }

    public function testLoadOmae(): PartialCharacter
    {
        $path = explode(
            DIRECTORY_SEPARATOR,
            dirname(__DIR__, 3)
        );
        $path[] = 'Data';
        $path[] = 'Omae';
        $path[] = 'Test.txt';
        $filename = implode(DIRECTORY_SEPARATOR, $path);
        $character = (new Shadowrun5eConverter($filename))->convert();
        self::assertSame('Fastjack', $character->handle);
        return $character;
    }

    #[Depends('testLoadOmae')]
    public function testMetadata(PartialCharacter $character): void
    {
        self::assertSame('Jack Smith', $character->realName);
        self::assertSame('shadowrun5e', $character->system);
    }

    #[Depends('testLoadOmae')]
    public function testPriorities(PartialCharacter $character): void
    {
        $priorities = $character->priorities;
        self::assertSame('D', $priorities['metatypePriority'] ?? null);
        self::assertSame('A', $priorities['attributePriority']);
        self::assertSame('E', $priorities['magicPriority']);
        self::assertSame('B', $priorities['skillPriority']);
        self::assertSame('C', $priorities['resourcePriority']);
        self::assertSame('human', $priorities['metatype']);
    }

    #[Depends('testLoadOmae')]
    public function testKarma(PartialCharacter $character): void
    {
        self::assertSame(7, $character->karmaCurrent);
        self::assertSame(7, $character->karma);
    }

    #[Depends('testLoadOmae')]
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

    #[Depends('testLoadOmae')]
    public function testQualitites(PartialCharacter $character): void
    {
        $qualities = $character->qualities;
        self::assertNotNull($qualities);
        self::assertCount(3, $qualities);
        self::assertContains(['id' => 'natural-athlete'], $qualities);
        self::assertContains(['id' => 'quick-healer'], $qualities);
        self::assertContains(['id' => 'tough-and-targeted'], $qualities);
    }

    #[Depends('testLoadOmae')]
    public function testSkills(PartialCharacter $character): void
    {
        $skills = $character->skills;
        self::assertNotNull($skills);
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

    #[Depends('testLoadOmae')]
    public function testWeapons(PartialCharacter $character): void
    {
        $weapons = $character->weapons;
        self::assertNotNull($weapons);
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

    #[Depends('testLoadOmae')]
    public function testArmor(PartialCharacter $character): void
    {
        /** @var array<int, array<string, string>> */
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
            $armor[0]
        );

        // The other has none.
        self::assertSame(['id' => 'armor-jacket'], $armor[1]);
    }

    #[Depends('testLoadOmae')]
    public function testAugmentations(PartialCharacter $character): void
    {
        $augmentations = $character->augmentations;
        self::assertNotNull($augmentations);
        self::assertCount(3, $augmentations);
        self::assertContains(['id' => 'damper'], $augmentations);
        self::assertContains(['id' => 'cybereyes-1'], $augmentations);
        self::assertContains(['id' => 'bone-lacing-aluminum'], $augmentations);
    }

    public function testErrors(): void
    {
        $path = explode(
            DIRECTORY_SEPARATOR,
            dirname(__DIR__, 3)
        );
        $path[] = 'Data';
        $path[] = 'Omae';
        $path[] = 'Test.txt';
        $filename = implode(DIRECTORY_SEPARATOR, $path);
        $converter = new Shadowrun5eConverter($filename);
        $converter->convert();
        $errors = $converter->getErrors();
        self::assertContains(
            'Found unhandled section "Unknown section"',
            $errors['section'],
        );
        self::assertContains(
            'Quality name "Invalid Quality" was not found',
            $errors['qualities'],
        );
        self::assertContains(
            'Unknown gear category "custom"',
            $errors['gear'],
        );
        self::assertContains(
            'Armor modification "Unknown" was not found',
            $errors['armor'],
        );
        self::assertContains(
            'Armor modification "Not Found" was not found',
            $errors['armor'],
        );
        self::assertContains(
            'Weapon modification "Unknown" was not found',
            $errors['weapons'],
        );
        self::assertContains(
            'Augmentation "Not Found" was not found',
            $errors['augmentations'],
        );
    }

    public function testInvalid(): void
    {
        $path = explode(
            DIRECTORY_SEPARATOR,
            dirname(__DIR__, 3)
        );
        $path[] = 'Data';
        $path[] = 'Omae';
        $path[] = 'Invalid.txt';
        $filename = implode(DIRECTORY_SEPARATOR, $path);
        $converter = new Shadowrun5eConverter($filename);
        $converter->convert();

        $errors = $converter->getErrors();
        self::assertContains(
            'Invalid priorities listed',
            $errors['priorities'],
        );
        self::assertContains(
            'Unknown skill type "Inactive"',
            $errors['skills'],
        );
    }

    public function testLoadMage(): void
    {
        $path = explode(
            DIRECTORY_SEPARATOR,
            dirname(__DIR__, 3)
        );
        $path[] = 'Data';
        $path[] = 'Omae';
        $path[] = 'Mage.txt';
        $filename = implode(DIRECTORY_SEPARATOR, $path);
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

    public function testLoadTechno(): void
    {
        $path = explode(
            DIRECTORY_SEPARATOR,
            dirname(__DIR__, 3)
        );
        $path[] = 'Data';
        $path[] = 'Omae';
        $path[] = 'Techno.txt';
        $filename = implode(DIRECTORY_SEPARATOR, $path);
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
