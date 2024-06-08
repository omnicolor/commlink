<?php

declare(strict_types=1);

namespace Tests\Feature\Models;

use App\Models\Event;
use App\Models\EventRsvp;
use App\Models\User;
use PHPUnit\Framework\Attributes\Medium;
use Tests\TestCase;

/**
 * Tests for the EventRsvp class.
 * @group events
 */
#[Medium]
final class EventRsvpTest extends TestCase
{
    public function testEvent(): void
    {
        $event = Event::factory()->create(['name' => 'RSVP test event']);
        $user = User::factory()->create();

        $response = EventRsvp::create([
            'event_id' => $event->id,
            'response' => 'accepted',
            'user_id' => $user->id,
        ]);
        self::assertSame('RSVP test event', $response->event->name);
    }

    public function testUser(): void
    {
        $event = Event::factory()->create();
        $user = User::factory()->create(['name' => 'RSVP test user']);

        $response = EventRsvp::create([
            'event_id' => $event->id,
            'response' => 'declined',
            'user_id' => $user->id,
        ]);
        self::assertSame('RSVP test user', $response->user->name);
    }
}
