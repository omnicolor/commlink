<?php

declare(strict_types=1);

namespace App\Http\Controllers\Expanse;

use App\Http\Resources\Expanse\CharacterResource;
use App\Models\Expanse\Character;
use App\Models\User;
use Illuminate\Http\Resources\Json\JsonResource;

/**
 * Controller for interacting with Expanse characters.
 */
class CharactersController extends \App\Http\Controllers\Controller
{
    /**
     * Return a collection of Expanse characters for the logged in user.
     * @return JsonResource
     */
    public function index(): JsonResource
    {
        return CharacterResource::collection(
            // @phpstan-ignore-next-line
            Character::where('owner', \Auth::user()->email)->get()
        );
    }

    /**
     * Return a single Expanse character.
     * @param string $identifier
     * @return CharacterResource
     */
    public function show(string $identifier): CharacterResource
    {
        // @phpstan-ignore-next-line
        $email = \Auth::user()->email;
        return new CharacterResource(
            Character::where('_id', $identifier)
                ->where('owner', $email)
                ->firstOrFail()
        );
    }
}
