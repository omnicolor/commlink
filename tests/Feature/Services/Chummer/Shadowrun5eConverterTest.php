<?php

declare(strict_types=1);

namespace Tests\Feature\Services\Chummer;

use App\Services\Chummer5\Shadowrun5eConverter;

/**
 * Tests for Chummer 5 converter.
 * @group chummer5
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
        $path[] = 'Chummer5';
        $path[] = null;
        $this->dataDirectory = implode(\DIRECTORY_SEPARATOR, $path);
        parent::__construct();
    }

    /**
     * Test trying to load a Chummer file that doesn't exist.
     * @test
     */
    public function testLoadNotFound(): void
    {
        self::expectException(\RuntimeException::class);
        self::expectExceptionMessage('"not-found.chum5" does not exist');
        new Shadowrun5eConverter('not-found.chum5');
    }

    /**
     * Test trying to load a Chummer file that isn't XML.
     * @test
     */
    public function testLoadNotXML(): void
    {
        self::expectException(\RuntimeException::class);
        self::expectExceptionMessage('Could not parse XML in Chummer file');
        new Shadowrun5eConverter(__FILE__);
    }

    /**
     * Test trying to convert a valid Chummer 5 file.
     * @test
     */
    public function testConvertBirdman(): void
    {
        $converter = new Shadowrun5eConverter(
            $this->dataDirectory . 'birdman.chum5'
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
}
