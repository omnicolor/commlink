<?php

declare(strict_types=1);

namespace Tests\Feature\Services\WorldAnvil;

use App\Services\WorldAnvil\CyberpunkRedConverter;
use PHPUnit\Framework\Attributes\Group;
use PHPUnit\Framework\Attributes\Medium;
use RuntimeException;
use Tests\TestCase;

use function dirname;
use function explode;
use function implode;

use const DIRECTORY_SEPARATOR;

#[Group('cyberpunk-red')]
#[Group('world-anvil')]
#[Medium]
final class CyberpunkRedConverterTest extends TestCase
{
    protected static string $testFile;

    public static function setUpBeforeClass(): void
    {
        $path = explode(DIRECTORY_SEPARATOR, dirname(__DIR__, 3));
        $path[] = 'Data';
        $path[] = 'WorldAnvil';
        $path[] = 'CyberpunkRed';
        $path[] = 'Caleb.json';
        self::$testFile = implode(DIRECTORY_SEPARATOR, $path);
    }

    public function testConstructorWithNotFoundFile(): void
    {
        self::expectException(RuntimeException::class);
        self::expectExceptionMessage('Unable to locate World Anvil file');
        new CyberpunkRedConverter('/dev/null/not-found.json');
    }

    public function testConstructorInvalidFile(): void
    {
        self::expectException(RuntimeException::class);
        self::expectExceptionMessage(
            'File does not appear to be a World Anvil file'
        );
        new CyberpunkRedConverter(__FILE__);
    }

    public function testConstructorWrongSystem(): void
    {
        // We'll use a valid World Anvil character from The Expanse.
        $path = explode(DIRECTORY_SEPARATOR, dirname(__DIR__, 3));
        $path[] = 'Data';
        $path[] = 'WorldAnvil';
        $path[] = 'Expanse';
        $path[] = 'AricHessel.json';
        $testFile = implode(DIRECTORY_SEPARATOR, $path);

        self::expectException(RuntimeException::class);
        self::expectExceptionMessage(
            'Character is not a Cyberpunk Red character'
        );
        new CyberpunkRedConverter($testFile);
    }

    public function testConvertValidCharacter(): void
    {
        $converter = new CyberpunkRedConverter(self::$testFile);
        $character = $converter->convert();
        self::assertCount(2, (array)$character->getSkills());
        self::assertSame('light-armorjack', $character->armor['head']?->id);
        self::assertSame('light-armorjack', $character->armor['body']?->id);
        self::assertSame('light-armorjack', $character->armor['shield']?->id);
        self::assertCount(2, $character->weapons);
        self::assertCount(22, $converter->getErrors());
    }

    public function testConvertCharacterWithInvalidData(): void
    {
        $path = explode(DIRECTORY_SEPARATOR, dirname(__DIR__, 3));
        $path[] = 'Data';
        $path[] = 'WorldAnvil';
        $path[] = 'CyberpunkRed';
        $path[] = 'BadData.json';
        $testFile = implode(DIRECTORY_SEPARATOR, $path);

        $converter = new CyberpunkRedConverter($testFile);
        $character = $converter->convert();

        self::assertNull($character->armor['head']);
        self::assertNull($character->armor['body']);
        self::assertNull($character->armor['shield']);
        self::assertContains(
            'Role "Unknown" is invalid',
            $converter->getErrors(),
        );
        self::assertContains(
            'Armor "Unknown helmet" was not found',
            $converter->getErrors(),
        );
        self::assertContains(
            'Armor "Unknown armor" was not found',
            $converter->getErrors(),
        );
        self::assertContains(
            'Armor "Unknown shield" was not found',
            $converter->getErrors(),
        );
    }
}
