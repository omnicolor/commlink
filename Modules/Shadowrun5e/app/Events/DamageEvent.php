<?php

declare(strict_types=1);

namespace Modules\Shadowrun5e\Events;

use App\Models\Campaign;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;
use Modules\Shadowrun5e\Models\Character;
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
