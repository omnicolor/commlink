<?php

declare(strict_types=1);

namespace Tests\Feature\Models;

use App\Models\Campaign;
use App\Models\Event;
use App\Models\EventRsvp;
use App\Models\User;
use PHPUnit\Framework\Attributes\Medium;
use Tests\TestCase;

/**
 * Tests for the event model.
 * @group events
 */
#[Medium]
final class EventTest extends TestCase
{
    public function testToStringWithName(): void
    {
        $event = new Event([
            'name' => 'Test event',
            'real_start' => '2023-04-01T08:00:00Z',
        ]);
        self::assertSame('Test event', (string)$event);
    }

    public function testToStringWithoutName(): void
    {
        $event = new Event(['real_start' => '2023-04-01T08:00:00Z']);
        self::assertSame('Sat, Apr 1, 2023 8:00 AM', (string)$event);
    }

    public function testCampaign(): void
    {
        /** @var Campaign */
        $campaign = Campaign::factory()->create(['name' => 'Event campaign']);
        $user = User::factory()->create();
        $event = Event::create([
            'campaign_id' => $campaign->id,
            'created_by' => $user->id,
            'name' => 'Test campaign event',
            'real_start' => now(),
        ]);

        $event->refresh();

        self::assertSame('Event campaign', $event->campaign->name);
    }

    public function testCreatedBy(): void
    {
        /** @var Campaign */
        $campaign = Campaign::factory()->create();
        $user = User::factory()->create(['name' => 'Test user']);
        $event = Event::create([
            'campaign_id' => $campaign->id,
            'created_by' => $user->id,
            'name' => 'Test campaign event',
            'real_start' => now(),
        ]);

        $event->refresh();

        self::assertSame('Test user', $event->creator->name);
    }

    public function testResponsesEmpty(): void
    {
        /** @var Campaign */
        $campaign = Campaign::factory()->create();
        $user = User::factory()->create();
        $event = Event::create([
            'campaign_id' => $campaign->id,
            'created_by' => $user->id,
            'name' => 'Test campaign event',
            'real_start' => now(),
        ]);
        self::assertCount(0, $event->responses);
    }

    public function testResponses(): void
    {
        /** @var Campaign */
        $campaign = Campaign::factory()->create();
        $user = User::factory()->create();
        $event = Event::create([
            'campaign_id' => $campaign->id,
            'created_by' => $user->id,
            'name' => 'Test campaign event',
            'real_start' => now(),
        ]);

        EventRsvp::create([
            'event_id' => $event->id,
            'response' => 'accepted',
            'user_id' => $user->id,
        ]);
        self::assertCount(1, $event->responses);
    }
}
