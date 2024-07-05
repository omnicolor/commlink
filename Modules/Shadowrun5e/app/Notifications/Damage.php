<?php

declare(strict_types=1);

namespace Modules\Shadowrun5e\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\BroadcastMessage;
use Illuminate\Notifications\Notification;

/**
 * @psalm-suppress PossiblyUnusedMethod
 */
class Damage extends Notification implements ShouldBroadcast, ShouldQueue
{
    use Queueable;

    /**
     * Create a new notification instance.
     * @psalm-suppress PossiblyUnusedMethod
     */
    public function __construct(
        public int $stun,
        public int $physical,
        public int $overflow,
    ) {
    }

    /**
     * Get the notification's delivery channels.
     * @psalm-suppress PossiblyUnusedMethod
     * @return array<int, string>
     */
    public function via(): array
    {
        return ['broadcast'];
    }

    /**
     * @psalm-suppress PossiblyUnusedMethod
     */
    public function toBroadcast(): BroadcastMessage
    {
        return new BroadcastMessage([
            'stun' => $this->stun,
            'physical' => $this->physical,
            'overflow' => $this->overflow,
        ]);
    }

    /**
     * Get the array representation of the notification.
     * @psalm-suppress PossiblyUnusedMethod
     * @return array<string, int>
     */
    public function toArray(): array
    {
        return [
            'stun' => $this->stun,
            'physical' => $this->physical,
            'overflow' => $this->overflow,
        ];
    }
}
