<?php

declare(strict_types=1);

namespace Modules\Shadowrun6e\Tests\Feature\Models;

use Modules\Shadowrun6e\Models\Program;
use PHPUnit\Framework\Attributes\Group;
use PHPUnit\Framework\Attributes\Small;
use Tests\TestCase;

#[Group('shadowrun')]
#[Group('shadowrun6e')]
#[Small]
final class ProgramTest extends TestCase
{
    public function testToString(): void
    {
        $program = Program::findOrFail('baby-monitor');
        self::assertSame('Baby Monitor', (string)$program);
    }
}
