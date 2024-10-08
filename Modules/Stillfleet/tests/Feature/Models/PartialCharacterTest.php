<?php

declare(strict_types=1);

namespace Modules\Stillfleet\Tests\Feature\Models;

use Modules\Stillfleet\Models\PartialCharacter;
use PHPUnit\Framework\Attributes\Group;
use PHPUnit\Framework\Attributes\Small;
use Tests\TestCase;

#[Group('stillfleet')]
#[Small]
final class PartialCharacterTest extends TestCase
{
    public function testToCharacter(): void
    {
        $partialCharacter = new PartialCharacter(['_id' => 'deadbeef']);
        $character = $partialCharacter->toCharacter();
        self::assertNull($character->_id);
    }
}
