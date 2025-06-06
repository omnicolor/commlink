<?php

declare(strict_types=1);

namespace Modules\Shadowrun5e\Tests\Feature\Listeners;

use App\Enums\ChannelType;
use App\Models\Campaign;
use App\Models\Channel;
use Illuminate\Http\Client\Request;
use Illuminate\Support\Facades\Http;
use Modules\Shadowrun5e\Events\DamageEvent;
use Modules\Shadowrun5e\Listeners\HandleDamageEvent;
use Modules\Shadowrun5e\Models\Character;
use PHPUnit\Framework\Attributes\Group;
use PHPUnit\Framework\Attributes\Medium;
use Tests\TestCase;

use function json_decode;
use function sprintf;

/**
 * Tests for listening for a damage event and re-broadcasting.
 */
#[Group('discord')]
#[Group('events')]
#[Group('slack')]
#[Medium]
final class HandleDamageEventTest extends TestCase
{
    /**
     * Test trying to broadcast to a Discord channel that doesn't have
     * a webhook set up.
     */
    public function testDiscordNoWebhook(): void
    {
        Http::fake();

        $campaign = Campaign::factory()
            ->has(Channel::factory(['type' => ChannelType::Discord]))
            ->create();
        $character = Character::factory()->create([]);

        $damage = (object)[
            'stun' => 1,
            'physical' => 0,
            'overflow' => 0,
        ];

        $event = new DamageEvent($character, $campaign, $damage);
        (new HandleDamageEvent())->handle($event);

        Http::assertNothingSent();

        $character->delete();
    }

    /**
     * Test a character taking some stun damage and sending to Discord.
     */
    public function testStunDamageToDiscord(): void
    {
        Http::fake();

        /** @var Campaign */
        $campaign = Campaign::factory()
            ->has(Channel::factory([
                'type' => ChannelType::Discord,
                'webhook' => 'http://example.org',
            ]))
            ->create();

        $character = Character::factory()->create([]);

        $damage = (object)[
            'stun' => 1,
            'physical' => 0,
            'overflow' => 0,
        ];

        $event = new DamageEvent($character, $campaign, $damage);
        (new HandleDamageEvent())->handle($event);

        $expected = sprintf(
            '%s takes 1 point of stun (Stun: 1 Physical: 0 Overflow: 0)',
            $character,
        );
        Http::assertSent(function (Request $request) use ($expected): bool {
            return 'http://example.org' === $request->url()
                && $expected === $request['content'];
        });

        $character->delete();
    }

    /**
     * Test a character overflowing their stun track into physical on Discord.
     */
    public function testStunOverflowToDiscord(): void
    {
        Http::fake();

        $campaign = Campaign::factory()
            ->has(Channel::factory([
                'type' => ChannelType::Discord,
                'webhook' => 'http://example.org',
            ]))
            ->create();
        $character = Character::factory()->create(['willpower' => 4]);

        // A character with 4 willpower will have 10 boxes of stun. So taking
        // 12 will result in a full stun track plus 1 box of physical.
        $damage = (object)[
            'stun' => 10,
            'physical' => 1,
            'overflow' => 0,
        ];

        $event = new DamageEvent($character, $campaign, $damage);
        (new HandleDamageEvent())->handle($event);

        $expected = sprintf(
            '%s fills their stun track with 10 points and overflows into 1 '
                . 'physical damage (Stun: 10 Physical: 1 Overflow: 0)',
            $character,
        );
        Http::assertSent(function (Request $request) use ($expected): bool {
            return 'http://example.org' === $request->url()
                && $expected === $request['content'];
        });

        $character->delete();
    }

    /**
     * Test a character taking so much stun damage they go into overflow.
     */
    public function testStunToOverflowDiscord(): void
    {
        Http::fake();

        $campaign = Campaign::factory()
            ->has(Channel::factory([
                'type' => ChannelType::Discord,
                'webhook' => 'http://example.org',
            ]))
            ->create();

        $character = Character::factory()->create([
            'body' => 2,
            'damageStun' => 9,
            'willpower' => 4,
        ]);

        // A character with 4 willpower will have 10 boxen of stun, of which
        // 9 are full. Body 2 gives the character 9 physical boxen.
        $damage = (object)[
            'stun' => 1,
            'physical' => 9,
            'overflow' => 1,
        ];

        $event = new DamageEvent($character, $campaign, $damage);
        (new HandleDamageEvent())->handle($event);

        $expected = sprintf(
            '%s fills their stun track (1 point) and their physical track '
                . '(9), taking 1 point of overflow (Stun: 10 Physical: 9 '
                . 'Overflow: 1)',
            $character,
        );
        Http::assertSent(function (Request $request) use ($expected): bool {
            return 'http://example.org' === $request->url()
                && $expected === $request['content'];
        });

        $character->delete();
    }

    /**
     * Test a character taking some physical damage.
     */
    public function testPhysicalSlack(): void
    {
        Http::fake();

        $campaign = Campaign::factory()
            ->has(Channel::factory(['type' => ChannelType::Slack]))
            ->create();

        $character = Character::factory()->create([]);

        $damage = (object)[
            'stun' => 0,
            'physical' => 1,
            'overflow' => 0,
        ];

        $event = new DamageEvent($character, $campaign, $damage);
        (new HandleDamageEvent())->handle($event);

        $expected = sprintf('%s takes 1 point of physical', $character);
        Http::assertSent(function (Request $request) use ($expected): bool {
            $attachment = json_decode($request->body())->attachments[0];
            return 'https://slack.com/api/chat.postMessage' === $request->url()
                && $attachment->text === $expected
                && 'Stun: 0 Physical: 1 Overflow: 0' === $attachment->footer;
        });

        $character->delete();
    }

    /**
     * Test a character taking some physical and overflow damage.
     */
    public function testPhysicalAndOverflowSlack(): void
    {
        Http::fake();

        $campaign = Campaign::factory()
            ->has(Channel::factory(['type' => ChannelType::Slack]))
            ->create();

        $character = Character::factory()->create([]);

        $damage = (object)[
            'stun' => 0,
            'physical' => 2,
            'overflow' => 1,
        ];

        $event = new DamageEvent($character, $campaign, $damage);
        (new HandleDamageEvent())->handle($event);

        $expected = sprintf(
            '%s fills their physical track with 2 points and takes 1 point of '
                . 'overflow',
            $character
        );
        Http::assertSent(function (Request $request) use ($expected): bool {
            $attachment = json_decode($request->body())->attachments[0];
            return 'https://slack.com/api/chat.postMessage' === $request->url()
                && $attachment->text === $expected
                && 'Stun: 0 Physical: 2 Overflow: 1' === $attachment->footer;
        });

        $character->delete();
    }

    /**
     * Test a character that's already down and out taking more damage.
     */
    public function testOverflowSlack(): void
    {
        Http::fake();

        /** @var Campaign */
        $campaign = Campaign::factory()
            ->has(Channel::factory(['type' => ChannelType::Slack]))
            ->create();

        $character = Character::factory()->create([]);

        $damage = (object)[
            'stun' => 0,
            'physical' => 0,
            'overflow' => 2,
        ];

        $event = new DamageEvent($character, $campaign, $damage);
        (new HandleDamageEvent())->handle($event);

        $expected = sprintf(
            '%s takes 2 points of overflow',
            $character
        );
        Http::assertSent(function (Request $request) use ($expected): bool {
            $attachment = json_decode($request->body())->attachments[0];
            return 'https://slack.com/api/chat.postMessage' === $request->url()
                && $attachment->text === $expected
                && 'Stun: 0 Physical: 0 Overflow: 2' === $attachment->footer;
        });

        $character->delete();
    }
}
