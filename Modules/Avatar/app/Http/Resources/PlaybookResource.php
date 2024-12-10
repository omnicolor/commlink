<?php

declare(strict_types=1);

namespace Modules\Avatar\Http\Resources;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Http\Resources\MissingValue;
use Modules\Avatar\Models\Playbook;

/**
 * @mixin Playbook
 * @psalm-suppress UnusedClass
 */
class PlaybookResource extends JsonResource
{
    /**
     * @return array{
     *   advanced_technique: string,
     *   balance_left: string,
     *   balance_right: string,
     *   base_stats: array{
     *     creativity: int,
     *     focus: int,
     *     harmony: int,
     *     passion: int
     *   },
     *   demeanor_options: array<int, string>,
     *   description: MissingValue|string,
     *   history: array<int, string>,
     *   id: string,
     *   moment_of_balance: string,
     *   moves: AnonymousResourceCollection,
     *   name: string,
     *   page: int,
     *   ruleset: string,
     *   links: array{
     *     self: string
     *   }
     * }
     */
    public function toArray(Request $request): array
    {
        /** @var User */
        $user = $request->user();
        return [
            'advanced_technique' => $this->advanced_technique,
            'balance_left' => $this->balance_left,
            'balance_right' => $this->balance_right,
            'base_stats' => [
                'creativity' => $this->creativity,
                'focus' => $this->focus,
                'harmony' => $this->harmony,
                'passion' => $this->passion,
            ],
            'demeanor_options' => $this->demeanor_options,
            'description' => $this->when(
                $user->hasPermissionTo('view data'),
                $this->description,
            ),
            'history' => $this->history,
            'id' => $this->id,
            'moment_of_balance' => $this->moment_of_balance,
            'moves' => MoveResource::collection($this->moves),
            'name' => $this->name,
            'page' => $this->page,
            'ruleset' => $this->ruleset,
            'links' => [
                'self' => route('avatar.playbooks.show', $this->id),
            ],
        ];
    }
}
