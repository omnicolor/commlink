<?php

declare(strict_types=1);

namespace Tests\Feature\View\Components;

use App\Models\Campaign;
use App\Models\User;
use App\View\Components\CampaignList;
use Illuminate\Database\Eloquent\Collection;

/**
 * @medium
 */
final class CampaignListTest extends \Tests\TestCase
{
    /**
     * Test the constructor when the list of GMed and registered campaigns are
     * different.
     * @test
     */
    public function testConstructorDifferentRegisteredBy(): void
    {
        /** @var User */
        $user = User::factory()->make();
        $gmCampaigns = new Collection([
            Campaign::factory()->make([
                'gm' => $user,
            ]),
        ]);
        $registeredCampaigns = new Collection([
            Campaign::factory()->make([
                'registered_by' => $user,
            ]),
        ]);
        $list = new CampaignList(
            $gmCampaigns,
            $registeredCampaigns,
            new Collection([]),
            $user
        );
        self::assertCount(1, $list->gmed);
        self::assertCount(1, $list->registered);
    }

    /**
     * Test the constructor when the list of GMed and registered campaigns are
     * the same.
     * @test
     */
    public function testConstructorSameCampaigns(): void
    {
        /** @var User */
        $user = User::factory()->create();
        $commonCampaign = Campaign::factory()->make([
            'name' => 'GMing and registered by',
            'gm' => $user,
            'registered_by' => $user,
        ]);
        $gmCampaigns = new Collection([$commonCampaign]);
        $registeredCampaigns = new Collection([
            $commonCampaign,
            Campaign::factory()->make([
                'name' => 'Registered, but not GMing',
                'gm' => null,
                'registered_by' => $user,
            ]),
            // For kicks, add a campaign that shouldn't be here.
            Campaign::factory()->make([
                'name' => 'Neither GMing nor registered',
            ]),
        ]);
        $list = new CampaignList(
            $gmCampaigns,
            $registeredCampaigns,
            new Collection([]),
            $user
        );
        self::assertCount(1, $list->gmed);
        self::assertCount(1, $list->registered);
    }
}
