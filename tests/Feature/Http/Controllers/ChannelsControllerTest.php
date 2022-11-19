<?php

declare(strict_types=1);

namespace Tests\Feature\Http\Controllers;

use App\Models\Channel;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Str;
use Tests\TestCase;

/**
 * Tests for the channels controller.
 * @group controllers
 * @medium
 */
final class ChannelsControllerTest extends TestCase
{
    use RefreshDatabase;
    use WithFaker;

    /**
     * Test an unauthenticated request to update a channel.
     * @test
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
     * @test
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
     * @test
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
     * @test
     */
    public function testUpdateNoChanges(): void
    {
        /** @var User */
        $user = User::factory()->create();
        /** @var Channel */
        $channel = Channel::factory()->create([
            'registered_by' => $user,
        ]);
        $this->actingAs($user)
            ->patchJson(
                route('channels.update', $channel),
                []
            )
            ->assertStatus(Response::HTTP_UNPROCESSABLE_ENTITY);
    }

    /**
     * Test updating a channel with incompatible changes.
     * @test
     */
    public function testUpdateIncompatibleChanges(): void
    {
        /** @var User */
        $user = User::factory()->create();
        /** @var Channel */
        $channel = Channel::factory()->create([
            'registered_by' => $user,
        ]);
        $this->actingAs($user)
            ->patchJson(
                route('channels.update', $channel),
                ['auto' => 1, 'webhook' => 'test']
            )
            ->assertStatus(Response::HTTP_UNPROCESSABLE_ENTITY);
    }

    /**
     * Test updating a channel with a webhook URL.
     * @test
     */
    public function testUpdateWebhookURL(): void
    {
        Http::fake();

        $url = 'https://example.org/webhook';
        /** @var User */
        $user = User::factory()->create();
        /** @var Channel */
        $channel = Channel::factory()->create([
            'registered_by' => $user,
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
     * @test
     */
    public function testAutoNonDiscord(): void
    {
        /** @var User */
        $user = User::factory()->create();
        /** @var Channel */
        $channel = Channel::factory()->create([
            'registered_by' => $user,
            'type' => Channel::TYPE_SLACK,
        ]);
        $this->actingAs($user)
            ->patchJson(
                route('channels.update', $channel),
                ['auto' => 1]
            )
            ->assertStatus(Response::HTTP_UNPROCESSABLE_ENTITY);
    }

    /**
     * Test auto-adding a webhook to a Discord channel.
     * @test
     */
    public function testAutoDiscord(): void
    {
        /** @var User */
        $user = User::factory()->create();
        /** @var Channel */
        $channel = Channel::factory()->create([
            'channel_id' => (string)$this->faker->randomNumber(8, true),
            'registered_by' => $user,
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

        $this->actingAs($user)
            ->patchJson(
                route('channels.update', $channel),
                ['auto' => 1]
            )
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
}
