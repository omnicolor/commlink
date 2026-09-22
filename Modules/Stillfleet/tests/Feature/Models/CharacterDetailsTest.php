<?php

declare(strict_types=1);

namespace Modules\Stillfleet\Tests\Feature\Models;

use Modules\Stillfleet\Models\CharacterDetails;
use PHPUnit\Framework\Attributes\Group;
use PHPUnit\Framework\Attributes\Small;
use PHPUnit\Framework\TestCase;

#[Group('stillfleet')]
#[Small]
final class CharacterDetailsTest extends TestCase
{
    public function testMakeWithNull(): void
    {
        $details = CharacterDetails::make(null);
        self::assertEquals(new CharacterDetails(), $details);
    }

    public function testMakeWithArray(): void
    {
        $details = CharacterDetails::make([
            'appearance' => 'pale skin',
            'family' => 'Nuclear (as in radioactive)',
        ]);

        self::assertNull($details->company);
        self::assertSame('pale skin', $details->appearance);
        self::assertSame('Nuclear (as in radioactive)', $details->family);
    }

    public function testToArrayEmpty(): void
    {
        $details = new CharacterDetails();
        self::assertSame([], $details->toArray());
    }

    public function testToArray(): void
    {
        $details = CharacterDetails::make([
            'crew_nickname' => 'Test',
            'refactor' => '💩',
        ]);
        $details->origin = 'Spin';

        self::assertSame(
            [
                'crew_nickname' => 'Test',
                'origin' => 'Spin',
                'refactor' => '💩',
            ],
            $details->toArray(),
        );
    }
}
