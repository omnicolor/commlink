<?php

declare(strict_types=1);

namespace App\Http\Controllers\Shadowrun5E;

use App\Http\Resources\Shadowrun5E\CharacterResource;
use App\Models\Shadowrun5E\Character;
use Illuminate\Http\Resources\Json\JsonResource;

class CharactersController extends \App\Http\Controllers\Controller
{
    /**
     * Return a collection of characters for the logged in user.
     * @return JsonResource
     */
    public function index(): JsonResource
    {
        return CharacterResource::collection(
            // @phpstan-ignore-next-line
            Character::where('owner', \Auth::user()->email)->get()
        );
    }
}
