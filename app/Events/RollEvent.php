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
     * Roll object that generated the event.
     * @var Roll
     */
    public Roll $roll;

    /**
     * Where the event was generated.
     * @var \App\Models\Channel
     */
    public Channel $source;

    /**
     * Create a new event instance.
     * @param Roll $roll
     * @param Channel $source
     */
    public function __construct(Roll $roll, Channel $source)
    {
        $this->roll = $roll;
        $this->source = $source;
    }
}
