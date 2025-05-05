<?php

declare(strict_types=1);

namespace Modules\Avatar\Tests\Feature\Models;

use Modules\Avatar\Enums\Era;
use Modules\Avatar\Models\Campaign;
use PHPUnit\Framework\Attributes\Group;
use PHPUnit\Framework\Attributes\Medium;
use Tests\TestCase;

#[Group('avatar')]
#[Medium]
final class CampaignTest extends TestCase
{
    public function testCampaignNoEra(): void
    {
        /** @var Campaign */
        $campaign = Campaign::factory()->make(['options' => []]);
        self::assertNull($campaign->era());
    }

    public function testCampaignWithEra(): void
    {
        /** @var Campaign */
        $campaign = Campaign::factory()->state([
            'options' => ['era' => 'hundred-year-war'],
            'system' => 'avatar',
        ])->make();
        self::assertSame(Era::HundredYearWar, $campaign->era());
    }
}
