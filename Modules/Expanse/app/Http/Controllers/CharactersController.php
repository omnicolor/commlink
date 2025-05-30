<?php

declare(strict_types=1);

namespace Modules\Expanse\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\View\View;
use Modules\Expanse\Http\Resources\CharacterResource;
use Modules\Expanse\Models\Character;

use function view;

class CharactersController extends Controller
{
    public function index(Request $request): JsonResource
    {
        return CharacterResource::collection(
            Character::where('owner', $request->user()?->email->address)->get()
        );
    }

    public function list(): View
    {
        return view('expanse::characters');
    }

    public function show(Request $request, string $identifier): CharacterResource
    {
        $email = $request->user()?->email->address;
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
