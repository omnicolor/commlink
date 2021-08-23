<?php

declare(strict_types=1);

namespace App\Events;

use App\Models\Channel;
use App\Rolls\Roll;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

/**
 * A user rolled some generic dice.
 */
class RollEvent
{
    use Dispatchable;
    use InteractsWithSockets;
    use SerializesModels;

    /**
     * Create a new event instance.
     * @param Roll $roll
     * @param ?Channel $source
     */
    public function __construct(public Roll $roll, public ?Channel $source)
    {
    }
}
