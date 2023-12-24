<?php

declare(strict_types=1);

namespace Tests\Feature\Http\Controllers;

use App\Models\Campaign;
use App\Models\Event;
use App\Models\EventRsvp;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Tests\TestCase;

/**
 * @group events
 * @medium
 */
final class EventsControllerTest extends TestCase
{
    use RefreshDatabase;

    public function testIndexNoEvents(): void
    {
        $user = User::factory()->create();
        self::actingAs($user)
            ->get('/api/events')
            ->assertOk()
            ->assertJsonCount(0, 'data');
    }

    public function testIndexWithEvent(): void
    {
        $user = User::factory()->create();
        Event::factory()->create(['created_by' => $user->id]);
        self::actingAs($user)
            ->get('/api/events')
            ->assertOk()
            ->assertJsonCount(1, 'data');
    }

    public function testIndexForCampaignNoEvents(): void
    {
        $user = User::factory()->create();
        // Event owned by the user, but in a different campaign.
        Event::factory()->create(['created_by' => $user->id]);
        $campaign = Campaign::factory()->create(['gm' => $user->id]);
        self::actingAs($user)
            ->get(route('events.campaign-index', ['campaign' => $campaign]))
            ->assertOk()
            ->assertJsonCount(0, 'data');
    }

    public function testIndexForCampaign(): void
    {
        $user = User::factory()->create();
        /** @var Campaign */
        $campaign = Campaign::factory()->create(['gm' => $user->id]);
        Event::factory()->create(['campaign_id' => $campaign->id]);
        self::actingAs($user)
            ->get(route('events.campaign-index', ['campaign' => $campaign]))
            ->assertOk()
            ->assertJsonCount(1, 'data');
    }

    public function testViewOtherEvent(): void
    {
        $user = User::factory()->create();
        $event = Event::factory()->create();
        self::actingAs($user)
            ->get(sprintf('/api/events/%d', $event->id))
            ->assertForbidden();
    }

    public function testViewCreatedEvent(): void
    {
        $user = User::factory()->create();
        $event = Event::factory()->create(['created_by' => $user->id]);
        self::actingAs($user)
            ->get(sprintf('/api/events/%d', $event->id))
            ->assertOk();
    }

    public function testViewForGmedEvent(): void
    {
        $user = User::factory()->create();
        /** @var Campaign */
        $campaign = Campaign::factory()->create(['gm' => $user->id]);
        $event = Event::factory()->create([
            'campaign_id' => $campaign->id,
            'created_by' => $user->id,
        ]);
        self::actingAs($user)
            ->get(sprintf('/api/events/%d', $event->id))
            ->assertOk()
            ->assertJsonCount(0, 'data.responses');
    }

    public function testViewForEventInvitedTo(): void
    {
        $user = User::factory()->create();
        /** @var Campaign */
        $campaign = Campaign::factory()
            ->hasAttached($user, ['status' => 'accepted'])
            ->create();
        $event = Event::factory()->create([
            'campaign_id' => $campaign->id,
        ]);
        EventRsvp::create([
            'event_id' => $event->id,
            'response' => 'accepted',
            'user_id' => $user->id,
        ]);
        self::actingAs($user)
            ->get(sprintf('/api/events/%d', $event->id))
            ->assertOk()
            ->assertJsonCount(1, 'data.responses');
    }

    public function testUpdateWithPut(): void
    {
        $user = User::factory()->create();
        /** @var Campaign */
        $campaign = Campaign::factory()->create();
        $event = Event::factory()->create([
            'campaign_id' => $campaign->id,
            'created_by' => $user->id,
            'description' => 'Old description',
            'game_end' => '2080-04-01',
            'game_start' => '2080-03-31',
            'name' => 'Old name',
            'real_end' => '2023-02-02T00:00:00Z',
            'real_start' => '2023-01-01T00:00:00Z',
        ]);
        $newStart = now()->toDateString();
        self::actingAs($user)
            ->putJson(
                route('events.put', ['event' => $event]),
                [
                    'name' => 'New put name',
                    'real_start' => $newStart,
                ],
            )
            ->assertAccepted();
        $event->refresh();
        self::assertSame('New put name', $event->name);
        self::assertNull($event->description);
        // @phpstan-ignore-next-line
        self::assertSame($newStart, $event->real_start->toDateString());
        self::assertNull($event->game_end);
        self::assertNull($event->game_start);
        self::assertNull($event->real_end);
    }

    public function testPatchWithInvalidContentType(): void
    {
        $user = User::factory()->create();
        $event = Event::factory()->create(['created_by' => $user->id]);
        self::actingAs($user)
            ->withHeaders(['Content-Type' => 'text/xml'])
            ->patch(
                route('events.patch', ['event' => $event]),
                ['<xml></xml>'],
            )
            ->assertStatus(Response::HTTP_UNSUPPORTED_MEDIA_TYPE);
    }

    public function testUpdateWithJsonPatchInvalidOperationException(): void
    {
        $user = User::factory()->create();
        /** @var Campaign */
        $event = Event::factory()->create(['created_by' => $user->id]);
        self::actingAs($user)
            ->call(
                method: Request::METHOD_PATCH,
                uri: route('events.patch', ['event' => $event]),
                server: ['CONTENT_TYPE' => 'application/json-patch+json'],
                content: 'sdfd: {',
            )
            ->assertBadRequest();
    }

    public function testUpdateWithJsonPatchTypeError(): void
    {
        $user = User::factory()->create();
        /** @var Campaign */
        $event = Event::factory()->create(['created_by' => $user->id]);
        self::actingAs($user)
            ->call(
                method: Request::METHOD_PATCH,
                uri: route('events.patch', ['event' => $event]),
                server: ['CONTENT_TYPE' => 'application/json-patch+json'],
                content: (string)json_encode('[sdfd: {'),
            )
            ->assertBadRequest();
    }

    public function testUpdateWithJsonPatchInvalidPointer(): void
    {
        $user = User::factory()->create();
        /** @var Campaign */
        $event = Event::factory()->create(['created_by' => $user->id]);
        self::actingAs($user)
            ->call(
                method: 'PATCH',
                uri: route('events.patch', ['event' => $event]),
                server: ['CONTENT_TYPE' => 'application/json-patch+json'],
                content: (string)json_encode([
                    ['op' => 'remove', 'path' => 'foo'],
                ]),
            )
            ->assertSee('Valid pointer values are:')
            ->assertBadRequest();
    }

    public function testUpdateWithJsonPatchInvalidStartDateFormat(): void
    {
        $user = User::factory()->create();
        /** @var Campaign */
        $event = Event::factory()->create(['created_by' => $user->id]);
        self::actingAs($user)
            ->call(
                method: 'PATCH',
                uri: route('events.patch', ['event' => $event]),
                server: ['CONTENT_TYPE' => 'application/json-patch+json'],
                content: (string)json_encode([
                    [
                        'op' => 'replace',
                        'path' => '/real_start',
                        'value' => 'asdf',
                    ],
                ]),
            )
            ->assertSee('Invalid date format')
            ->assertBadRequest();
    }

    public function testUpdateWithJsonPatchInvalidEndDateFormat(): void
    {
        $user = User::factory()->create();
        /** @var Campaign */
        $event = Event::factory()->create(['created_by' => $user->id]);
        self::actingAs($user)
            ->call(
                method: 'PATCH',
                uri: route('events.patch', ['event' => $event]),
                server: ['CONTENT_TYPE' => 'application/json-patch+json'],
                content: (string)json_encode([
                    ['op' => 'replace', 'path' => '/real_end', 'value' => 'xx'],
                ]),
            )
            ->assertSee('Invalid date format')
            ->assertBadRequest();
    }

    public function testUpdateWithJsonPatchStartAfterEnd(): void
    {
        $user = User::factory()->create();
        /** @var Campaign */
        $campaign = Campaign::factory()->create();
        $event = Event::factory()->create([
            'created_by' => $user->id,
            'real_start' => '2023-01-01T00:00:00Z',
        ]);
        self::actingAs($user)
            ->call(
                method: 'PATCH',
                uri: route('events.patch', ['event' => $event]),
                server: ['CONTENT_TYPE' => 'application/json-patch+json'],
                content: (string)json_encode([
                    [
                        'op' => 'replace',
                        'path' => '/real_end',
                        'value' => '2022-04-01',
                    ],
                ]),
            )
            ->assertSee('end must be after its beginning')
            ->assertBadRequest();
    }

    public function testUpdateWithJsonPatch(): void
    {
        $user = User::factory()->create();
        /** @var Campaign */
        $campaign = Campaign::factory()->create();
        $event = Event::factory()->create([
            'campaign_id' => $campaign->id,
            'created_by' => $user->id,
            'description' => 'Old description',
            'game_end' => '2080-04-01',
            'game_start' => '2080-03-31',
            'name' => 'Old name',
            'real_end' => '2023-02-02T00:00:00Z',
            'real_start' => '2023-01-01T00:00:00Z',
        ]);
        self::actingAs($user)
            ->call(
                method: 'PATCH',
                uri: route('events.patch', ['event' => $event]),
                server: ['CONTENT_TYPE' => 'application/json-patch+json'],
                content: (string)json_encode([
                    ['op' => 'remove', 'path' => '/name'],
                    [
                        'op' => 'replace',
                        'path' => '/real_start',
                        'value' => '2023-04-01 20:00:00',
                    ],
                    ['op' => 'remove', 'path' => '/real_end'],
                    ['op' => 'remove', 'path' => '/description'],
                ]),
            )
            ->assertAccepted();
        $event->refresh();
        self::assertSame('Sat, Apr 1, 2023 8:00 PM', $event->name);
        self::assertNull($event->description);
        self::assertSame(
            '2023-04-01T20:00:00.000000Z',
            // @phpstan-ignore-next-line
            $event->real_start->toJSON(),
        );
        self::assertNull($event->real_end);
        self::assertSame('2080-04-01', $event->game_end);
        self::assertSame('2080-03-31', $event->game_start);
    }

    public function testUpdateWithDataPatch(): void
    {
        $user = User::factory()->create();
        /** @var Campaign */
        $campaign = Campaign::factory()->create();
        $event = Event::factory()->create([
            'campaign_id' => $campaign->id,
            'created_by' => $user->id,
            'description' => 'Old description',
            'game_end' => '2080-04-01',
            'game_start' => '2080-03-31',
            'name' => 'Old name',
            'real_end' => '2023-02-02T00:00:00Z',
            'real_start' => '2023-01-01T00:00:00Z',
        ]);
        self::actingAs($user)
            ->patchJson(
                route('events.patch', ['event' => $event]),
                [
                    'description' => 'New patched description',
                    'name' => 'New patched name',
                ],
            )
            ->assertAccepted();
        $event->refresh();
        self::assertSame('New patched name', $event->name);
        self::assertSame('New patched description', $event->description);
        // @phpstan-ignore-next-line
        self::assertSame('2023-01-01T00:00:00.000000Z', $event->real_start->toJSON());
        self::assertSame('2080-04-01', $event->game_end);
        self::assertSame('2080-03-31', $event->game_start);
        // @phpstan-ignore-next-line
        self::assertSame('2023-02-02T00:00:00.000000Z', $event->real_end->toJSON());
        // @phpstan-ignore-next-line
        self::assertSame('2023-01-01T00:00:00.000000Z', $event->real_start->toJSON());
    }

    public function testStoreOthersCampaign(): void
    {
        $user = User::factory()->create();
        /** @var Campaign */
        $campaign = Campaign::factory()->create();
        self::actingAs($user)
            ->postJson(
                route('events.store', ['campaign' => $campaign]),
                ['real_start' => '2020-02-02']
            )
            ->assertForbidden();
    }

    public function testStoreCampaign(): void
    {
        $user = User::factory()->create();
        /** @var Campaign */
        $campaign = Campaign::factory()->create([
            'gm' => $user->id,
        ]);
        self::actingAs($user)
            ->postJson(
                route('events.store', ['campaign' => $campaign]),
                [
                    'description' => 'Test event',
                    'game_start' => 'Star date 1234.5',
                    'game_end' => 'Star date 1235.1',
                    'real_start' => '2020-02-02 20:00',
                    'real_end' => '2020-02-02 22:00',
                ]
            )
            ->assertJson([
                'data' => [
                    'campaign' => [
                        'id' => $campaign->id,
                        'name' => $campaign->name,
                    ],
                    'created_by' => [
                        'id' => $user->id,
                        'name' => $user->name,
                    ],
                    'description' => 'Test event',
                    'game_end' => 'Star date 1235.1',
                    'game_start' => 'Star date 1234.5',
                    'name' => 'Sun, Feb 2, 2020 8:00 PM',
                    'real_end' => '2020-02-02T22:00:00.000000Z',
                    'real_start' => '2020-02-02T20:00:00.000000Z',
                    'responses' => [],
                    'links' => [
                        // Ignoring since we don't know the event's ID.
                    ],
                ],
                'links' => [
                    'root' => '/',
                    'collection' => '/events',
                ],
            ])
            ->assertCreated();
    }

    public function testDestroy(): void
    {
        $user = User::factory()->create();
        $event = Event::factory()->create(['created_by' => $user->id]);
        self::actingAs($user)
            ->delete(route('events.destroy', ['event' => $event]))
            ->assertNoContent();
        self::actingAs($user)
            ->get(route('events.show', ['event' => $event]))
            ->assertNotFound();
        self::actingAs($user)
            ->delete(route('events.destroy', ['event' => $event]))
            ->assertNoContent();
    }

    public function testDestroyOthersEvent(): void
    {
        $user = User::factory()->create();
        $event = Event::factory()->create();
        self::actingAs($user)
            ->delete(route('events.destroy', ['event' => $event]))
            ->assertForbidden();
    }
}
