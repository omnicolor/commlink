<?php

declare(strict_types=1);

namespace Modules\Shadowrun5e\Tests\Feature\View\Components;

use App\Models\Campaign;
use App\Models\User;
use Modules\Shadowrun5e\View\Components\CampaignOptions;
use PHPUnit\Framework\Attributes\Group;
use PHPUnit\Framework\Attributes\Small;
use Tests\TestCase;

#[Group('shadowrun')]
#[Group('shadowrun5e')]
#[Small]
final class CampaignOptionsTest extends TestCase
{
    public function testShadowrunCampaignOptionsView(): void
    {
        $user = User::factory()->create();
        $campaign = Campaign::factory()->make([
            'registered_by' => $user,
            'system' => 'shadowrun5e',
        ]);
        self::component(CampaignOptions::class, ['campaign' => $campaign])
            ->assertSee('Start date');
    }

    public function testShadowrunCampaignOptionsViewHasBooks(): void
    {
        $options = new CampaignOptions();
        self::assertCount(47, $options->books);
    }
}
