<?php

declare(strict_types=1);

namespace Tests\Feature\Listeners;

use App\Events\RollEvent;
use App\Listeners\HandleRollEvent;
use App\Models\Campaign;
use App\Models\Channel;
use App\Rolls\Generic;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\Client\Request;
use Illuminate\Support\Facades\Http;
use phpmock\phpunit\PHPMock;

/**
 * Tests for listening for a Roll event and rebroadcasting.
 * @group discord
 * @group events
 * @group slack
 * @medium
 */
final class HandleRollEventTest extends \Tests\TestCase
{
    use PHPMock;
    use RefreshDatabase;

    /**
     * Mock random_int function to take randomness out of testing.
     * @var \PHPUnit\Framework\MockObject\MockObject
     */
    protected \PHPUnit\Framework\MockObject\MockObject $randomInt;

    /**
     * Set up the mock random function each time.
     */
    public function setUp(): void
    {
        parent::setUp();
        $this->randomInt = $this->getFunctionMock(
            'App\\Rolls',
            'random_int'
        );
    }

    /**
     * Test an improperly created event that has no source.
     * @test
     */
    public function testNoSource(): void
    {
        Http::fake();
        $this->randomInt->expects(self::any())->willReturn(5);
        $roll = new Generic('1d6', 'unnamed', new Channel());
        (new HandleRollEvent())->handle(new RollEvent($roll, null));

        Http::assertNothingSent();
    }

    /**
     * Test an event that has a source without a campaign.
     * @test
     */
    public function testNoCampaign(): void
    {
        Http::fake();
        $this->randomInt->expects(self::any())->willReturn(4);

        /** @var Channel */
        $source = Channel::factory()->make();
        $roll = new Generic('1d6', 'unnamed', $source);
        $rollEvent = new RollEvent($roll, $source);
        (new HandleRollEvent())->handle($rollEvent);

        Http::assertNothingSent();
    }

    /**
     * Test an event that has a source and a campaign that only points to the
     * source as a channel.
     * @medium
     * @test
     */
    public function testOnlySource(): void
    {
        Http::fake();
        $this->randomInt->expects(self::any())->willReturn(3);

        /** @var Campaign */
        $campaign = Campaign::factory()
            ->has(Channel::factory())
            ->create();
        /** @var Channel */
        $source = $campaign->channels->first();
        $roll = new Generic('1d6', 'unnamed', $source);
        $rollEvent = new RollEvent($roll, $source);
        (new HandleRollEvent())->handle($rollEvent);

        Http::assertNothingSent();
    }

    /**
     * Test an event that has a source, a campaign, and more than one channel,
     * but none of the channels have webhooks.
     * @medium
     * @test
     */
    public function testNoWebhooks(): void
    {
        Http::fake();
        $this->randomInt->expects(self::any())->willReturn(6);

        /** @var Campaign */
        $campaign = Campaign::factory()
            ->has(Channel::factory([
                'type' => Channel::TYPE_DISCORD,
            ])->count(4))
            ->create();
        /** @var Channel */
        $source = $campaign->channels->first();
        $roll = new Generic('1d6', 'unnamed', $source);
        $rollEvent = new RollEvent($roll, $source);
        (new HandleRollEvent())->handle($rollEvent);

        Http::assertNothingSent();
    }

    /**
     * Test an event that has a source, a campaign, and more than one channel,
     * one of which has a webhook.
     * @medium
     * @test
     */
    public function testSlack(): void
    {
        Http::fake();
        $this->randomInt->expects(self::any())->willReturn(1);

        // @phpstan-ignore-next-line
        $campaign = Campaign::factory()
            ->has(Channel::factory())
            ->hasChannels(
                1,
                [
                    'type' => Channel::TYPE_SLACK,
                    'webhook' => 'http://example.com',
                ]
            )
            ->create();
        $source = $campaign->channels->first();
        $roll = new Generic('1d6', 'unnamed', $source);
        $rollEvent = new RollEvent($roll, $source);
        (new HandleRollEvent())->handle($rollEvent);

        Http::assertSent(function (Request $request): bool {
            return 'https://slack.com/api/chat.postMessage' === $request->url();
        });
    }

    /**
     * Test an event that has a source, a campaign, and more than one channel,
     * one of which has a webhook.
     * @medium
     * @test
     */
    public function testDiscord(): void
    {
        Http::fake();
        $this->randomInt->expects(self::any())->willReturn(6);

        $roll = new Generic('1d6', 'unnamed', new Channel());
        // @phpstan-ignore-next-line
        $campaign = Campaign::factory()
            ->has(Channel::factory())
            ->hasChannels(
                1,
                [
                    'type' => Channel::TYPE_DISCORD,
                    'webhook' => 'http://example.org',
                ]
            )
            ->create();
        $source = $campaign->channels->first();
        $rollEvent = new RollEvent($roll, $source);
        (new HandleRollEvent())->handle($rollEvent);

        Http::assertSent(function (Request $request): bool {
            return 'http://example.org' === $request->url();
        });
    }
}
