<?php

declare(strict_types=1);

namespace Tests\Feature\View\Components;

use App\Models\Campaign;
use App\Models\User;
use App\View\Components\CampaignOptions;
use Tests\TestCase;

/**
 * @medium
 */
final class CampaignOptionsTest extends TestCase
{
    public function testGenericCampaign(): void
    {
        $user = User::factory()->create();
        $campaign = Campaign::factory()->make([
            'registered_by' => $user,
            'system' => 'dnd5e',
        ]);
        $this->component(CampaignOptions::class, ['campaign' => $campaign])
            ->assertDontSee('Start date');
    }

    public function testShadowrun5EOptions(): void
    {
        $user = User::factory()->create();
        $campaign = Campaign::factory()->make([
            'registered_by' => $user,
            'system' => 'shadowrun5e',
        ]);
        $this->component(CampaignOptions::class, ['campaign' => $campaign])
            ->assertSee('Start date');
    }
}
