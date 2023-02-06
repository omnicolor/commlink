<?php

declare(strict_types=1);

namespace Tests\Feature\Models\Shadowrun5e;

use App\Models\Shadowrun5e\Campaign;
use App\Models\Shadowrun5e\Character;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

/**
 * @medium
 */
final class CampaignTest extends TestCase
{
    use RefreshDatabase;

    public function testGetContactsNoCharacters(): void
    {
        $campaign = Campaign::factory()->create();
        // Some tests don't clean up after themselves...
        $campaign->characters()->each(function (Character $character, $key) {
            $character->delete();
        });
        self::assertCount(0, $campaign->contacts());
    }

    public function testGetContactsNotShared(): void
    {
        $campaign = Campaign::factory()->create();
        // Some tests don't clean up after themselves...
        $campaign->characters()->each(function (Character $character, $key) {
            $character->delete();
        });
        $character1 = Character::factory()->create([
            'campaign_id' => $campaign->id,
            'contacts' => [
                [
                    'name' => 'Dodger',
                    'archetype' => 'Decker',
                    'connection' => 6,
                    'loyalty' => 1,
                ],
            ],
        ]);
        $character2 = Character::factory()->create([
            'campaign_id' => $campaign->id,
            'contacts' => [
                [
                    'name' => 'Fastjack',
                    'archetype' => 'Decker',
                    'connection' => 5,
                    'loyalty' => 2,
                ],
            ],
        ]);

        $contacts = $campaign->contacts();
        self::assertCount(2, $contacts);
        self::assertCount(1, $contacts['Dodger']->characters);
        self::assertCount(1, $contacts['Fastjack']->characters);
        $character1->delete();
        $character2->delete();
    }

    public function testGetContactsShared(): void
    {
        $campaign = Campaign::factory()->create();
        // Some tests don't clean up after themselves...
        $campaign->characters()->each(function (Character $character, $key) {
            $character->delete();
        });
        $character1 = Character::factory()->create([
            'campaign_id' => $campaign->id,
            'contacts' => [
                [
                    'name' => 'Dodger',
                    'archetype' => 'Decker',
                    'connection' => 6,
                    'loyalty' => 1,
                ],
            ],
        ]);
        $character2 = Character::factory()->create([
            'campaign_id' => $campaign->id,
            'contacts' => [
                [
                    'name' => 'Dodger',
                    'archetype' => 'Decker',
                    'connection' => 6,
                    'loyalty' => 1,
                ],
            ],
        ]);

        $contacts = $campaign->contacts();
        self::assertCount(1, $contacts);
        self::assertCount(2, $contacts['Dodger']->characters);
        $character1->delete();
        $character2->delete();
    }
}
