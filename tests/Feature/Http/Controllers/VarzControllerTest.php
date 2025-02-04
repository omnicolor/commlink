<?php

declare(strict_types=1);

namespace Tests\Feature\Http\Controllers;

use App\Models\Campaign;
use App\Models\CampaignInvitation;
use App\Models\Channel;
use App\Models\Character;
use App\Models\ChatCharacter;
use App\Models\Deck;
use App\Models\Initiative;
use Modules\Shadowrun5e\Models\Character as Runner;
use Override;
use PHPUnit\Framework\Attributes\Medium;
use Tests\TestCase;

use function config;
use function route;

/**
 * Tests for the Varz controller.
 */
#[Medium]
final class VarzControllerTest extends TestCase
{
    #[Override]
    public function setUp(): void
    {
        parent::setUp();

        ChatCharacter::truncate();
        Channel::truncate();
        Deck::truncate();
        Initiative::truncate();
        CampaignInvitation::truncate();
        Campaign::truncate();
        Character::truncate();
    }

    /**
     * Test getting system metrics.
     */
    public function testVarzEmpty(): void
    {
        $systems = [];
        foreach (config('commlink.systems') as $code => $name) {
            $systems[$code] = [
                'name' => $name,
                'data' => [
                    'campaigns' => 0,
                    'player_characters' => 0,
                ],
            ];
        }

        self::getJson(route('varz'))
            ->assertOk()
            ->assertJson([
                'campaigns_total' => 0,
                'channels' => [
                    'discord' => 0,
                    'slack' => 0,
                ],
                'characters_total' => 0,
                'systems' => $systems,
            ]);
    }

    /**
     * Test getting system metrics after creating some things.
     */
    public function testVarz(): void
    {
        $systems = [];
        foreach (config('commlink.systems') as $code => $name) {
            $systems[$code] = [
                'name' => $name,
                'data' => [
                    'campaigns' => 0,
                    'player_characters' => 0,
                ],
            ];
        }

        Runner::factory()->create();
        Campaign::factory()->create([
            'system' => 'shadowrun5e',
        ]);
        Channel::factory()->create([
            'type' => Channel::TYPE_DISCORD,
        ]);
        Channel::factory()->create([
            'type' => Channel::TYPE_SLACK,
        ]);

        $systems['shadowrun5e']['data'] = [
            'campaigns' => 1,
            'player_characters' => 1,
        ];

        self::getJson(route('varz'))
            ->assertOk()
            ->assertJson([
                'campaigns_total' => 1,
                'channels' => [
                    'discord' => 1,
                    'slack' => 1,
                ],
                'characters_total' => 1,
                'systems' => $systems,
            ]);
    }
}
