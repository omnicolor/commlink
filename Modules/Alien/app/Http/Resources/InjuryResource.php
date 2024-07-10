<?php

declare(strict_types=1);

namespace Modules\Alien\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use Modules\Alien\Models\Injury;

/**
 * @mixin Injury
 */
class InjuryResource extends JsonResource
{
    /**
     * @return  array<string, array<string, int|string>|bool|int|null|string>
     */
    public function toArray(Request $request): array
    {
        return [
            'death_roll_modifier' => $this->death_roll_modifier,
            'effects' => $this->effects,
            'effects_text' => $this->effects_text,
            'fatal' => $this->fatal,
            'healing_time' => $this->healing_time,
            'id' => $this->id,
            'name' => $this->name,
            'roll' => $this->roll,
            'time_limit' => $this->time_limit,
            'links' => [
                'self' => route('alien.injuries.show', $this->id),
            ],
        ];
    }
}
