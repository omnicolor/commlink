<?php

declare(strict_types=1);

namespace Tests\Feature\Services\HeroLab;

use App\Services\HeroLab\Shadowrun5eConverter;
use Modules\Shadowrun5e\Models\Character;
use PHPUnit\Framework\Attributes\Group;
use PHPUnit\Framework\Attributes\Small;
use RuntimeException;
use Tests\TestCase;

use function dirname;
use function explode;
use function implode;

use const DIRECTORY_SEPARATOR;

#[Group('herolab')]
#[Small]
final class Shadowrun5eConverterTest extends TestCase
{
    protected static string $dataDirectory;

    public static function setUpBeforeClass(): void
    {
        $path = explode(
            DIRECTORY_SEPARATOR,
            dirname(__DIR__, 3)
        );
        $path[] = 'Data';
        $path[] = 'HeroLab';
        $path[] = 'Shadowrun5e';
        $path[] = null;
        self::$dataDirectory = implode(DIRECTORY_SEPARATOR, $path);
    }

    /**
     * Test creating an ID from something's name.
     */
    public function testCreateIDFromName(): void
    {
        $hl = new Shadowrun5eConverter(
            self::$dataDirectory . 'valid-portfolio1.por'
        );
        self::assertSame('blades', $hl->createIDFromName('Blades'));
        self::assertSame(
            'pilot-ground-craft',
            $hl->createIDFromName('Pilot Ground Craft')
        );
        self::assertSame(
            'critical-strike-unarmed-combat',
            $hl->createIDFromName('Critical Strike: Unarmed Combat')
        );
    }

    /**
     * Test trying to load a portfolio that doesn't exist.
     */
    public function testLoadNotFound(): void
    {
        self::expectException(RuntimeException::class);
        self::expectExceptionMessage('Portfolio not found');
        new Shadowrun5eConverter('not-found-portfolio.por');
    }

    /**
     * Test trying to load a portfolio that is an existing file, but is not
     * a Hero Lab portfolio.
     */
    public function testInvalidPortfolio(): void
    {
        self::expectException(RuntimeException::class);
        self::expectExceptionMessage('Portfolio is not valid');
        new Shadowrun5eConverter(__FILE__);
    }

    /**
     * Test trying to load an invalid file.
     */
    public function testBadZipPortfolio(): void
    {
        self::expectException(RuntimeException::class);
        self::expectExceptionMessage(
            'Opening portfolio failed with unknown code: 28'
        );
        new Shadowrun5eConverter('/dev/null');
    }

    public function testDifferentSystemPortfolio(): void
    {
        self::expectException(RuntimeException::class);
        self::expectExceptionMessage('The portfolio isn\'t a Shadowrun 5th edition character');
        new Shadowrun5eConverter(self::$dataDirectory . 'different-system.por');
    }

    /**
     * Test trying to load a portfolio with invalid XML.
     */
    public function testBadXml(): void
    {
        self::expectException(RuntimeException::class);
        self::expectExceptionMessage('Portfolio metadata is invalid');
        new Shadowrun5eConverter(self::$dataDirectory . 'bad-xml.por');
    }

    /**
     * Test trying to load a portfolio with valid main XML but invalid lead1
     * XML.
     */
    public function testBadLeadXml(): void
    {
        self::expectException(RuntimeException::class);
        self::expectExceptionMessage('Portfolio metadata is invalid');
        new Shadowrun5eConverter(
            self::$dataDirectory . 'invalid-priorities.por'
        );
    }

    /**
     * Test trying to load a portfolio with valid main XML but lead1.xml is
     * missing.
     */
    public function testMissingLeadXml(): void
    {
        self::expectException(RuntimeException::class);
        self::expectExceptionMessage('Portfolio metadata is invalid');
        new Shadowrun5eConverter(self::$dataDirectory . 'missing-lead1.por');
    }

    /**
     * Test trying to load a portfolio without the required XML files.
     */
    public function testEmptyPortfolio(): void
    {
        self::expectException(RuntimeException::class);
        self::expectExceptionMessage('Portfolio metadata is invalid');
        new Shadowrun5eConverter(self::$dataDirectory . 'no-files.por');
    }

    public function testValidPortfolioWithBadStatblockXml(): void
    {
        self::expectException(RuntimeException::class);
        self::expectExceptionMessage('Failed to load Portfolio stats');
        new Shadowrun5eConverter(self::$dataDirectory . 'bad-statblock.por');
    }

    /**
     * Test that converting a valid Hero Lab portfolio returns a Commlink
     * character.
     */
    public function testConvertPortfolio(): void
    {
        $hl = new Shadowrun5eConverter(
            self::$dataDirectory . 'valid-portfolio1.por'
        );
        $expectedBackground = [
            'age' => 25,
            'hair' => '',
            'eyes' => '',
            'skin' => '',
        ];

        $character = $hl->convert();
        self::assertSame('Brian', $character->handle);
        self::assertSame('Human', $character->metatype);
        self::assertSame(3, $character->karma);
        self::assertSame(3, $character->karmaCurrent);
        self::assertSame(1000, $character->nuyen);
        self::assertSame('male', $character->gender);
        self::assertEquals($expectedBackground, $character->background);
        self::assertSame(6, $character->agility);
        self::assertSame(5, $character->body);
        self::assertSame(3, $character->charisma);
        self::assertSame(3, $character->intuition);
        self::assertSame(3, $character->logic);
        self::assertSame(3, $character->reaction);
        self::assertSame(5, $character->strength);
        self::assertSame(4, $character->willpower);
        self::assertSame(3, $character->edge);
        self::assertSame(6, $character->magic);
        self::assertNull($character->resonance);
        self::assertCount(8, (array)$character->qualities);
        self::assertCount(1, (array)$character->skillGroups);
        self::assertCount(9, (array)$character->skills);
        self::assertCount(7, (array)$character->knowledgeSkills);
        self::assertCount(6, $character->magics['powers'] ?? null);
        self::assertCount(3, (array)$character->augmentations);
        self::assertCount(2, (array)$character->weapons);
        self::assertCount(3, (array)$character->armor);
        self::assertCount(3, (array)$character->gear);
        self::assertCount(1, (array)$character->identities);
        self::assertCount(2, (array)$character->contacts);
        self::assertSame(
            [
                'metatype' => 'Human',
                'attributePriority' => 'A',
                'magicPriority' => 'D',
                'metatypePriority' => 'C',
                'resourcePriority' => 'D',
                'skillPriority' => 'C',
                'gameplay' => 'established',
                'system' => 'sum-to-ten',
                'rulebooks' => 'aetherology,assassins-primer,bloody-business,'
                    . 'bullets-and-bandages,chrome-flesh,core,court-of-shadows,'
                    . 'cutting-aces,data-trails,gun-heaven-3,hard-targets,'
                    . 'howling-shadows,lockdown,rigger-5,run-and-gun,'
                    . 'run-faster,shadow-spells,shadows-in-focus-butte,'
                    . 'shadows-in-focus-san-francisco-metroplex,'
                    . 'shadows-in-focus-sioux-nation,stolen-souls,'
                    . 'street-grimoire,vladivostok-guantlet',
            ],
            $character->priorities
        );

        self::assertEquals([], $hl->getErrors());
    }

    /**
     * Test converting a test Hero Lab portfolio.
     */
    public function testAnotherPortfolio(): void
    {
        $hl = new Shadowrun5eConverter(
            self::$dataDirectory . 'valid-portfolio2.por'
        );
        $character = $hl->convert();

        self::assertStringNotContainsString(
            'run-faster',
            (string)($character->priorities['rulebooks'] ?? ''),
        );
        self::assertSame('street', $character->priorities['gameplay'] ?? null);
        self::assertSame('Troll', $character->priorities['metatype']);
        self::assertSame(7, $character->body);
        self::assertSame(4, $character->agility);
        self::assertSame(5, $character->reaction);
        self::assertSame(6, $character->strength);
        self::assertSame(4, $character->willpower);
        self::assertSame(3, $character->logic);
        self::assertSame(3, $character->intuition);
        self::assertSame(4, $character->charisma);
        self::assertSame(6, $character->resonance);
        self::assertNull($character->magic);
        self::assertSame(3, $character->edge);
        self::assertCount(2, $character->gear ?? []);
        $errors = $hl->getErrors();
        self::assertCount(5, $errors);
        self::assertSame(
            'Quality "Custom quality: None" was not found.',
            $errors['qualities'][0],
        );
        self::assertCount(5, $errors['skills']);
        // TODO: Implement martial arts
        self::assertEmpty($character->martialArts);
        // TODO: Implement complex forms
        self::assertEmpty($character->complexForms);
        self::assertEmpty($character->magics);
    }

    /**
     * Test converting a mage that has initiated.
     */
    public function testPrime(): void
    {
        $hl = new Shadowrun5eConverter(self::$dataDirectory . 'prime.por');
        $character = $hl->convert();

        self::assertCount(4, $character->magics['spells'] ?? null);
        self::assertCount(1, $character->magics['metamagics'] ?? null);
    }

    /**
     * Test converting a portfolio that uses the life module system.
     */
    public function testLifeModule(): void
    {
        $hl = new Shadowrun5eConverter(self::$dataDirectory . 'life-module.por');
        $character = $hl->convert();

        self::assertStringContainsString(
            'run-faster',
            (string)($character->priorities['rulebooks'] ?? ''),
        );
        self::assertSame(
            'life-module',
            $character->priorities['system'] ?? null,
        );
    }

    /**
     * Test converting a portfolio that uses the point buy system.
     */
    public function testPointBuy(): void
    {
        $hl = new Shadowrun5eConverter(self::$dataDirectory . 'point-buy.por');
        $character = $hl->convert();

        self::assertStringContainsString(
            'run-faster',
            (string)($character->priorities['rulebooks'] ?? ''),
        );
        self::assertSame('point-buy', $character->priorities['system'] ?? null);
    }

    /**
     * Test a portfolio for a seasoned mage that has initiated and gained a
     * metamagic.
     */
    public function testMetamagic(): void
    {
        $hl = new Shadowrun5eConverter(self::$dataDirectory . 'metamagic.por');
        $character = $hl->convert();

        self::assertSame(
            ['astral-bluff', 'channeling'],
            $character->magics['metamagics'] ?? null
        );
    }

    public function testVehicles(): void
    {
        $hl = new Shadowrun5eConverter(self::$dataDirectory . 'vehicles.por');
        $character = $hl->convert();

        self::assertCount(3, $character->vehicles ?? []);
        $hound = $character->vehicles[2] ?? null;
        self::assertSame('Da G-Ride', $hound['subname'] ?? null);
        self::assertSame('ak-98', $hound['weapons'][0]['id']);
        self::assertSame(
            'acceleration-enhancement-1',
            $hound['modifications'][0]['id'],
        );
        self::assertSame('credstick-silver', $hound['gear'][0]['id']);
    }
}
