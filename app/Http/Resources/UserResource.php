<?php

declare(strict_types=1);

namespace App\Http\Resources;

use App\Models\Character;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use Spatie\Permission\Models\Role;

/**
 * @mixin User
 */
class UserResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     * @return array<string, array<int|string, array<string, mixed>|string>|int|string>
     */
    public function toArray(Request $request): array
    {
        $characters = [];
        /** @var Character $character */
        // @phpstan-ignore-next-line
        foreach ($this->characters()->get() as $character) {
            $characters[] = [
                'id' => $character->id,
                'name' => (string)$character,
                'system' => $character->system,
                '_links' => [
                    'json' => (string)url('/api/' . $character->system . '/characters/' . $character->id),
                    'html' => (string)url('/characters/' . $character->system . '/' . $character->id),
                ],
            ];
        }

        $gmedCampaigns = [];
        foreach ($this->campaignsGmed as $campaign) {
            $gmedCampaigns[] = [
                'id' => $campaign->id,
                '_links' => [
                    'html' => (string)url('/campaigns/' . $campaign->id),
                ],
                'name' => $campaign->name,
                'system' => $campaign->system,
            ];
        }

        $playingCampaigns = [];
        foreach ($this->campaigns as $campaign) {
            $playingCampaigns[] = [
                'id' => $campaign->id,
                '_links' => [
                    'html' => (string)url('/campaigns/' . $campaign->id),
                ],
                'name' => $campaign->name,
                'system' => $campaign->system,
            ];
        }

        $roles = [];
        /**
         * @psalm-suppress UnnecessaryVarAnnotation
         * @var Role $role
         */
        foreach ($this->roles as $role) {
            $roles[] = [
                'id' => $role->id,
                'name' => $role->name,
            ];
        }

        return [
            'characters' => $characters,
            'email' => $this->email,
            // @phpstan-ignore-next-line
            'features' => $this->getFeatures(),
            'gmOf' => $gmedCampaigns,
            'id' => $this->id,
            'name' => $this->name,
            'playingIn' => $playingCampaigns,
            'roles' => $roles,
            '_links' => [
                'self' => (string)url('/api/users/' . $this->id),
            ],
        ];
    }
}
