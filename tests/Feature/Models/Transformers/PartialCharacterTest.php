<?php

declare(strict_types=1);

namespace Tests\Feature\Models\Transformers;

use App\Models\Transformers\PartialCharacter;
use Tests\TestCase;

/**
 * @group models
 * @group transformers
 * @small
 */
final class PartialCharacterTest extends TestCase
{
    /**
     * @test
     */
    public function testNewFromBuilder(): void
    {
        $character = new PartialCharacter([
            'name' => 'Test Transformers character',
            'created_by' => __CLASS__ . '::' . __FUNCTION__,
        ]);
        $character->save();

        $loaded = PartialCharacter::find($character->id);
        // @phpstan-ignore-next-line
        self::assertSame('Test Transformers character', $loaded->name);
        $character->delete();
    }
}
