<?php

declare(strict_types=1);

namespace Tests\Feature\Models\Cyberpunkred;

use App\Models\Cyberpunkred\PartialCharacter;
use PHPUnit\Framework\Attributes\Small;
use Tests\TestCase;

/**
 * @group cyberpunkred
 */
#[Small]
final class PartialCharacterTest extends TestCase
{
    /**
     * Test creating a new lifepath array for a character.
     */
    public function testInitializeLifepath(): void
    {
        $character = new PartialCharacter();
        self::assertNull($character->lifepath);
        $character->initializeLifepath();
        self::assertArrayHasKey('affectation', $character->lifepath);
        self::assertArrayHasKey('affectation', $character->lifepath);
        self::assertArrayHasKey('background', $character->lifepath);
        self::assertArrayHasKey('clothing', $character->lifepath);
        self::assertArrayHasKey('environment', $character->lifepath);
        self::assertArrayHasKey('feeling', $character->lifepath);
        self::assertArrayHasKey('hair', $character->lifepath);
        self::assertArrayHasKey('origin', $character->lifepath);
        self::assertArrayHasKey('person', $character->lifepath);
        self::assertArrayHasKey('personality', $character->lifepath);
        self::assertArrayHasKey('possession', $character->lifepath);
        self::assertArrayHasKey('value', $character->lifepath);
        self::assertArrayHasKey('rolled', $character->lifepath['value']);
        self::assertArrayHasKey('chosen', $character->lifepath['value']);
    }

    /**
     * Test that initializing a lifepath doesn't overwrite one if it already
     * exists.
     */
    public function testInitializeLifepathDoesntOverwrite(): void
    {
        $character = new PartialCharacter(['lifepath' => 'huh...']);
        self::assertSame('huh...', $character->lifepath);
        $character->initializeLifepath();
        self::assertSame('huh...', $character->lifepath);
    }

    /**
     * @medium
     */
    public function testNewFromBuilder(): void
    {
        $character = new PartialCharacter([
            'handle' => 'Test Cyberpunk character',
        ]);
        $character->save();

        $loaded = PartialCharacter::find($character->id);
        // @phpstan-ignore-next-line
        self::assertSame('Test Cyberpunk character', $loaded->handle);
        $character->delete();
    }
}
