<?php

declare(strict_types=1);

namespace Modules\Dnd5e\Events;

use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;
use Modules\Dnd5e\Enums\CoinType;
use Modules\Dnd5e\Models\Character;
use Override;
use Spatie\EventSourcing\StoredEvents\ShouldBeStored;

class MoneyGained extends ShouldBeStored implements ShouldBroadcast
{
    use Dispatchable;
    use InteractsWithSockets;
    use SerializesModels;

    public function __construct(
        public readonly Character $character,
        public readonly CoinType $currency,
        public readonly int $amount,
    ) {
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
