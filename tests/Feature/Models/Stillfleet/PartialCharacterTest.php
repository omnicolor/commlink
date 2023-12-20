<?php

declare(strict_types=1);

namespace Tests\Feature\Models\Stillfleet;

use App\Models\Stillfleet\PartialCharacter;
use Tests\TestCase;

/**
 * @group stillfleet
 * @small
 */
final class PartialCharacterTest extends TestCase
{
    public function testToCharacter(): void
    {
        $partialCharacter = new PartialCharacter(['_id' => 'deadbeef']);
        $character = $partialCharacter->toCharacter();
        self::assertNull($character->_id);
    }
}
