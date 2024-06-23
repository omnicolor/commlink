<?php

declare(strict_types=1);

namespace Tests\Feature\Models\Expanse;

use App\Models\Expanse\PartialCharacter;
use PHPUnit\Framework\Attributes\Group;
use PHPUnit\Framework\Attributes\Small;
use Tests\TestCase;

#[Group('expanse')]
#[Small]
final class PartialCharacterTest extends TestCase
{
    public function testNewFromBuilder(): void
    {
        $character = new PartialCharacter(['name' => 'Bob King']);
        $character->save();

        $loaded = PartialCharacter::find($character->id);
        self::assertSame('Bob King', (string)$loaded);
        $character->delete();
    }
}
