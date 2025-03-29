<?php

declare(strict_types=1);

namespace App\Http\Resources;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use Override;

/**
 * @mixin User
 * @psalm-type Role = object{id: int, name: string}
 */
class UserMinimalResource extends JsonResource
{
    /**
     * @return array{
     *     id: int,
     *     name: string
     * }
     */
    #[Override]
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'name' => $this->name,
        ];
    }
}
