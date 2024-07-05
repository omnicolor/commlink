<?php

declare(strict_types=1);

namespace Tests\Feature\Services\WorldAnvil;

use App\Services\WorldAnvil\ExpanseConverter;
use Modules\Expanse\Models\Origin\Martian;
use PHPUnit\Framework\Attributes\Group;
use PHPUnit\Framework\Attributes\Medium;
use RuntimeException;
use Tests\TestCase;

use function dirname;
use function explode;
use function implode;

use const DIRECTORY_SEPARATOR;

#[Group('expanse')]
#[Group('world-anvil')]
#[Medium]
final class ExpanseConverterTest extends TestCase
{
    protected static string $testFile;

    public static function setUpBeforeClass(): void
    {
        $path = explode(DIRECTORY_SEPARATOR, dirname(__DIR__, 3));
        $path[] = 'Data';
        $path[] = 'WorldAnvil';
        $path[] = 'Expanse';
        $path[] = 'AricHessel.json';
        self::$testFile = implode(DIRECTORY_SEPARATOR, $path);
    }

    public function testConstructorWithNotFoundFile(): void
    {
        self::expectException(RuntimeException::class);
        self::expectExceptionMessage('Unable to locate World Anvil file');
        new ExpanseConverter('/dev/null/not-found.json');
    }

    public function testConstructorInvalidFile(): void
    {
        self::expectException(RuntimeException::class);
        self::expectExceptionMessage(
            'File does not appear to be a World Anvil file'
        );
        new ExpanseConverter(__FILE__);
    }

    public function testConstructorWrongSystem(): void
    {
        // We'll use a valid World Anvil character from Cyberpunk Red.
        $path = explode(DIRECTORY_SEPARATOR, dirname(__DIR__, 3));
        $path[] = 'Data';
        $path[] = 'WorldAnvil';
        $path[] = 'CyberpunkRed';
        $path[] = 'Caleb.json';
        $testFile = implode(DIRECTORY_SEPARATOR, $path);

        self::expectException(RuntimeException::class);
        self::expectExceptionMessage('Character is not an Expanse character');
        new ExpanseConverter($testFile);
    }

    public function testConvertValidCharacter(): void
    {
        $converter = new ExpanseConverter(self::$testFile);
        $character = $converter->convert();
        self::assertCount(5, $converter->getErrors());
        self::assertSame('Aric Hessel', $character->name);
        self::assertSame('Male', $character->gender);
        self::assertInstanceOf(Martian::class, $character->origin);
        self::assertSame('trade', $character->background->id);
        self::assertSame('middle', $character->social_class->id);
        self::assertSame('Scavenger', $character->profession);
        self::assertSame('Red skin, like Mar\'s soil.', $character->appearance);
        self::assertSame('Penitent, test', $character->drive);
        self::assertSame(1, $character->level);
        self::assertSame(0, $character->experience);
        self::assertSame(32, $character->age);
        self::assertSame(1, $character->accuracy);
        self::assertSame(2, $character->communication);
        self::assertSame(3, $character->constitution);
        self::assertSame(5, $character->dexterity);
        self::assertSame(0, $character->fighting);
        self::assertSame(-1, $character->intelligence);
        self::assertSame(1, $character->perception);
        self::assertSame(-1, $character->strength);
        self::assertSame(1, $character->willpower);
        self::assertSame('fringer', $character->getTalents()[0]?->id);
        self::assertCount(5, $character->getFocuses());
        self::assertSame('crafting', $character->getFocuses()[0]?->id);
    }
}
