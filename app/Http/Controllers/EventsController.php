<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Http\Requests\EventPatchRequest;
use App\Http\Requests\EventPostRequest;
use App\Http\Requests\EventPutRequest;
use App\Http\Requests\RsvpPutRequest;
use App\Http\Resources\EventCollection;
use App\Http\Resources\EventResource;
use App\Http\Resources\EventRsvpResource;
use App\Models\Campaign;
use App\Models\Event;
use App\Models\EventRsvp;
use App\Models\User;
use App\Policies\EventPolicy;
use Carbon\Exceptions\InvalidFormatException;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Rs\Json\Patch;
use Rs\Json\Patch\InvalidOperationException;
use Rs\Json\Pointer\InvalidPointerException;
use TypeError;

/**
 * @psalm-suppress UnusedClass
 */
class EventsController extends Controller
{
    public function index(): EventCollection
    {
        $events = Event::with('creator', 'campaign.users', 'responses')->get();
        return new EventCollection($events);
    }

    public function indexForCampaign(Campaign $campaign): EventCollection
    {
        $events = Event::with('creator', 'campaign.users', 'responses')
            ->where('campaign_id', $campaign->id)
            ->get();
        return new EventCollection($events);
    }

    public function show(Event $event): EventResource
    {
        $this->authorize('view', $event);
        return new EventResource($event);
    }

    public function patch(Request $request, Event $event): JsonResponse
    {
        return match ($request->headers->get('Content-Type')) {
            'application/json' => $this->dataPatch($request, $event),
            'application/json-patch+json' => $this->jsonPatch($request, $event),
            default => new JsonResponse(
                sprintf(
                    'Unacceptable Content-Type: %s',
                    $request->headers->get('Content-Type'),
                ),
                JsonResponse::HTTP_UNSUPPORTED_MEDIA_TYPE,
            ),
        };
    }

    protected function dataPatch(Request $request, Event $event): JsonResponse
    {
        $this->authorize('update', $event);
        // @phpstan-ignore-next-line
        $validated = $request->validate((new EventPatchRequest())->rules());
        $event->fill($validated);
        $event->updated_at = now();
        $event->save();
        return new JsonResponse(
            new EventResource($event),
            JsonResponse::HTTP_ACCEPTED,
        );
    }

    protected function jsonPatch(Request $request, Event $event): JsonResponse
    {
        $document = $event->toJson();
        $patch = (string)json_encode($request->input());
        try {
            $updatedEvent = json_decode(
                (string)(new Patch($document, $patch))->apply()
            );
            // @phpstan-ignore-next-line
        } catch (TypeError $ex) {
            abort(JsonResponse::HTTP_BAD_REQUEST, $ex->getMessage());
        } catch (InvalidOperationException $ex) {
            // Will be thrown when using invalid JSON in a patch document.
            abort(JsonResponse::HTTP_BAD_REQUEST, $ex->getMessage());
            // @phpstan-ignore-next-line
        } catch (InvalidPointerException $ex) {
            abort(
                JsonResponse::HTTP_BAD_REQUEST,
                $ex->getMessage()
                    . '. Valid pointer values are: /description, /game_end, '
                    . '/game_start, /name, /real_end, /real_start',
            );
        }

        // Real start is required and needs to be a date.
        abort_if(
            !property_exists($updatedEvent, 'real_start')
                || null === $updatedEvent->real_start,
            JsonResponse::HTTP_BAD_REQUEST,
            'real_start is required',
        );
        try {
            $event->real_start = new Carbon($updatedEvent->real_start);
        } catch (InvalidFormatException) {
            abort(
                JsonResponse::HTTP_BAD_REQUEST,
                'Invalid date format for real_start',
            );
        }
        if (
            !property_exists($updatedEvent, 'real_end')
            || null === $updatedEvent->real_end
        ) {
            $event->real_end = null;
        } else {
            // If real_end is included, it needs to be a real date.
            try {
                $event->real_end = new Carbon($updatedEvent->real_end);
            } catch (InvalidFormatException) {
                abort(
                    JsonResponse::HTTP_BAD_REQUEST,
                    'Invalid date format for real_end',
                );
            }
            abort_if(
                $event->real_end->lessThan($event->real_start),
                JsonResponse::HTTP_BAD_REQUEST,
                'Event\'s end must be after its beginning',
            );
        }

        // Event's name is required, but defaults to the event's start time.
        $event->name = $updatedEvent->name
            ?? $event->real_start->toDayDateTimeString();
        $event->game_start = $updatedEvent->game_start;
        $event->game_end = $updatedEvent->game_end;
        $event->description = $updatedEvent->description ?? null;
        $event->save();
        return new JsonResponse(
            new EventResource($event),
            JsonResponse::HTTP_ACCEPTED,
        );
    }

    public function store(
        EventPostRequest $request,
        Campaign $campaign,
    ): JsonResponse {
        $user = $request->user();
        abort_if(null === $user, JsonResponse::HTTP_UNAUTHORIZED);
        abort_if(
            null === $campaign->gamemaster || !$campaign->gamemaster->is($user),
            JsonResponse::HTTP_FORBIDDEN,
            'Only the gamemaster can create events for a campaign',
        );
        $event = new Event($request->validated());
        if (null === $request->name) {
            $event->name = (new Carbon($request->real_start))->toDayDateTimeString();
        }
        $event->campaign_id = $campaign->id;
        $event->created_by = $user->id;
        $event->save();
        return new JsonResponse(
            new EventResource($event),
            JsonResponse::HTTP_CREATED,
        );
    }

    public function put(EventPutRequest $request, Event $event): JsonResponse
    {
        $this->authorize('update', $event);
        $event->fill([
            'description' => $request->description,
            'game_end' => $request->game_end,
            'game_start' => $request->game_start,
            'name' => $request->name,
            'real_end' => $request->real_end,
            'real_start' => $request->real_start,
        ]);
        $event->updated_at = now();
        $event->save();
        return new JsonResponse(
            new EventResource($event),
            JsonResponse::HTTP_ACCEPTED,
        );
    }

    public function destroy(Event $event): JsonResponse
    {
        $this->authorize('delete', $event);
        $event->delete();
        return new JsonResponse('', JsonResponse::HTTP_NO_CONTENT);
    }

    public function getRsvp(Request $request, Event $event): JsonResponse
    {
        /** @var User */
        $user = $request->user();
        abort_unless(
            (new EventPolicy())->view($user, $event),
            JsonResponse::HTTP_NOT_FOUND,
        );
        $rsvp = EventRsvp::firstOrCreate(
            ['event_id' => $event->id, 'user_id' => $user->id],
            ['response' => 'tentative'],
        );

        return new JsonResponse(
            new EventRsvpResource($rsvp),
            JsonResponse::HTTP_OK,
        );
    }

    public function deleteRsvp(Request $request, Event $event): JsonResponse
    {
        /** @var User */
        $user = $request->user();
        abort_unless(
            (new EventPolicy())->view($user, $event),
            JsonResponse::HTTP_NOT_FOUND,
        );
        EventRsvp::where('event_id', $event->id)
            ->where('user_id', $user->id)
            ->delete();
        return new JsonResponse('', JsonResponse::HTTP_NO_CONTENT);
    }

    public function updateRsvp(RsvpPutRequest $request, Event $event): JsonResponse
    {
        /** @var User */
        $user = $request->user();
        $rsvp = EventRsvp::updateOrCreate(
            ['event_id' => $event->id, 'user_id' => $user->id],
            ['response' => $request->response],
        );

        return new JsonResponse(
            new EventRsvpResource($rsvp),
            JsonResponse::HTTP_ACCEPTED,
        );
    }
}
