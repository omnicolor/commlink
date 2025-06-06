<?php

declare(strict_types=1);

namespace Modules\Capers\Tests\Feature\Rolls;

use App\Enums\ChannelType;
use App\Models\Campaign;
use App\Models\Channel;
use App\Models\ChatUser;
use App\Models\User;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Support\Str;
use Modules\Capers\Models\StandardDeck;
use Modules\Capers\Rolls\ShuffleAll;
use Omnicolor\Slack\Attachment;
use Omnicolor\Slack\Exceptions\SlackException;
use PHPUnit\Framework\Attributes\Group;
use PHPUnit\Framework\Attributes\Medium;
use Tests\TestCase;

#[Group('capers')]
#[Medium]
final class ShuffleAllTest extends TestCase
{
    use WithFaker;

    /**
     * Test trying to request shuffle all without an attached campaign.
     */
    #[Group('slack')]
    public function testShuffleWithNoCampaign(): void
    {
        $channel = Channel::factory()->make(['system' => 'capers']);
        $channel->username = $this->faker->name;

        self::expectException(SlackException::class);
        self::expectExceptionMessage(
            'Decks for Capers require a linked Commlink campaign.',
        );
        (new ShuffleAll('shuffleAll', $channel->username, $channel))
            ->forSlack();
    }

    /**
     * Test trying to shuffle Capers decks somehow from a non-Capers campaign.
     */
    #[Group('discord')]
    public function testShuffleFromOtherSystem(): void
    {
        $campaign = Campaign::factory()->create(['system' => 'shadowrun5e']);

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
     */
    #[Group('slack')]
    public function testShuffleAllNotGm(): void
    {
        $campaign = Campaign::factory()->create(['system' => 'capers']);

        $channel = Channel::factory()->make([
            'campaign_id' => $campaign,
            'system' => 'capers',
        ]);
        $channel->username = $this->faker->name;

        self::expectException(SlackException::class);
        self::expectExceptionMessage(
            'You must be the game\'s GM to shuffle all decks',
        );
        (new ShuffleAll('shuffleAll', $channel->username, $channel))
            ->forSlack();
    }

    /**
     * Test trying to shuffle all as the game's GM from Slack.
     */
    #[Group('slack')]
    public function testShuffleAllWithNoDecks(): void
    {
        $user = User::factory()->create();

        $campaign = Campaign::factory()->create([
            'gm' => $user->id,
            'system' => 'capers',
        ]);

        $channel = Channel::factory()->make([
            'campaign_id' => $campaign,
            'system' => 'capers',
            'type' => ChannelType::Slack,
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
            ->forSlack()
            ->jsonSerialize();

        self::assertArrayHasKey('attachments', $response);
        self::assertSame(
            [
                'color' => Attachment::COLOR_INFO,
                'text' => '',
                'title' => 'The Gamemaster shuffled all decks',
            ],
            $response['attachments'][0],
        );

        // Make sure no decks were created by shuffling.
        self::assertDatabaseMissing('decks', ['campaign_id' => $campaign->id]);
    }

    /**
     * Test trying to shuffle all as the game's GM in Discord.
     */
    #[Group('discord')]
    public function testShuffleAllFromDiscord(): void
    {
        $user = User::factory()->create();

        $campaign = Campaign::factory()->create([
            'gm' => $user->id,
            'system' => 'capers',
        ]);

        $channel = Channel::factory()->make([
            'campaign_id' => $campaign,
            'system' => 'capers',
            'type' => ChannelType::Discord,
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
            $response,
        );

        $deck = StandardDeck::findForCampaignAndPlayer(
            $campaign,
            $deck->character_id,
        );
        self::assertCount(54, $deck);
    }

    #[Group('irc')]
    public function testNotGmInIrc(): void
    {
        $campaign = Campaign::factory()->create(['system' => 'capers']);

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

    #[Group('irc')]
    public function testForIrc(): void
    {
        $user = User::factory()->create();

        $campaign = Campaign::factory()->create([
            'gm' => $user->id,
            'system' => 'capers',
        ]);

        $channel = Channel::factory()->make([
            'campaign_id' => $campaign,
            'system' => 'capers',
            'type' => ChannelType::Irc,
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
            $response,
        );
    }
}
