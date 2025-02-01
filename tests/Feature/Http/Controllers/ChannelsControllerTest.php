<?php

declare(strict_types=1);

namespace Tests\Feature\Http\Controllers;

use App\Models\Channel;
use App\Models\User;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Str;
use PHPUnit\Framework\Attributes\Medium;
use Tests\TestCase;

#[Medium]
final class ChannelsControllerTest extends TestCase
{
    use WithFaker;

    /**
     * Test an unauthenticated request to update a channel.
     */
    public function testUnauthenticated(): void
    {
        $this->patchJson(
            route('channels.update', Channel::factory()->create()),
            []
        )
            ->assertUnauthorized();
    }

    /**
     * Test trying to update a channel that doesn't exist.
     */
    public function testUpdateNotFoundChannel(): void
    {
        /** @var User */
        $user = User::factory()->create();
        $this->actingAs($user)
            ->patchJson(
                route('channels.update', 0),
                []
            )
            ->assertNotFound();
    }

    /**
     * Test trying to update someone else's channel.
     */
    public function testUpdateSomeoneElsesChannel(): void
    {
        /** @var User */
        $user = User::factory()->create();
        /** @var Channel */
        $channel = Channel::factory()->create();
        $this->actingAs($user)
            ->patchJson(
                route('channels.update', $channel),
                []
            )
            ->assertForbidden();
    }

    /**
     * Test updating a channel with no changes.
     */
    public function testUpdateNoChanges(): void
    {
        /** @var User */
        $user = User::factory()->create();
        /** @var Channel */
        $channel = Channel::factory()->create([
            'registered_by' => $user->id,
        ]);
        $this->actingAs($user)
            ->patchJson(
                route('channels.update', $channel),
                []
            )
            ->assertUnprocessable();
    }

    /**
     * Test updating a channel with incompatible changes.
     */
    public function testUpdateIncompatibleChanges(): void
    {
        /** @var User */
        $user = User::factory()->create();
        /** @var Channel */
        $channel = Channel::factory()->create([
            'registered_by' => $user->id,
        ]);
        $this->actingAs($user)
            ->patchJson(
                route('channels.update', $channel),
                ['auto' => 1, 'webhook' => 'test']
            )
            ->assertUnprocessable();
    }

    /**
     * Test updating a channel with a webhook URL.
     */
    public function testUpdateWebhookURL(): void
    {
        Http::fake();

        $url = 'https://example.org/webhook';
        /** @var User */
        $user = User::factory()->create();
        /** @var Channel */
        $channel = Channel::factory()->create([
            'registered_by' => $user->id,
        ]);
        $this->actingAs($user)
            ->patchJson(
                route('channels.update', $channel),
                ['webhook' => $url]
            )
            ->assertOk();
        $channel->refresh();
        self::assertSame($url, $channel->webhook);

        Http::assertNothingSent();
    }

    /**
     * Test trying to auto-add a webhook to a non-Discord channel.
     */
    public function testAutoNonDiscord(): void
    {
        /** @var User */
        $user = User::factory()->create();
        /** @var Channel */
        $channel = Channel::factory()->create([
            'registered_by' => $user->id,
            'type' => Channel::TYPE_SLACK,
        ]);
        $this->actingAs($user)
            ->patchJson(
                route('channels.update', $channel->id),
                ['auto' => 1]
            )
            ->assertJson(['message' => 'Auto only works for Discord channels.'])
            ->assertUnprocessable();
    }

    /**
     * Test auto-adding a webhook to a Discord channel.
     */
    public function testAutoDiscord(): void
    {
        /** @var User */
        $user = User::factory()->create();

        /** @var Channel */
        $channel = Channel::factory()->create([
            'channel_id' => (string)$this->faker->randomNumber(8, true),
            'registered_by' => $user->id,
            'type' => Channel::TYPE_DISCORD,
        ]);

        // Values to return from mock Discord API.
        $webhookId = $this->faker->randomNumber(5, false);
        $webhookToken = Str::random(30);
        Http::fake([
            sprintf('https://discord.com/api/channels/%s/webhooks', $channel->channel_id) => Http::response(
                ['id' => $webhookId, 'token' => $webhookToken],
                Response::HTTP_OK,
                []
            ),
        ]);

        self::assertSame(Channel::TYPE_DISCORD, $channel->type);
        self::actingAs($user)
            ->patchJson(
                route('channels.update', $channel->id),
                ['auto' => 1]
            )
            ->assertJsonMissing(['errors' => []])
            ->assertOk();
        $channel->refresh();
        self::assertSame(
            sprintf(
                'https://discord.com/api/webhooks/%s/%s',
                $webhookId,
                $webhookToken
            ),
            $channel->webhook
        );
    }

    public function testDeletingAChannelOwnedBySomeoneElse(): void
    {
        $channel = Channel::factory()->create();
        self::actingAs(User::factory()->create())
            ->delete(route('channels.destroy', $channel))
            ->assertForbidden();
    }

    public function testDeletingANotFoundChannel(): void
    {
        self::actingAs(User::factory()->create())
            ->delete('/channels/0')
            ->assertNotFound();
    }

    public function testDeletingAChannel(): void
    {
        $user = User::factory()->create();
        $channel = Channel::factory()->create(['registered_by' => $user->id]);
        self::assertDatabaseHas('channels', ['id' => $channel->id]);
        self::actingAs($user)
            ->delete(route('channels.destroy', $channel))
            ->assertNoContent();
        self::assertDatabaseMissing('channels', ['id' => $channel->id]);
    }
}
