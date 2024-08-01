<?php

declare(strict_types=1);

namespace Modules\Avatar\Tests\Feature\View\Components;

use App\Models\Campaign;
use App\Models\User;
use Modules\Avatar\View\Components\CampaignOptions;
use PHPUnit\Framework\Attributes\Small;
use Tests\TestCase;

#[Small]
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
