<?php

declare(strict_types=1);

namespace Tests\Feature\View\Components;

use App\Models\Campaign;
use App\Models\User;
use App\View\Components\CampaignList;
use Illuminate\Database\Eloquent\Collection;
use PHPUnit\Framework\Attributes\Medium;
use Tests\TestCase;

#[Medium]
final class CampaignListTest extends TestCase
{
    /**
     * Test the constructor when the list of GMed and registered campaigns are
     * different.
     */
    public function testConstructorDifferentRegisteredBy(): void
    {
        /** @var User */
        $user = User::factory()->make();

        /** @var Collection<int|string, Campaign> */
        $gmCampaigns = new Collection([
            Campaign::factory()->make([
                'gm' => $user,
            ]),
        ]);

        /** @var Collection<int|string, Campaign> */
        $registeredCampaigns = new Collection([
            Campaign::factory()->make([
                'registered_by' => $user,
            ]),
        ]);

        /** @var Collection<int|string, Campaign> */
        $playingCampaigns = new Collection([]);

        $list = new CampaignList(
            $gmCampaigns,
            $registeredCampaigns,
            $playingCampaigns,
            $user
        );
        self::assertCount(1, $list->gmed);
        self::assertCount(1, $list->registered);
    }

    /**
     * Test the constructor when the list of GMed and registered campaigns are
     * the same.
     */
    public function testConstructorSameCampaigns(): void
    {
        /** @var User */
        $user = User::factory()->create();

        /** @var Campaign */
        $commonCampaign = Campaign::factory()->make([
            'name' => 'GMing and registered by',
            'gm' => $user,
            'registered_by' => $user,
        ]);

        /** @var Collection<int|string, Campaign> */
        $gmCampaigns = new Collection([$commonCampaign]);

        /** @var Collection<int|string, Campaign> */
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

        /** @var Collection<int|string, Campaign> */
        $playingCampaigns = new Collection([]);

        $list = new CampaignList(
            $gmCampaigns,
            $registeredCampaigns,
            $playingCampaigns,
            $user
        );
        self::assertCount(1, $list->gmed);
        self::assertCount(1, $list->registered);
        self::assertCount(0, $list->playing);
    }

    /**
     * Test filtering out *playing* a game if the user also registered it.
     */
    public function testPlayingFiltered(): void
    {
        /** @var User */
        $user = User::factory()->create();

        /** @var Collection<int|string, Campaign> */
        $gmedCampaigns = new Collection([]);

        /** @var Campaign */
        $commonCampaign = Campaign::factory()->make([
            'name' => 'GMing and registered by',
            'registered_by' => $user,
        ]);

        /** @var Collection<int|string, Campaign> */
        $playedCampaigns = new Collection([$commonCampaign]);

        /** @var Collection<int|string, Campaign> */
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
            $gmedCampaigns,
            $registeredCampaigns,
            $playedCampaigns,
            $user
        );

        self::assertCount(0, $list->gmed);
        self::assertCount(2, $list->registered);
        self::assertCount(0, $list->playing);
    }
}
