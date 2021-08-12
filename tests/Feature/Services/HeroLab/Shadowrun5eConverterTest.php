<?php

declare(strict_types=1);

namespace Tests\Feature\Services\HeroLab;

use App\Models\Shadowrun5E\Character;
use App\Services\HeroLab\Shadowrun5eConverter;

/**
 * Functional tests for HeroLab Shadowrun5E converter.
 * @group herolab
 * @small
 */
final class Shadowrun5eConverterTest extends \Tests\TestCase
{
    protected string $dataDirectory;

    public function __construct()
    {
        $path = explode(
            \DIRECTORY_SEPARATOR,
            dirname(dirname(dirname(__DIR__)))
        );
        $path[] = 'Data';
        $path[] = 'HeroLab';
        $path[] = 'Shadowrun5e';
        $path[] = null;
        $this->dataDirectory = implode(\DIRECTORY_SEPARATOR, $path);
        parent::__construct();
    }

    /**
     * Test creating an ID from something's name.
     * @test
     */
    public function testCreateIDFromName(): void
    {
        $hl = new Shadowrun5eConverter($this->dataDirectory . 'brian.por');
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
     * @test
     */
    public function testLoadNotFound(): void
    {
        self::expectException(\RuntimeException::class);
        self::expectExceptionMessage('Portfolio not found');
        new Shadowrun5eConverter('not-found-portfolio.por');
    }

    /**
     * Test trying to load a portfolio that is an existing file, but is not
     * a Hero Lab portfolio.
     * @test
     */
    public function testInvalidPortfolio(): void
    {
        self::expectException(\RuntimeException::class);
        self::expectExceptionMessage('Portfolio is not valid');
        new Shadowrun5eConverter(__FILE__);
    }

    /**
     * Test trying to load an invalid file.
     * @test
     */
    public function testBadZipPortfolio(): void
    {
        self::expectException(\RuntimeException::class);
        self::expectExceptionMessage(
            'Opening portfolio failed with unknown code: 28'
        );
        new Shadowrun5eConverter('/dev/null');
    }

    /**
     * Test trying to load a portfolio with invalid XML.
     * @test
     */
    public function testBadXml(): void
    {
        self::expectException(\RuntimeException::class);
        self::expectExceptionMessage('Failed to load Portfolio stats');
        new Shadowrun5eConverter($this->dataDirectory . 'bad-xml.por');
    }

    /**
     * Test trying to load a portfolio without the required XML files.
     * @test
     */
    public function testEmptyPortfolio(): void
    {
        self::expectException(\RuntimeException::class);
        self::expectExceptionMessage('Failed to load Portfolio stats');
        new Shadowrun5eConverter($this->dataDirectory . 'no-files.por');
    }

    /**
     * Test that converting a valid Hero Lab portfolio returns a Commlink
     * character.
     * @test
     */
    public function testConvertPortfolio(): void
    {
        $hl = new Shadowrun5eConverter($this->dataDirectory . 'brian.por');
        $expectedBackground = [
            'age' => 25,
            'hair' => '',
            'eyes' => '',
            'skin' => '',
        ];

        $character = $hl->convert();
        self::assertInstanceOf(Character::class, $character);
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
        self::assertCount(12, (array)$character->skills);
        self::assertCount(7, (array)$character->knowledgeSkills);
        // @phpstan-ignore-next-line
        self::assertCount(6, $character->magics['powers']);
        self::assertCount(3, (array)$character->augmentations);
        self::assertCount(2, (array)$character->weapons);
        self::assertCount(3, (array)$character->armor);
        self::assertCount(3, (array)$character->gear);
        self::assertCount(1, (array)$character->identities);
        self::assertCount(2, (array)$character->contacts);

        self::assertEquals([], $hl->getErrors());
    }
}
