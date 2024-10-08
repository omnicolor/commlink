<?php

declare(strict_types=1);

namespace Modules\Capers\Tests\Feature\Rolls;

use App\Exceptions\SlackException;
use App\Models\Campaign;
use App\Models\Channel;
use Illuminate\Foundation\Testing\WithFaker;
use Modules\Capers\Models\StandardDeck;
use Modules\Capers\Rolls\Shuffle;
use PHPUnit\Framework\Attributes\Group;
use PHPUnit\Framework\Attributes\Medium;
use Tests\TestCase;

use function sprintf;

#[Group('capers')]
#[Medium]
final class ShuffleTest extends TestCase
{
    use WithFaker;

    /**
     * Test trying to shuffle a deck in a Slack channel with no campaign.
     */
    #[Group('slack')]
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
     */
    #[Group('discord')]
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
     */
    #[Group('slack')]
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
     */
    #[Group('discord')]
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
            sprintf('%s shuffled their deck', $channel->username),
            $response
        );

        $deck = StandardDeck::findForCampaignAndPlayer(
            $campaign,
            $channel->username
        );
        self::assertCount(54, $deck);
    }

    #[Group('irc')]
    public function testErrorForIrc(): void
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
            ->forIrc();
        self::assertSame(
            'Capers-style card decks are only available for Capers campaigns.',
            $response
        );
    }

    #[Group('irc')]
    public function testForIrc(): void
    {
        /** @var Campaign */
        $campaign = Campaign::factory()->create(['system' => 'capers']);

        /** @var Channel */
        $channel = Channel::factory()->make([
            'campaign_id' => $campaign,
            'system' => 'capers',
            'type' => Channel::TYPE_IRC,
        ]);
        $channel->username = $this->faker->name;

        $deck = new StandardDeck();
        $deck->campaign_id = $campaign->id;
        $deck->character_id = $channel->username;
        $deck->shuffle();
        $deck->draw(10);
        $deck->save();

        $response = (new Shuffle('shuffle', $channel->username, $channel))
            ->forIrc();

        self::assertStringStartsWith(
            sprintf('%s shuffled their deck', $channel->username),
            $response
        );
    }
}
