<?php

declare(strict_types=1);

namespace App\Http\Controllers\Shadowrun5e;

use App\Http\Controllers\Controller;
use App\Http\Requests\Shadowrun5e\ContactCreateRequest;
use App\Models\Shadowrun5e\Character;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

class ContactsController extends Controller
{
    /**
     * @psalm-suppress InvalidArgument
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
