<?php

declare(strict_types=1);

namespace Modules\Alien\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use Modules\Alien\Models\Character;

class RollResource extends JsonResource
{
    /**
     * @param array<string, mixed> $result
     */
    public function __construct(
        Request $request,
        protected array $result,
        protected Character $character,
    ) {
        parent::__construct($request);
    }

    /**
     * @return array{
     *   panic: bool,
     *   pushable: bool,
     *   rolls: array<int, int>,
     *   success: bool,
     *   text: string,
     *   title: string,
     * }
     */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->result['id'],
            'panic' => $this->result['panic'],
            'pushable' => $this->result['pushable'],
            'rolls' => $this->result['rolls'],
            'success' => $this->result['success'],
            'text' => $this->result['text'],
            'title' => $this->result['title'],
        ];
    }

    /**
     * @return array{
     *   links: array{
     *     character: string,
     *     campaign?: string,
     *     self: string,
     *     pushes: array<int, string>
     *   }
     * }
     */
    public function with(Request $request): array
    {
        $links = [
            'character' => route('alien.characters.show', $this->character),
            'self' => route('alien.rolls.show', $this->result['id']),
            'pushes' => [],
        ];
        if (null !== $this->character->campaign_id) {
            $links['campaign'] =
                route('campaigns.show', $this->character->campaign_id);
        }
        return ['links' => $links];
    }
}
