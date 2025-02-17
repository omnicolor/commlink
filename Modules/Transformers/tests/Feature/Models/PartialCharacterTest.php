<?php

declare(strict_types=1);

namespace Modules\Transformers\Tests\Feature\Models;

use Modules\Transformers\Models\PartialCharacter;
use PHPUnit\Framework\Attributes\Group;
use PHPUnit\Framework\Attributes\Small;
use Tests\TestCase;

#[Group('transformers')]
#[Small]
final class PartialCharacterTest extends TestCase
{
    public function testNewFromBuilder(): void
    {
        $character = new PartialCharacter([
            'name' => 'Test Transformers character',
            'created_by' => __CLASS__ . '::' . __FUNCTION__,
        ]);
        $character->save();

        $loaded = PartialCharacter::find($character->id);
        self::assertInstanceOf(PartialCharacter::class, $loaded);
        self::assertSame('Test Transformers character', $loaded->name);
        $character->delete();
    }
}
