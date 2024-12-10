<?php

declare(strict_types=1);

namespace Modules\Root\Tests\Feature\Models;

use Modules\Root\Models\Nature;
use PHPUnit\Framework\Attributes\Group;
use PHPUnit\Framework\Attributes\Small;
use Tests\TestCase;

#[Group('root')]
#[Small]
final class NatureTest extends TestCase
{
    public function testLoad(): void
    {
        $playbook = Nature::findOrFail('defender');
        self::assertSame('defender', $playbook->id);
        self::assertSame('Defender', (string)$playbook);
        self::assertSame(
            'Clear your exhaustion track when you defend someone who cannot '
                . 'defend themself from dire threat.',
            $playbook->description,
        );
    }
}
