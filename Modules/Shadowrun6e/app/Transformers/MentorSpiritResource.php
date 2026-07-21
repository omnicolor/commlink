<?php

declare(strict_types=1);

namespace Modules\Shadowrun6e\Transformers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use Modules\Shadowrun6e\Models\MentorSpirit;
use Override;

/**
 * @mixin MentorSpirit
 */
class MentorSpiritResource extends JsonResource
{
    /**
     * @return array{
     *     advantages: array{
     *         all: string,
     *         magician: string,
     *         adept: string
     *     },
     *     description?: string,
     *     disadvantages: string,
     *     id: string,
     *     name: string,
     *     page: int,
     *     ruleset: string,
     *      links: array{
     *          self: string
     *      }
     * }
     */
    #[Override]
    public function toArray(Request $request): array
    {
        /** @var User $user */
        $user = $request->user();
        return [
            'advantages' => $this->advantages,
            'description' => $this->when(
                $user->hasPermissionTo('view data'),
                $this->description,
            ),
            'disadvantages' => $this->disadvantages,
            'id' => $this->id,
            'name' => $this->name,
            'page' => $this->page,
            'ruleset' => $this->ruleset,
            'links' => [
                'self' => route('shadowrun6e.mentor-spirits.show', $this->id),
                'collection' => route('shadowrun6e.mentor-spirits.index'),
            ],
        ];
    }
}
