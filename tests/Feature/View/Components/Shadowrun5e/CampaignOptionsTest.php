<?php

declare(strict_types=1);

namespace Tests\Feature\View\Components\Shadowrun5e;

use App\Models\Campaign;
use App\Models\User;
use App\View\Components\Shadowrun5e\CampaignOptions;
use Tests\TestCase;

/**
 * @small
 */
final class CampaignOptionsTest extends TestCase
{
    public function testShadowrunCampaignOptionsView(): void
    {
        $user = User::factory()->create();
        $campaign = Campaign::factory()->make([
            'registered_by' => $user,
            'system' => 'shadowrun5e',
        ]);
        $this->component(CampaignOptions::class, ['campaign' => $campaign])
            ->assertSee('Start date');
    }

    public function testShadowrunCampaignOptionsViewHasBooks(): void
    {
        $options = new CampaignOptions();
        self::assertCount(47, $options->books);
    }
}
