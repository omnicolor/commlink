<?php

declare(strict_types=1);

namespace Tests\Feature\Rolls\Capers;

use App\Exceptions\SlackException;
use App\Models\Campaign;
use App\Models\Capers\StandardDeck;
use App\Models\Channel;
use App\Rolls\Capers\Draw;
use Illuminate\Foundation\Testing\WithFaker;
use PHPUnit\Framework\Attributes\Group;
use PHPUnit\Framework\Attributes\Medium;
use Tests\TestCase;

#[Group('capers')]
#[Medium]
final class DrawTest extends TestCase
{
    use WithFaker;

    /**
     * Test trying to draw a card in a Slack channel with no campaign.
     */
    #[Group('slack')]
    public function testDrawWithNoCampaign(): void
    {
        /** @var Channel */
        $channel = Channel::factory()->make(['system' => 'capers']);
        $channel->username = $this->faker->name;

        self::expectException(SlackException::class);
        self::expectExceptionMessage(
            'Decks for Capers require a linked Commlink campaign.'
        );
        (new Draw('draw', $channel->username, $channel))->forSlack();
    }

    /**
     * Test trying to draw a Capers card somehow from a non-Capers campaign.
     */
    #[Group('slack')]
    public function testDrawFromOtherSystem(): void
    {
        /** @var Campaign */
        $campaign = Campaign::factory()->create(['system' => 'shadowrun5e']);

        /** @var Channel */
        $channel = Channel::factory()->make([
            'campaign_id' => $campaign,
            'system' => 'capers',
        ]);
        $channel->username = $this->faker->name;

        self::expectException(SlackException::class);
        self::expectExceptionMessage(
            'Capers-style card decks are only available for Capers campaigns.'
        );
        (new Draw('draw', $channel->username, $channel))->forSlack();
    }

    /**
     * Test trying to draw a Capers card for the first time.
     */
    #[Group('slack')]
    public function testDrawFirstTime(): void
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
        $response = (new Draw('draw', $channel->username, $channel))
            ->forSlack();
        self::assertDatabaseHas(
            'decks',
            [
                'campaign_id' => $campaign->id,
                'character_id' => $channel->username,
            ]
        );

        self::assertStringStartsWith(
            sprintf('%s drew the ', $channel->username),
            json_decode((string)$response)->attachments[0]->title
        );
        self::assertSame(
            '53 cards remain',
            json_decode((string)$response)->attachments[0]->footer
        );
    }

    /**
     * Test trying to draw a Capers card if the character already has done so.
     */
    #[Group('slack')]
    public function testDrawAgain(): void
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

        $response = (new Draw('draw', $channel->username, $channel))
            ->forSlack();

        self::assertStringStartsWith(
            sprintf('%s drew the ', $channel->username),
            json_decode((string)$response)->attachments[0]->title
        );
        self::assertSame(
            '43 cards remain',
            json_decode((string)$response)->attachments[0]->footer
        );
    }

    /**
     * Test trying to draw from an empty deck.
     */
    #[Group('slack')]
    public function testDrawEmptySlack(): void
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
        $deck->draw(54);
        $deck->save();

        self::expectException(SlackException::class);
        self::expectExceptionMessage('Insufficient cards remain in deck');
        (new Draw('draw', $channel->username, $channel))->forSlack();
    }

    /**
     * Test trying to draw a card in Discord.
     */
    #[Group('discord')]
    public function testDrawDiscord(): void
    {
        /** @var Campaign */
        $campaign = Campaign::factory()->create(['system' => 'capers']);

        /** @var Channel */
        $channel = Channel::factory()->make([
            'campaign_id' => $campaign,
            'system' => 'capers',
        ]);
        $channel->username = $this->faker->name;

        $response = (new Draw('draw guns', $channel->username, $channel))
            ->forDiscord();
        self::assertStringStartsWith(
            \sprintf('%s drew the **', $channel->username),
            $response
        );
        self::assertStringContainsString('for guns', $response);
    }

    /**
     * Test trying to draw from an empty deck in Discord.
     */
    #[Group('discord')]
    public function testDrawEmptyDiscord(): void
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
        $deck->draw(54);
        $deck->save();

        $response = (new Draw('draw', $channel->username, $channel))
            ->forDiscord();
        self::assertSame('Insufficient cards remain in deck', $response);
    }

    /**
     * Test trying to draw a card in IRC.
     */
    #[Group('irc')]
    public function testDrawIrc(): void
    {
        /** @var Campaign */
        $campaign = Campaign::factory()->create(['system' => 'capers']);

        /** @var Channel */
        $channel = Channel::factory()->make([
            'campaign_id' => $campaign,
            'system' => 'capers',
        ]);
        $channel->username = $this->faker->name;

        $response = (new Draw('draw guns', $channel->username, $channel))
            ->forIrc();
        self::assertStringStartsWith(
            \sprintf('%s drew the ', $channel->username),
            $response
        );
        self::assertStringContainsString('for guns', $response);
    }

    /**
     * Test trying to draw from an empty deck in IRC.
     */
    #[Group('irc')]
    public function testDrawEmptyIRC(): void
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
        $deck->draw(54);
        $deck->save();

        $response = (new Draw('draw', $channel->username, $channel))->forIrc();
        self::assertSame('Insufficient cards remain in deck', $response);
    }
}
