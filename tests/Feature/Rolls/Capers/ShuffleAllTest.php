<?php

declare(strict_types=1);

namespace Tests\Feature\Rolls\Capers;

use App\Exceptions\SlackException;
use App\Models\Campaign;
use App\Models\Capers\StandardDeck;
use App\Models\Channel;
use App\Models\ChatUser;
use App\Models\User;
use App\Rolls\Capers\ShuffleAll;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Support\Str;
use Tests\TestCase;

/**
 * Tests for the GM requesting everyone to shuffle their decks.
 * @group capers
 * @medium
 */
final class ShuffleAllTest extends TestCase
{
    use RefreshDatabase;
    use WithFaker;

    /**
     * Test trying to request shuffle all without an attached campaign.
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
        (new ShuffleAll('shuffleAll', $channel->username, $channel))
            ->forSlack();
    }

    /**
     * Test trying to shuffle Capers decks somehow from a non-Capers campaign.
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

        $response = (new ShuffleAll('shuffleAll', $channel->username, $channel))
            ->forDiscord();
        self::assertSame(
            'Capers-style card decks are only available for Capers campaigns.',
            $response
        );
    }

    /**
     * Test trying to request all users shuffle as a non-GM.
     * @group slack
     * @test
     */
    public function testShuffleAllNotGm(): void
    {
        /** @var Campaign */
        $campaign = Campaign::factory()->create(['system' => 'capers']);

        /** @var Channel */
        $channel = Channel::factory()->make([
            'campaign_id' => $campaign,
            'system' => 'capers',
        ]);
        $channel->username = $this->faker->name;

        self::expectException(SlackException::class);
        self::expectExceptionMessage(
            'You must be the game\'s GM to shuffle all decks'
        );
        (new ShuffleAll('shuffleAll', $channel->username, $channel))
            ->forSlack();
    }

    /**
     * Test trying to shuffle all as the game's GM from Slack.
     * @group slack
     * @test
     */
    public function testShuffleAllWithNoDecks(): void
    {
        /** @var User */
        $user = User::factory()->create();

        /** @var Campaign */
        $campaign = Campaign::factory()->create([
            'gm' => $user->id,
            'system' => 'capers',
        ]);

        /** @var Channel */
        $channel = Channel::factory()->make([
            'campaign_id' => $campaign,
            'system' => 'capers',
            'type' => Channel::TYPE_SLACK,
        ]);
        $channel->username = $this->faker->name;
        $channel->user = 'U' . Str::random(10);

        ChatUser::factory()->create([
            'remote_user_id' => $channel->user,
            'server_id' => $channel->server_id,
            'server_type' => ChatUser::TYPE_SLACK,
            'user_id' => $user,
            'verified' => true,
        ]);

        $response = (new ShuffleAll('shuffleAll', $channel->username, $channel))
            ->forSlack();
        self::assertSame(
            'The Gamemaster shuffled all decks',
            json_decode((string)$response)->attachments[0]->title
        );

        // Make sure no decks were created by shuffling.
        self::assertDatabaseMissing('decks', ['campaign_id' => $campaign->id]);
    }

    /**
     * Test trying to shuffle all as the game's GM in Discord.
     * @group discord
     * @test
     */
    public function testShuffleAllFromDiscord(): void
    {
        /** @var User */
        $user = User::factory()->create();

        /** @var Campaign */
        $campaign = Campaign::factory()->create([
            'gm' => $user->id,
            'system' => 'capers',
        ]);

        /** @var Channel */
        $channel = Channel::factory()->make([
            'campaign_id' => $campaign,
            'system' => 'capers',
            'type' => Channel::TYPE_DISCORD,
        ]);
        $channel->username = $this->faker->name;
        $channel->user = 'U' . Str::random(10);

        ChatUser::factory()->create([
            'remote_user_id' => $channel->user,
            'server_id' => $channel->server_id,
            'server_type' => ChatUser::TYPE_DISCORD,
            'user_id' => $user,
            'verified' => true,
        ]);

        $deck = new StandardDeck();
        $deck->campaign_id = $campaign->id;
        $deck->character_id = $this->faker->name;
        $deck->shuffle();
        $deck->draw(10);
        $deck->save();

        $response = (new ShuffleAll('shuffleAll', $channel->username, $channel))
            ->forDiscord();
        self::assertSame(
            'The Gamemaster shuffled all decks',
            $response
        );

        $deck = StandardDeck::findForCampaignAndPlayer(
            $campaign,
            $deck->character_id
        );
        self::assertCount(54, $deck);
    }

    /**
     * @group irc
     */
    public function testNotGmInIrc(): void
    {
        /** @var Campaign */
        $campaign = Campaign::factory()->create(['system' => 'capers']);

        /** @var Channel */
        $channel = Channel::factory()->make([
            'campaign_id' => $campaign,
            'system' => 'capers',
        ]);
        $channel->username = $this->faker->name;

        $response = (new ShuffleAll('shuffleAll', $channel->username, $channel))
            ->forIrc();
        self::assertSame(
            'You must be the game\'s GM to shuffle all decks',
            $response,
        );
    }

    /**
     * @group irc
     */
    public function testForIrc(): void
    {
        /** @var User */
        $user = User::factory()->create();

        /** @var Campaign */
        $campaign = Campaign::factory()->create([
            'gm' => $user->id,
            'system' => 'capers',
        ]);

        /** @var Channel */
        $channel = Channel::factory()->make([
            'campaign_id' => $campaign,
            'system' => 'capers',
            'type' => Channel::TYPE_IRC,
        ]);
        $channel->username = $this->faker->name;
        $channel->user = 'U' . Str::random(10);

        ChatUser::factory()->create([
            'remote_user_id' => $channel->user,
            'server_id' => $channel->server_id,
            'server_type' => ChatUser::TYPE_IRC,
            'user_id' => $user,
            'verified' => true,
        ]);

        $deck = new StandardDeck();
        $deck->campaign_id = $campaign->id;
        $deck->character_id = $this->faker->name;
        $deck->shuffle();
        $deck->draw(10);
        $deck->save();

        $response = (new ShuffleAll('shuffleAll', $channel->username, $channel))
            ->forIrc();
        self::assertSame(
            'The Gamemaster shuffled all decks',
            $response
        );
    }
}
