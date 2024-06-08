<?php

declare(strict_types=1);

namespace Tests\Feature\Models\Shadowrun5e;

use App\Models\Shadowrun5e\Campaign;
use App\Models\Shadowrun5e\Character;
use App\Models\Shadowrun5e\Contact;
use App\Models\Shadowrun5e\ContactArray;
use PHPUnit\Framework\Attributes\Medium;
use Tests\TestCase;

/**
 * @group shadowrun
 * @group shadowrun5e
 */
#[Medium]
final class CampaignTest extends TestCase
{
    /**
     * Some tests (especially failed tests) don't clean up after themselves.
     *
     * This creates a new campaign, and makes sure that campaign ID doesn't have
     * any residual characters tied to it.
     */
    protected function createCleanCampaign(): Campaign
    {
        /** @var Campaign */
        $campaign = Campaign::factory()->create();
        // @phpstan-ignore-next-line
        $campaign->characters()->each(function (Character $character, $key): void {
            $character->delete();
        });
        return $campaign;
    }

    public function testGetContactsNoCharacters(): void
    {
        $campaign = $this->createCleanCampaign();
        self::assertCount(0, $campaign->contacts());
    }

    public function testGetContactsNotShared(): void
    {
        $campaign = $this->createCleanCampaign();

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

        /** @var Contact */
        $contact = $contacts['Dodger'];
        self::assertCount(1, $contact->characters);

        /** @var Contact */
        $contact = $contacts['Fastjack'];
        self::assertCount(1, $contact->characters);

        $character1->delete();
        $character2->delete();
    }

    public function testGetContactsShared(): void
    {
        $campaign = $this->createCleanCampaign();
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

        /** @var ContactArray */
        $contacts = $campaign->contacts();
        self::assertCount(1, $contacts);

        /** @var Contact */
        $contact = $contacts['Dodger'];

        self::assertCount(2, $contact->characters);

        $character1->delete();
        $character2->delete();
    }
}
