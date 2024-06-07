<?php

declare(strict_types=1);

namespace Tests\Feature\View\Components;

use App\Models\Campaign;
use App\Models\User;
use App\View\Components\CampaignOptions;
use PHPUnit\Framework\Attributes\Medium;
use Tests\TestCase;

#[Medium]
final class CampaignOptionsTest extends TestCase
{
    public function testGenericCampaign(): void
    {
        $user = User::factory()->create();
        $campaign = Campaign::factory()->make([
            'registered_by' => $user,
            'system' => 'dnd5e',
        ]);
        self::component(CampaignOptions::class, ['campaign' => $campaign])
            ->assertDontSee('Start date');
    }

    public function testShadowrun5EOptions(): void
    {
        $user = User::factory()->create();
        $campaign = Campaign::factory()->make([
            'registered_by' => $user,
            'system' => 'shadowrun5e',
        ]);
        self::component(CampaignOptions::class, ['campaign' => $campaign])
            ->assertSee('Start date');
    }
}
