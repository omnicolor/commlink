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

class MoneyConverted extends ShouldBeStored implements ShouldBroadcast
{
    use Dispatchable;
    use InteractsWithSockets;
    use SerializesModels;

    public int $to_amount;

    public function __construct(
        public readonly Character $character,
        public readonly CoinType $from_currency,
        public readonly int $from_amount,
        public readonly CoinType $to_currency,
        int|null $to_amount = null,
    ) {
        if (null === $to_amount) {
            $this->to_amount = (int)CoinType::convert(
                $from_amount,
                $from_currency,
                $to_currency,
            );
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
