<?php

declare(strict_types=1);

namespace App\Http\Resources;

use App\Models\Campaign;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use Illuminate\Http\Resources\Json\JsonResource;
use Override;
use Spatie\Permission\Models\Role;

use function route;

/**
 * @phpstan-type LocalCampaignResource array{
 *     id: int,
 *     links: array{
 *         json: ?string,
 *         html: ?string
 *     },
 *     name: string,
 *     system: string
 * }
 * @mixin User
 */
class UserResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     * @return array{
     *     characters: AnonymousResourceCollection,
     *     email: string,
     *     features: array<int, string>,
     *     gmOf: array<int, LocalCampaignResource>,
     *     id: int,
     *     name: string,
     *     playingIn: array<int, LocalCampaignResource>,
     *     roles: array<int, array{id: int, name: string}>,
     *     links: array{
     *         self: string
     *     }
     * }
     */
    #[Override]
    public function toArray(Request $request): array
    {
        $gmedCampaigns = [];
        /** @var Campaign $campaign */
        foreach ($this->campaignsGmed as $campaign) {
            $gmedCampaigns[] = [
                'id' => $campaign->id,
                'links' => [
                    'json' => route('campaigns.show', $campaign),
                    'html' => route('campaign.view', $campaign),
                ],
                'name' => $campaign->name,
                'system' => $campaign->system,
            ];
        }

        $playingCampaigns = [];
        /** @var Campaign $campaign */
        foreach ($this->campaigns as $campaign) {
            $playingCampaigns[] = [
                'id' => $campaign->id,
                'links' => [
                    'json' => route('campaigns.show', $campaign),
                    'html' => route('campaign.view', $campaign),
                ],
                'name' => $campaign->name,
                'system' => $campaign->system,
            ];
        }

        $roles = [];
        /** @var Role $role */
        foreach ($this->roles as $role) {
            $roles[] = [
                'id' => (int)$role->id,
                'name' => $role->name,
            ];
        }

        return [
            'characters' => CharacterResource::collection($this->characters()->get()),
            'email' => $this->email,
            'features' => $this->getFeatures(),
            'gmOf' => $gmedCampaigns,
            'id' => $this->id,
            'name' => $this->name,
            'playingIn' => $playingCampaigns,
            'roles' => $roles,
            'links' => [
                'self' => route('users.show', $this->id),
            ],
        ];
    }
}
