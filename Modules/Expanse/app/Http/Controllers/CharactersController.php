<?php

declare(strict_types=1);

namespace Modules\Expanse\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\View\View;
use Modules\Expanse\Http\Resources\CharacterResource;
use Modules\Expanse\Models\Character;

/**
 * @psalm-suppress UnusedClass
 */
class CharactersController extends Controller
{
    /**
     * @psalm-suppress PossiblyUnusedMethod
     */
    public function index(Request $request): JsonResource
    {
        return CharacterResource::collection(
            // @phpstan-ignore-next-line
            Character::where('owner', $request->user()->email)->get()
        );
    }

    /**
     * @psalm-suppress PossiblyUnusedMethod
     */
    public function list(): View
    {
        return view('expanse::characters');
    }

    /**
     * @psalm-suppress PossiblyUnusedMethod
     */
    public function show(Request $request, string $identifier): CharacterResource
    {
        // @phpstan-ignore-next-line
        $email = $request->user()->email;
        return new CharacterResource(
            Character::where('_id', $identifier)
                ->where('owner', $email)
                ->firstOrFail()
        );
    }

    public function view(Request $request, Character $character): View
    {
        $user = $request->user();
        return view(
            'expanse::character',
            ['character' => $character, 'user' => $user]
        );
    }
}
