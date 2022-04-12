<?php

declare(strict_types=1);

namespace Tests\Feature\Models\Capers;

use App\Models\Capers\PartialCharacter;

/**
 * @group capers
 * @group models
 * @small
 */
final class PartialCharacterTest extends \Tests\TestCase
{
    /**
     * @test
     */
    public function testNewFromBuilder(): void
    {
        $character = new PartialCharacter(['name' => 'Test character']);
        $character->save();

        $loaded = PartialCharacter::find($character->id);
        // @phpstan-ignore-next-line
        self::assertSame('Test character', $loaded->name);
    }
}
