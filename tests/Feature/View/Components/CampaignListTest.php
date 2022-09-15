<?php

declare(strict_types=1);

namespace Tests\Feature\View\Components;

use App\Models\Campaign;
use App\Models\User;
use App\View\Components\CampaignList;
use Illuminate\Database\Eloquent\Collection;
use Tests\TestCase;

/**
 * @medium
 */
final class CampaignListTest extends TestCase
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
        self::assertCount(0, $list->playing);
    }

    /**
     * Test filtering out *playing* a game if the user also registered it.
     * @test
     */
    public function testPlayingFiltered(): void
    {
        /** @var User */
        $user = User::factory()->create();
        $commonCampaign = Campaign::factory()->make([
            'name' => 'GMing and registered by',
            'registered_by' => $user,
        ]);
        $playedCampaigns = new Collection([$commonCampaign]);
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
            new Collection([]),
            $registeredCampaigns,
            $playedCampaigns,
            $user
        );
        self::assertCount(0, $list->gmed);
        self::assertCount(2, $list->registered);
        self::assertCount(0, $list->playing);
    }
}
