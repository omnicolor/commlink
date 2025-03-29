<?php

declare(strict_types=1);

namespace App\Http\Resources;

use App\Models\Character;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use Override;
use Symfony\Component\Routing\Exception\RouteNotFoundException;

use function route;
use function sprintf;

/**
 * Representation of a system-neutral minimal character. For returning a full
 * character, use the appropriate module's CharacterResource.
 * @mixin Character
 */
class CharacterResource extends JsonResource
{
    /**
     * @return array{
     *     id: string,
     *     name: string,
     *     owner: UserMinimalResource,
     *     system?: string,
     *     links: array{
     *         json: null|string,
     *         html: null|string
     *     }
     * }
     */
    #[Override]
    public function toArray(Request $request): array
    {
        try {
            $json = route(sprintf('%s.characters.show', $this->system), $this);
        } catch (RouteNotFoundException) {
            $json = null;
        }
        try {
            $html = route(sprintf('%s.character', $this->system), $this);
        } catch (RouteNotFoundException) {
            $html = null;
        }
        return [
            'id' => $this->id,
            'name' => $this->__toString(),
            'owner' => new UserMinimalResource($this->user()),
            'system' => $this->system,
            'links' => [
                'json' => $json,
                'html' => $html,
            ],
        ];
    }
}
