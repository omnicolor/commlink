<?php

declare(strict_types=1);

namespace Tests\Feature\Services\Chummer;

use App\Models\Shadowrun5e\Identity;
use App\Models\Shadowrun5e\Tradition;
use App\Services\Chummer5\Shadowrun5eConverter;
use RuntimeException;
use Tests\TestCase;

use const DIRECTORY_SEPARATOR;

/**
 * Tests for Chummer 5 converter.
 * @group chummer5
 * @small
 */
final class Shadowrun5eConverterTest extends TestCase
{
    protected static string $dataDirectory;

    public static function setUpBeforeClass(): void
    {
        $path = explode(
            DIRECTORY_SEPARATOR,
            dirname(dirname(dirname(__DIR__)))
        );
        $path[] = 'Data';
        $path[] = 'Chummer5';
        $path[] = null;
        self::$dataDirectory = implode(DIRECTORY_SEPARATOR, $path);
    }

    /**
     * Test trying to load a Chummer file that doesn't exist.
     */
    public function testLoadNotFound(): void
    {
        self::expectException(RuntimeException::class);
        self::expectExceptionMessage('"not-found.chum5" does not exist');
        new Shadowrun5eConverter('not-found.chum5');
    }

    /**
     * Test trying to load a Chummer file that isn't XML.
     */
    public function testLoadNotXML(): void
    {
        self::expectException(RuntimeException::class);
        self::expectExceptionMessage('Could not parse XML in Chummer file');
        new Shadowrun5eConverter(__FILE__);
    }

    /**
     * Test trying to convert a valid Chummer 5 file.
     */
    public function testConvertBirdman(): void
    {
        $converter = new Shadowrun5eConverter(
            self::$dataDirectory . 'birdman.chum5'
        );
        $character = $converter->convert();
        self::assertSame('human', $character->metatype);
        self::assertSame('BirdMan', $character->handle);
        self::assertSame(2, $character->agility);
        self::assertCount(2, (array)$character->armor);
        self::assertCount(0, (array)$character->augmentations);
        self::assertSame(3, $character->body);
        self::assertSame(6, $character->charisma);
        self::assertCount(3, (array)$character->contacts);
        self::assertSame(3, $character->edge);
        self::assertCount(2, (array)$character->gear);
        self::assertCount(2, (array)$character->identities);
        self::assertSame(4, $character->intuition);
        self::assertSame(0, $character->karma);
        self::assertSame(0, $character->karmaCurrent);
        self::assertSame(1, $character->logic);
        self::assertSame(6, $character->magic);
        // @phpstan-ignore-next-line
        self::assertCount(1, $character->magics['spells']);
        self::assertSame(5020, $character->nuyen);
        self::assertNotEmpty($character->magics);
        self::assertCount(1, (array)$character->qualities);
        self::assertSame(2, $character->reaction);
        self::assertNull($character->resonance);
        self::assertSame(1, $character->strength);
        self::assertCount(0, (array)$character->weapons);
        self::assertSame(5, $character->willpower);
        self::assertCount(2, $character->getSkills());
        self::assertCount(5, $character->getKnowledgeSkills());
        self::assertCount(55, $converter->getErrors());
    }

    /**
     * Test trying to load a Chummer 5 file for a non-magician and non-adept
     * that has a magic rating.
     */
    public function testConvertInvalidMagicAttribute(): void
    {
        $converter = new Shadowrun5eConverter(
            self::$dataDirectory . 'test.chum5'
        );
        $character = $converter->convert();
        self::assertNull($character->magic);
    }

    /**
     * Test trying to load a lifestyles if the character doesn't have any SINs.
     */
    public function testLifestylesWithoutIdentities(): void
    {
        $converter = new Shadowrun5eConverter(
            self::$dataDirectory . 'test.chum5'
        );
        $character = $converter->convert();
        self::assertEmpty($character->getIdentities());
    }

    /**
     * Test trying to load a character's identities.
     */
    public function testIdentities(): void
    {
        $converter = new Shadowrun5eConverter(
            self::$dataDirectory . 'sins.chum5'
        );
        $character = $converter->convert();
        self::assertNotEmpty($character->getIdentities());
        /** @var Identity */
        $identity = $character->getIdentities()[0];
        self::assertCount(1, $identity->licenses);
        self::assertCount(1, $identity->lifestyles);
        self::assertEmpty($identity->subscriptions);
        self::assertSame(4, $identity->sin);
        self::assertSame('joe cotton', $identity->name);
    }

    /**
     * Test a character that specialized in a knowledge skill.
     */
    public function testKnowledgeSpecializations(): void
    {
        $converter = new Shadowrun5eConverter(
            self::$dataDirectory . 'test.chum5'
        );
        $character = $converter->convert();
        $skills = $character->getKnowledgeSkills();
        $english = null;
        foreach ($skills as $skill) {
            if ('English' === $skill->name) {
                $english = $skill;
                break;
            }
        }
        if (null === $english) {
            self::fail('Could not find knowledge skill under test');
        }
        self::assertSame('Written', $english->specialization);
    }

    /**
     * Test a character that specialized in an active skill.
     */
    public function testActiveSkillSpecializations(): void
    {
        $converter = new Shadowrun5eConverter(
            self::$dataDirectory . 'test.chum5'
        );
        $character = $converter->convert();
        $skills = $character->getSkills();
        $combat = null;
        foreach ($skills as $skill) {
            if ('Astral Combat' === $skill->name) {
                $combat = $skill;
                break;
            }
        }
        if (null === $combat) {
            self::fail('Could not find active skill under test');
        }
        self::assertSame(
            'While high,Opponent - Spirits',
            $combat->specialization
        );
    }

    /**
     * Test loading a character's magical tradition if they have one.
     */
    public function testTradition(): void
    {
        $converter = new Shadowrun5eConverter(
            self::$dataDirectory . 'sins.chum5'
        );
        $character = $converter->convert();
        /** @var Tradition */
        $tradition = $character->getTradition();
        self::assertSame('Norse', $tradition->name);
    }

    /**
     * Test loading a weapon.
     */
    public function testWeapons(): void
    {
        $converter = new Shadowrun5eConverter(
            self::$dataDirectory . 'test.chum5'
        );
        $character = $converter->convert();
        $ak = null;
        foreach ($character->getWeapons() as $weapon) {
            if ('AK-98' === $weapon->name) {
                $ak = $weapon;
                break;
            }
        }
        if (null === $ak) {
            self::fail('Could not find weapon under test');
        }
        self::assertSame('AK-98', $ak->name);
    }

    /**
     * Test loading a character with a mapped quality.
     */
    public function testMappedQuality(): void
    {
        $converter = new Shadowrun5eConverter(
            self::$dataDirectory . 'Blindfire.chum5'
        );
        $character = $converter->convert();
        $albinism = null;
        foreach ($character->getQualities() as $quality) {
            if ('Albinism' === $quality->name) {
                $albinism = $quality;
                break;
            }
        }
        if (null === $albinism) {
            self::fail('Could not find quality under test');
        }
        self::assertSame('Albinism', $albinism->name);
    }
}
