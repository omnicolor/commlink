<?php

declare(strict_types=1);

namespace Tests\Feature\Listeners;

use App\Events\RollEvent;
use App\Listeners\HandleRollEvent;
use App\Models\Campaign;
use App\Models\Channel;
use App\Rolls\Generic;
use Illuminate\Http\Client\Request;
use Illuminate\Support\Facades\Http;

/**
 * Tests for listening for a Roll event and rebroadcasting.
 * @group current
 * @group discord
 * @group events
 * @group slack
 * @small
 */
final class HandleRollEventTest extends \Tests\TestCase
{
    /**
     * Test an improperly created event that has no source.
     * @test
     */
    public function testNoSource(): void
    {
        Http::fake();

        $roll = new Generic('1d6', 'unnamed');
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

        $roll = new Generic('1d6', 'unnamed');
        /** @var Channel */
        $source = Channel::factory()->make();
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

        $roll = new Generic('1d6', 'unnamed');
        /** @var Campaign */
        $campaign = Campaign::factory()
            ->has(Channel::factory())
            ->create();
        $source = $campaign->channels->first();
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

        $roll = new Generic('1d6', 'unnamed');
        /** @var Campaign */
        $campaign = Campaign::factory()
            ->has(Channel::factory()->count(4))
            ->create();
        $source = $campaign->channels->first();
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

        $roll = new Generic('1d6', 'unnamed');
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

        Http::assertSent(function (Request $request): bool {
            return 'http://example.com' === $request->url();
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

        $roll = new Generic('1d6', 'unnamed');
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
