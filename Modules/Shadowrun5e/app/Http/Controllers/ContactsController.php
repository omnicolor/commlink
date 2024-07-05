<?php

declare(strict_types=1);

namespace Modules\Shadowrun5e\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Modules\Shadowrun5e\Http\Requests\ContactCreateRequest;
use Modules\Shadowrun5e\Models\Character;

/**
 * @psalm-suppress UnusedClass
 */
class ContactsController extends Controller
{
    /**
     * @psalm-suppress InvalidArgument
     * @psalm-suppress PossiblyUnusedMethod
     */
    public function index(Character $character): JsonResponse
    {
        /** @var \App\Models\User */
        $user = Auth::user();

        $campaign = $character->campaign();
        abort_if(
            $user->email !== $character->owner
            && (null === $campaign || $user->isNot($campaign->gamemaster)),
            JsonResponse::HTTP_NOT_FOUND
        );

        $contacts = collect($character->contacts);
        if (null === $campaign || $user->isNot($campaign->gamemaster)) {
            // @phpstan-ignore-next-line
            $contacts->transform(function (array $item): Collection {
                return collect($item)->except(['gmNotes']);
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
