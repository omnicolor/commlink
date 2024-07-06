<?php

declare(strict_types=1);

namespace Modules\Startrekadventures\Http\Resources;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use Modules\Startrekadventures\Models\Talent;

/**
 * @mixin Talent
 */
class TalentResource extends JsonResource
{
    /**
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        /** @var User */
        $user = $request->user();
        return [
            'name' => $this->name,
            'description' => $this->when(
                $user->hasPermissionTo('view data'),
                $this->description,
            ),
            'id' => $this->id,
            'page' => $this->page,
            'requirements' => $this->requirements,
            'ruleset' => $this->ruleset,
        ];
    }
}
