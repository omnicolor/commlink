<?php

declare(strict_types=1);

namespace Modules\Shadowrun5e\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Modules\Shadowrun5e\Http\Requests\ContactCreateRequest;
use Modules\Shadowrun5e\Models\Character;

use function abort_if;
use function collect;
use function response;

/**
 * @psalm-suppress UnusedClass
 */
class ContactsController extends Controller
{
    /**
     * @psalm-suppress PossiblyUnusedMethod
     */
    public function index(Request $request, Character $character): JsonResponse
    {
        /** @var User */
        $user = $request->user();

        $campaign = $character->campaign();
        abort_if(
            $user->email !== $character->owner
            && (null === $campaign || $user->isNot($campaign->gamemaster)),
            JsonResponse::HTTP_NOT_FOUND,
        );

        $contacts = collect($character->contacts);
        if (null === $campaign || $user->isNot($campaign->gamemaster)) {
            $contacts->transform(function (array $item): array {
                return collect($item)->except(['gmNotes'])->toArray();
            });
        }
        return response()->json($contacts);
    }

    /**
     * @psalm-suppress PossiblyUnusedMethod
     */
    public function store(
        Character $character,
        ContactCreateRequest $request
    ): JsonResponse {
        Log::info(
            '{system} - Creating contact "{name}" for "{character}"',
            [
                'system' => 'shadowrun5e',
                'name' => $request->input('name'),
                'character' => (string)$character,
            ]
        );
        $contact = [
            'archetype' => $request->archetype,
            'connection' => $request->connection,
            'loyalty' => $request->loyalty,
            'gmNotes' => $request->gmNotes,
            'name' => $request->name,
            'notes' => $request->notes,
        ];
        $contacts = $character->contacts;
        $contacts[] = $contact;
        $character->contacts = $contacts;
        $character->save();
        return response()->json($contact, JsonResponse::HTTP_CREATED);
    }
}
