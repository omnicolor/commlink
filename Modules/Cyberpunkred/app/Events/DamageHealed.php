<?php

declare(strict_types=1);

namespace Modules\Cyberpunkred\Events;

use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;
use Modules\Cyberpunkred\Models\Character;
use Override;
use RangeException;
use Spatie\EventSourcing\StoredEvents\ShouldBeStored;

class DamageHealed extends ShouldBeStored implements ShouldBroadcast
{
    use Dispatchable;
    use InteractsWithSockets;
    use SerializesModels;

    public function __construct(
        public readonly Character $character,
        public readonly int $amount,
    ) {
        if (0 >= $amount) {
            throw new RangeException('Damage must be positive');
        }
    }

    /**
     * @return array<int, PrivateChannel>
     */
    #[Override]
    public function broadcastOn(): array
    {
        return [
            new PrivateChannel('characters.' . $this->character->id),
        ];
    }
}
