<?php

declare(strict_types=1);

namespace Tests\Feature\View\Components\Shadowrun5e;

use App\Models\Shadowrun5e\Character;
use App\View\Components\Shadowrun5e\Metadata;
use PHPUnit\Framework\Attributes\Group;
use PHPUnit\Framework\Attributes\Small;
use Tests\TestCase;

#[Group('shadowrun')]
#[Group('shadowrun5e')]
#[Small]
final class MetadataTest extends TestCase
{
    public function testMetadata(): void
    {
        $this->component(
            Metadata::class,
            [
                'character' => new Character([
                    'handle' => 'The Smiling Bandit',
                    'realName' => 'Ha ha ha',
                ]),
            ]
        )
            ->assertSee('&yen;0', false)
            ->assertSee('The Smiling Bandit')
            ->assertSee('Ha ha ha');
    }
}
