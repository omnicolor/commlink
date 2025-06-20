<?php

declare(strict_types=1);

namespace Modules\Subversion\Http\Resources;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Http\Resources\MissingValue;
use Modules\Subversion\Models\Gear;
use Override;

use function route;

/**
 * @mixin Gear
 */
class GearResource extends JsonResource
{
    /**
     * @return array{
     *   category: string,
     *   description: MissingValue|string,
     *   firewall: MissingValue|int,
     *   fortune: int,
     *   id: string,
     *   name: string,
     *   page: int,
     *   ruleset: string,
     *   security_rating: MissingValue|int,
     *   links: array{
     *     self: string,
     *   }
     * }
     */
    #[Override]
    public function toArray(Request $request): array
    {
        /** @var User */
        $user = $request->user();
        return [
            'category' => $this->category,
            'description' => $this->when(
                $user->hasPermissionTo('view data'),
                $this->description,
            ),
            'firewall' => $this->when(null !== $this->firewall, $this->firewall),
            'fortune' => $this->fortune,
            'id' => $this->id,
            'name' => $this->name,
            'page' => $this->page,
            'ruleset' => $this->ruleset,
            'security_rating' => $this->when(
                null !== $this->security_rating,
                $this->security_rating,
            ),
            'links' => [
                'self' => route('subversion.gear.show', $this->id),
            ],
        ];
    }
}
