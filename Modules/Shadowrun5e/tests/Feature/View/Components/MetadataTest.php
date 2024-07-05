<?php

declare(strict_types=1);

namespace Modules\Shadowrun5e\Tests\Feature\View\Components;

use Modules\Shadowrun5e\Models\Character;
use Modules\Shadowrun5e\View\Components\Metadata;
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
