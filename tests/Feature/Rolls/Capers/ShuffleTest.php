<?php

declare(strict_types=1);

namespace Tests\Feature\Rolls\Capers;

use App\Exceptions\SlackException;
use App\Models\Campaign;
use App\Models\Capers\StandardDeck;
use App\Models\Channel;
use App\Rolls\Capers\Shuffle;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

/**
 * Tests for shuffling a player's deck in the Capers system.
 * @group capers
 * @medium
 */
final class ShuffleTest extends TestCase
{
    use WithFaker;

    /**
     * Test trying to shuffle a deck in a Slack channel with no campaign.
     * @group slack
     * @test
     */
    public function testShuffleWithNoCampaign(): void
    {
        /** @var Channel */
        $channel = Channel::factory()->make(['system' => 'capers']);
        $channel->username = $this->faker->name;

        self::expectException(SlackException::class);
        self::expectExceptionMessage(
            'Decks for Capers require a linked Commlink campaign.'
        );
        (new Shuffle('shuffle', $channel->username, $channel))->forSlack();
    }

    /**
     * Test trying to shuffle a Capers deck somehow from a non-Capers campaign.
     * @group discord
     * @test
     */
    public function testShuffleFromOtherSystem(): void
    {
        /** @var Campaign */
        $campaign = Campaign::factory()->create(['system' => 'shadowrun5e']);

        /** @var Channel */
        $channel = Channel::factory()->make([
            'campaign_id' => $campaign,
            'system' => 'capers',
        ]);
        $channel->username = $this->faker->name;

        $response = (new Shuffle('shuffle', $channel->username, $channel))
            ->forDiscord();
        self::assertSame(
            'Capers-style card decks are only available for Capers campaigns.',
            $response
        );
    }

    /**
     * Test trying to shuffle a Capers deck for the first time.
     * @group slack
     * @test
     */
    public function testShuffleFirstTime(): void
    {
        /** @var Campaign */
        $campaign = Campaign::factory()->create(['system' => 'capers']);

        /** @var Channel */
        $channel = Channel::factory()->make([
            'campaign_id' => $campaign,
            'system' => 'capers',
        ]);
        $channel->username = $this->faker->name;

        self::assertDatabaseMissing(
            'decks',
            [
                'campaign_id' => $campaign->id,
                'character_id' => $channel->username,
            ]
        );
        $response = (new Shuffle('shuffle', $channel->username, $channel))
            ->forSlack();
        self::assertDatabaseHas(
            'decks',
            [
                'campaign_id' => $campaign->id,
                'character_id' => $channel->username,
            ]
        );

        self::assertStringStartsWith(
            sprintf('%s shuffled their deck', $channel->username),
            json_decode((string)$response)->attachments[0]->title
        );
    }

    /**
     * Test trying to shuffle a Capers deck if the character already has done
     * so.
     * @group discord
     * @test
     */
    public function testShuffleAgain(): void
    {
        /** @var Campaign */
        $campaign = Campaign::factory()->create(['system' => 'capers']);

        /** @var Channel */
        $channel = Channel::factory()->make([
            'campaign_id' => $campaign,
            'system' => 'capers',
        ]);
        $channel->username = $this->faker->name;

        $deck = new StandardDeck();
        $deck->campaign_id = $campaign->id;
        $deck->character_id = $channel->username;
        $deck->shuffle();
        $deck->draw(10);
        $deck->save();

        $response = (new Shuffle('shuffle', $channel->username, $channel))
            ->forDiscord();

        self::assertStringStartsWith(
            \sprintf('%s shuffled their deck', $channel->username),
            $response
        );

        $deck = StandardDeck::findForCampaignAndPlayer(
            $campaign,
            $channel->username
        );
        self::assertCount(54, $deck);
    }
}
