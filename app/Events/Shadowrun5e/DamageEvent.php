<?php

declare(strict_types=1);

namespace App\Events\Shadowrun5e;

use App\Models\Campaign;
use App\Models\Shadowrun5e\Character;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;
use stdClass;

class DamageEvent
{
    use Dispatchable;
    use InteractsWithSockets;
    use SerializesModels;

    public function __construct(
        public Character $character,
        public Campaign $campaign,
        public stdClass $damage
    ) {
    }
}
