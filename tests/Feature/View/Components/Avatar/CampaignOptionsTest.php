<?php

declare(strict_types=1);

namespace Tests\Feature\View\Components\Avatar;

use App\Models\Campaign;
use App\Models\User;
use App\View\Components\Avatar\CampaignOptions;
use Tests\TestCase;

/**
 * @small
 */
final class CampaignOptionsTest extends TestCase
{
    public function testCampaignOptionsView(): void
    {
        $user = User::factory()->create();
        $campaign = Campaign::factory()->make([
            'registered_by' => $user,
            'system' => 'avatar',
        ]);
        $this->component(CampaignOptions::class, ['campaign' => $campaign])
            ->assertSee('Era');
    }
}
