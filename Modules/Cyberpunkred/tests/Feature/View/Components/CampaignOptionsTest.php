<?php

declare(strict_types=1);

namespace Modules\Cyberpunkred\Tests\Feature\View\Components;

use App\Models\Campaign;
use App\Models\User;
use App\View\Components\Cyberpunkred\CampaignOptions;
use PHPUnit\Framework\Attributes\Group;
use PHPUnit\Framework\Attributes\Small;
use Tests\TestCase;

#[Group('cyberpunkred')]
#[Small]
final class CampaignOptionsTest extends TestCase
{
    public function testCampaignOptionsView(): void
    {
        $user = User::factory()->create();
        $campaign = Campaign::factory()->make([
            'registered_by' => $user,
            'system' => 'cyberpunkred',
        ]);
        $this->component(CampaignOptions::class, ['campaign' => $campaign])
            ->assertSee('Tarot');
    }
}
