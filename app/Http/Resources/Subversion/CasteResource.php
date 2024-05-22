<?php

declare(strict_types=1);

namespace App\Http\Resources\Subversion;

use App\Models\Subversion\Caste;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Http\Resources\MissingValue;

/**
 * @mixin Caste
 * @psalm-suppress UnusedClass
 */
class CasteResource extends JsonResource
{
    /**
     * @return array{
     *   description: MissingValue|string,
     *   fortune: int,
     *   id: string,
     *   name: string,
     *   page: int,
     *   ruleset: string,
     *   links: array{
     *     self: string,
     *   }
     * }
     */
    public function toArray(Request $request): array
    {
        /** @var User */
        $user = $request->user();
        return [
            'description' => $this->when(
                $user->hasPermissionTo('view data'),
                $this->description,
            ),
            'fortune' => $this->fortune,
            'id' => $this->id,
            'name' => $this->name,
            'page' => $this->page,
            'ruleset' => $this->ruleset,
            'links' => [
                'self' => route('subversion.castes.show', $this->id),
            ],
        ];
    }
}
