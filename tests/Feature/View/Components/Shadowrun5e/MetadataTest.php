<?php

declare(strict_types=1);

namespace Tests\Feature\View\Components\Shadowrun5e;

use App\Models\Shadowrun5E\Character;
use App\View\Components\Shadowrun5e\Metadata;

/**
 * @small
 */
final class MetadataTest extends \Tests\TestCase
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
