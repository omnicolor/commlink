<?php

declare(strict_types=1);

namespace Tests\Feature\Listeners;

use App\Events\RollEvent;
use App\Listeners\HandleRollEvent;
use App\Models\Campaign;
use App\Models\Channel;
use App\Rolls\Generic;
use App\Rolls\Shadowrun5e\Number;
use Facades\App\Services\DiceService;
use Illuminate\Http\Client\Request;
use Illuminate\Support\Facades\Http;
use PHPUnit\Framework\Attributes\Group;
use PHPUnit\Framework\Attributes\Medium;
use Tests\TestCase;

/**
 * Tests for listening for a Roll event and rebroadcasting.
 */
#[Group('discord')]
#[Group('events')]
#[Group('slack')]
#[Medium]
final class HandleRollEventTest extends TestCase
{
    /**
     * Test an improperly created event that has no source.
     */
    public function testNoSource(): void
    {
        Http::fake();
        DiceService::shouldReceive('rollMany')
            ->once()
            ->with(1, 6)
            ->andReturn([5]);

        $roll = new Generic('1d6', 'unnamed', new Channel());
        (new HandleRollEvent())->handle(new RollEvent($roll, null));

        Http::assertNothingSent();
    }

    /**
     * Test an event that has a source without a campaign.
     */
    public function testNoCampaign(): void
    {
        Http::fake();
        DiceService::shouldReceive('rollMany')
            ->once()
            ->with(1, 6)
            ->andReturn([4]);

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
     */
    public function testOnlySource(): void
    {
        Http::fake();
        DiceService::shouldReceive('rollMany')
            ->once()
            ->with(1, 6)
            ->andReturn([3]);

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
     */
    public function testNoWebhooks(): void
    {
        Http::fake();
        DiceService::shouldReceive('rollMany')
            ->once()
            ->with(1, 6)
            ->andReturn([6]);

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
     */
    public function testSlack(): void
    {
        Http::fake();
        DiceService::shouldReceive('rollMany')
            ->once()
            ->with(1, 6)
            ->andReturn([1]);

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
     */
    public function testDiscord(): void
    {
        Http::fake();
        DiceService::shouldReceive('rollMany')
            ->once()
            ->with(1, 6)
            ->andReturn([6]);

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

    /**
     * Test an event that throws a Slack exception from another channel's
     * command.
     */
    public function testSlackException(): void
    {
        Http::fake();

        $roll = new Number('101', 'unnamed', new Channel());

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
        $rollEvent = new RollEvent($roll, $source);
        (new HandleRollEvent())->handle($rollEvent);

        Http::assertNothingSent();
    }
}
