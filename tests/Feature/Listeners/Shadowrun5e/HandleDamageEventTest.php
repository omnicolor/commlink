<?php

declare(strict_types=1);

namespace Tests\Feature\Listeners\Shadowrun5e;

use App\Events\Shadowrun5e\DamageEvent;
use App\Listeners\Shadowrun5e\HandleDamageEvent;
use App\Models\Campaign;
use App\Models\Channel;
use App\Models\Shadowrun5e\Character;
use Illuminate\Http\Client\Request;
use Illuminate\Support\Facades\Http;
use Tests\TestCase;

/**
 * Tests for listening for a damage event and re-broadcasting.
 * @group discord
 * @group events
 * @group slack
 * @medium
 */
final class HandleDamageEventTest extends TestCase
{
    /**
     * Test trying to broadcast to a Discord channel that doesn't have
     * a webhook set up.
     * @test
     */
    public function testDiscordNoWebhook(): void
    {
        Http::fake();

        /** @var Campaign */
        $campaign = Campaign::factory()
            ->has(Channel::factory(['type' => Channel::TYPE_DISCORD]))
            ->create();

        /** @var Character */
        $character = Character::factory()->create([
            'created_by' => __CLASS__ . '::' . __FUNCTION__,
        ]);

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
     * @test
     */
    public function testStunDamageToDiscord(): void
    {
        Http::fake();

        /** @var Campaign */
        $campaign = Campaign::factory()
            ->has(Channel::factory([
                'type' => Channel::TYPE_DISCORD,
                'webhook' => 'http://example.org',
            ]))
            ->create();

        /** @var Character */
        $character = Character::factory()->create([
            'created_by' => __CLASS__ . '::' . __FUNCTION__,
        ]);

        $damage = (object)[
            'stun' => 1,
            'physical' => 0,
            'overflow' => 0,
        ];

        $event = new DamageEvent($character, $campaign, $damage);
        (new HandleDamageEvent())->handle($event);

        $expected = \sprintf(
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
     * @test
     */
    public function testStunOverflowToDiscord(): void
    {
        Http::fake();

        /** @var Campaign */
        $campaign = Campaign::factory()
            ->has(Channel::factory([
                'type' => Channel::TYPE_DISCORD,
                'webhook' => 'http://example.org',
            ]))
            ->create();

        /** @var Character */
        $character = Character::factory()->create([
            'willpower' => 4,
            'created_by' => __CLASS__ . '::' . __FUNCTION__,
        ]);

        // A character with 4 willpower will have 10 boxes of stun. So taking
        // 12 will result in a full stun track plus 1 box of physical.
        $damage = (object)[
            'stun' => 10,
            'physical' => 1,
            'overflow' => 0,
        ];

        $event = new DamageEvent($character, $campaign, $damage);
        (new HandleDamageEvent())->handle($event);

        $expected = \sprintf(
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
     * @test
     */
    public function testStunToOverflowDiscord(): void
    {
        Http::fake();

        /** @var Campaign */
        $campaign = Campaign::factory()
            ->has(Channel::factory([
                'type' => Channel::TYPE_DISCORD,
                'webhook' => 'http://example.org',
            ]))
            ->create();

        /** @var Character */
        $character = Character::factory()->create([
            'body' => 2,
            'damageStun' => 9,
            'willpower' => 4,
            'created_by' => __CLASS__ . '::' . __FUNCTION__,
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

        $expected = \sprintf(
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
     * @test
     */
    public function testPhysicalSlack(): void
    {
        Http::fake();

        /** @var Campaign */
        $campaign = Campaign::factory()
            ->has(Channel::factory(['type' => Channel::TYPE_SLACK]))
            ->create();

        /** @var Character */
        $character = Character::factory()->create([
            'created_by' => __CLASS__ . '::' . __FUNCTION__,
        ]);

        $damage = (object)[
            'stun' => 0,
            'physical' => 1,
            'overflow' => 0,
        ];

        $event = new DamageEvent($character, $campaign, $damage);
        (new HandleDamageEvent())->handle($event);

        $expected = \sprintf('%s takes 1 point of physical', $character);
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
     * @test
     */
    public function testPhysicalAndOverflowSlack(): void
    {
        Http::fake();

        /** @var Campaign */
        $campaign = Campaign::factory()
            ->has(Channel::factory(['type' => Channel::TYPE_SLACK]))
            ->create();

        /** @var Character */
        $character = Character::factory()->create([
            'created_by' => __CLASS__ . '::' . __FUNCTION__,
        ]);

        $damage = (object)[
            'stun' => 0,
            'physical' => 2,
            'overflow' => 1,
        ];

        $event = new DamageEvent($character, $campaign, $damage);
        (new HandleDamageEvent())->handle($event);

        $expected = \sprintf(
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
     * @test
     */
    public function testOverflowSlack(): void
    {
        Http::fake();

        /** @var Campaign */
        $campaign = Campaign::factory()
            ->has(Channel::factory(['type' => Channel::TYPE_SLACK]))
            ->create();

        /** @var Character */
        $character = Character::factory()->create([
            'created_by' => __CLASS__ . '::' . __FUNCTION__,
        ]);

        $damage = (object)[
            'stun' => 0,
            'physical' => 0,
            'overflow' => 2,
        ];

        $event = new DamageEvent($character, $campaign, $damage);
        (new HandleDamageEvent())->handle($event);

        $expected = \sprintf(
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
