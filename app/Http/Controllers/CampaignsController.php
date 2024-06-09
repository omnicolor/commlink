<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Events\CampaignCreated;
use App\Http\Requests\CampaignCreateRequest;
use App\Http\Requests\CampaignInvitationCreateRequest;
use App\Http\Requests\CampaignInvitationResponseRequest;
use App\Http\Resources\CampaignCollection;
use App\Http\Resources\CampaignInvitationResource;
use App\Http\Resources\CampaignResource;
use App\Models\Campaign;
use App\Models\CampaignInvitation;
use App\Models\Initiative;
use App\Models\Shadowrun5e\Character;
use App\Models\Shadowrun5e\Grunt;
use App\Models\User;
use Carbon\CarbonImmutable;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Validator;
use Illuminate\View\View;
use Rs\Json\Patch;
use Rs\Json\Patch\InvalidOperationException;
use Rs\Json\Pointer\InvalidPointerException;
use TypeError;

use function max;
use function sprintf;

class CampaignsController extends Controller
{
    public function createForm(): View
    {
        return view('campaign.create');
    }

    /**
     * @psalm-suppress InvalidReturnStatement
     * @psalm-suppress InvalidReturnType
     */
    public function create(CampaignCreateRequest $request): RedirectResponse
    {
        $campaign = new Campaign($request->only([
            'description',
            'name',
            'system',
        ]));
        // @phpstan-ignore-next-line
        $campaign->registered_by = Auth::user()->id;

        switch ($request->input('system')) {
            case 'avatar':
                $options = [
                    'era' => $request->input('avatar-era'),
                    'scope' => $request->input('avatar-scope'),
                    'focus' => $request->input('avatar-focus'),
                    'focusDetails' => $request->input('avatar-focus-details'),
                ];
                if (null !== $request->input('avatar-focus')) {
                    $options['focusObject'] = $request->input(sprintf(
                        'avatar-focus-%s-object',
                        $request->input('avatar-focus')
                    ));
                }
                $campaign->options = $options;
                break;
            case 'cyberpunkred':
                $campaign->options = [
                    'nightCityTarot' => $request->boolean('night-city-tarot'),
                ];
                break;
            case 'shadowrun5e':
                $campaign->options = [
                    'creation' => (array)$request->input('sr5e-creation'),
                    'gameplay' => (string)$request->input('sr5e-gameplay'),
                    'rulesets' => (array)$request->input('sr5e-rules'),
                    'startDate' => (string)$request->input('sr5e-start-date'),
                ];
                break;
            case 'subversion':
                $campaign->options = [
                    'community-description' => $request->input('subversion-community-description'),
                    'community-type' => $request->input('subversion-community-type'),
                ];
                break;
        }
        $campaign->save();
        CampaignCreated::dispatch($campaign);
        return redirect('dashboard');
    }

    /**
     * @psalm-suppress InvalidReturnStatement
     * @psalm-suppress InvalidReturnType
     */
    public function destroy(Campaign $campaign): Response
    {
        Gate::authorize('delete', $campaign);
        $campaign->delete();
        return response('', Response::HTTP_NO_CONTENT);
    }

    /**
     * Launch the GM screen.
     * @psalm-suppress NoValue
     */
    public function gmScreen(Campaign $campaign): View
    {
        Gate::authorize('gm', $campaign);
        switch ($campaign->system) {
            case 'cyberpunkred':
                return view(
                    'Cyberpunkred.gm-screen',
                    [
                        'campaign' => $campaign,
                        // @phpstan-ignore-next-line
                        'initiative' => Initiative::forCampaign($campaign)
                            ->orderByDesc('initiative')
                            ->get(),
                        'user' => Auth::user(),
                    ]
                );
            case 'shadowrun5e':
                $characters = $campaign->characters();
                // Figure out the what the largest monitor row will be.
                $maxMonitor = 0;
                /** @var Character $character */
                foreach ($characters as $character) {
                    $maxMonitor = max(
                        $maxMonitor,
                        $character->physical_monitor,
                        $character->stun_monitor,
                        $character->overflow_monitor,
                        $character->edge
                    );
                }

                return view(
                    'Shadowrun5e.gm-screen',
                    [
                        'campaign' => $campaign,
                        'characters' => $characters,
                        'grunts' => Grunt::all(),
                        // @phpstan-ignore-next-line
                        'initiative' => Initiative::forCampaign($campaign)
                            ->orderByDesc('initiative')
                            ->get(),
                        'max_monitor' => $maxMonitor,
                        'user' => Auth::user(),
                    ]
                );
            default:
                return abort(Response::HTTP_NOT_FOUND);
        }
    }

    public function index(Request $request): JsonResource
    {
        /** @var User */
        $user = $request->user();

        // Campaigns the user plays in.
        $campaigns = $user->campaigns()
            ->wherePivotIn('status', ['accepted', 'invited'])
            ->get();
        $campaigns = $campaigns->merge($user->campaignsGmed);
        $campaigns = $campaigns->merge($user->campaignsRegistered);
        return new CampaignCollection($campaigns);
    }

    /**
     * Handle a GM or registrant inviting a player to a campaign.
     */
    public function invite(
        Campaign $campaign,
        CampaignInvitationCreateRequest $request,
    ): JsonResource | JsonResponse {
        abort_if(
            // @phpstan-ignore-next-line
            CampaignInvitation::where('campaign_id', $campaign->id)
                ->where('email', $request->email)
                // CampaignInviationCreateRequest verified that the requesting
                // user is logged in.
                // @phpstan-ignore-next-line
                ->where('invited_by', $request->user()->id)
                ->exists(),
            Response::HTTP_CONFLICT,
            'You have already invited that user',
        );

        $user = User::where('email', $request->email)->first();
        if (null === $user) {
            // User is new to Commlink.
            $invitation = CampaignInvitation::create([
                'campaign_id' => $campaign->id,
                'email' => $request->email,
                // CampaignInviationCreateRequest verified that the requesting
                // user is logged in.
                // @phpstan-ignore-next-line
                'invited_by' => $request->user()->id,
                'name' => $request->name,
                'updated_at' => null,
            ]);
            return (new CampaignInvitationResource($invitation))
                ->additional(['meta' => ['status' => 'new']]);
        }

        // User already has a Commlink account. Have they already been invited
        // to this campaign?
        $player = $campaign->users->find($user->id);
        abort_if(
            'invited' === $player?->pivot?->status,
            Response::HTTP_CONFLICT,
            'That user has already been invited',
        );
        // or are they already playing?
        abort_if(
            'accepted' === $player?->pivot?->status,
            Response::HTTP_CONFLICT,
            'That user has already joined the campaign',
        );
        // Or are they the GM?
        abort_if(
            $user->is($campaign->gamemaster),
            Response::HTTP_BAD_REQUEST,
            'You can\'t invite the GM to play',
        );

        $campaign->users()->attach(
            $user->id,
            ['status' => CampaignInvitation::INVITED]
        );
        $invitation = new CampaignInvitation([
            'campaign_id' => $campaign->id,
            'email' => $request->email,
            // @phpstan-ignore-next-line
            'invited_by' => $request->user()->id,
            'name' => $user->name,
        ]);

        return (new CampaignInvitationResource($invitation))
            ->additional([
                'meta' => [
                    'status' => 'existing',
                    'user' => [
                        'id' => $user->id,
                        'name' => $user->name,
                    ],
                ],
            ])
            ->response()
            ->setStatusCode(Response::HTTP_CREATED);
    }

    public function patch(Campaign $campaign, Request $request): JsonResponse
    {
        $this->authorize('update', $campaign);
        return match ($request->headers->get('Content-Type')) {
            'application/json' => $this->patchData($campaign, $request),
            'application/json-patch+json' => $this->patchJson($campaign, $request),
            default => new JsonResponse(
                sprintf(
                    'Unacceptable Content-Type: %s',
                    $request->headers->get('Content-Type'),
                ),
                JsonResponse::HTTP_UNSUPPORTED_MEDIA_TYPE,
            ),
        };
    }

    public function patchData(Campaign $campaign, Request $request): JsonResponse
    {
        $validator = Validator::make(
            $request->all(),
            [
                'currentDate' => [
                    'date',
                    'nullable',
                    'string',
                ],
            ],
        );
        abort_if(
            $validator->fails(),
            JsonResponse::HTTP_UNPROCESSABLE_ENTITY,
            $validator->errors()->first('current_date'),
        );

        $options = $campaign->options ?? [];
        $options['currentDate'] = $request->currentDate;
        $campaign->options = $options;
        $campaign->save();
        if (null !== $request->currentDate) {
            $formattedDate = (new CarbonImmutable($request->currentDate))
                ->format('l, F jS Y');
        } else {
            $formattedDate = 'no date set';
        }

        return (new CampaignResource($campaign))
            ->additional([
                'data' => [
                    'formatted_date' => $formattedDate,
                ],
            ])
            ->response()
            ->setStatusCode(Response::HTTP_ACCEPTED);
    }

    public function patchJson(Campaign $campaign, Request $request): JsonResponse
    {
        $document = $campaign->toJson();
        $patch = (string)json_encode($request->input());
        try {
            $updatedCampaign = json_decode(
                (new Patch($document, $patch))->apply()
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
                    . '. Valid pointer values are: /options/current_date'
            );
        }
        $campaign->options = $updatedCampaign->options;
        $campaign->save();

        $formattedDate = $campaign->options['currentDate'] ?? null;
        if (null !== $formattedDate) {
            $formattedDate = (new CarbonImmutable($formattedDate))
                ->format('l, F jS Y');
        } else {
            $formattedDate = 'no date set';
        }

        return (new CampaignResource($campaign))
            ->additional([
                'data' => [
                    'formatted_date' => $formattedDate,
                ],
            ])
            ->response()
            ->setStatusCode(Response::HTTP_ACCEPTED);
    }

    /**
     * Allow a player to respond to a GM or registrant invitation to a table.
     * @psalm-suppress InvalidReturnStatement
     * @psalm-suppress InvalidReturnType
     */
    public function respond(
        Campaign $campaign,
        CampaignInvitationResponseRequest $request,
    ): RedirectResponse {
        /** @var User */
        $user = $request->user();

        $campaign->users()->detach($user->id);
        $campaign->users()->attach($user->id, ['status' => $request->response]);
        if ('removed' === $request->response) {
            return redirect('dashboard');
        }
        return redirect(route('campaign.view', $campaign));
    }

    public function respondAccept(
        Campaign $campaign,
        CampaignInvitation $invitation,
        string $token,
    ): View {
        abort_if(
            $invitation->hash() !== $token,
            Response::HTTP_FORBIDDEN,
            'The token does not appear to be valid for the invitation',
        );
        abort_if(
            CampaignInvitation::INVITED !== $invitation->status,
            Response::HTTP_BAD_REQUEST,
            'It appears you\'ve already responded to the invitation',
        );
        return view(
            'campaign.Invitation.accept',
            [
                'campaign' => $campaign,
                'change_url' => route(
                    'campaign.invitation-change',
                    [
                        'campaign' => $campaign,
                        'invitation' => $invitation->id,
                        'token' => $token,
                    ],
                ),
                'decline_url' => route(
                    'campaign.invitation-decline',
                    [
                        'campaign' => $campaign,
                        'invitation' => $invitation->id,
                        'token' => $token,
                    ],
                ),
                'spam_url' => route(
                    'campaign.invitation-spam',
                    [
                        'campaign' => $campaign,
                        'invitation' => $invitation->id,
                        'token' => $token,
                    ],
                ),
                'invitation' => $invitation,
            ],
        );
    }

    /**
     * @psalm-suppress PossiblyUnusedParam
     */
    public function respondDecline(
        Campaign $campaign,
        CampaignInvitation $invitation,
        string $token,
    ): View {
        abort_if(
            $invitation->hash() !== $token,
            Response::HTTP_FORBIDDEN,
            'The token does not appear to be valid for the invitation',
        );
        abort_if(
            CampaignInvitation::INVITED !== $invitation->status,
            Response::HTTP_BAD_REQUEST,
            'It appears you\'ve already responded to the invitation',
        );

        $invitation->status = CampaignInvitation::RESPONDED;
        $invitation->updated_at = $invitation->responded_at = now()->toDateTimeString();
        $invitation->save();

        return view(
            'campaign.Invitation.decline',
            [
                'campaign' => $campaign,
                'invitation' => $invitation,
            ],
        );
    }

    public function respondSpam(
        Campaign $campaign,
        CampaignInvitation $invitation,
        string $token,
    ): View {
        abort_if(
            $invitation->hash() !== $token,
            Response::HTTP_FORBIDDEN,
            'The token does not appear to be valid for the invitation',
        );
        abort_if(
            CampaignInvitation::INVITED !== $invitation->status,
            Response::HTTP_BAD_REQUEST,
            'It appears you\'ve already responded to the invitation',
        );

        $invitation->status = CampaignInvitation::SPAM;
        $invitation->updated_at = $invitation->responded_at = now()->toDateTimeString();
        $invitation->save();

        return view(
            'campaign.Invitation.spam',
            [
                'campaign' => $campaign,
                'invitation' => $invitation,
            ],
        );
    }

    public function respondChangeEmail(
        Campaign $campaign,
        CampaignInvitation $invitation,
        string $token,
    ): View {
        abort_if(
            $invitation->hash() !== $token,
            Response::HTTP_FORBIDDEN,
            'The token does not appear to be valid for the invitation',
        );
        abort_if(
            CampaignInvitation::INVITED !== $invitation->status,
            Response::HTTP_BAD_REQUEST,
            'It appears you\'ve already responded to the invitation',
        );
        return view(
            'campaign.Invitation.change-email',
            [
                'campaign' => $campaign,
                'accept_url' => route(
                    'campaign.invitation-accept',
                    [
                        'campaign' => $campaign,
                        'invitation' => $invitation->id,
                        'token' => $token,
                    ],
                ),
                'decline_url' => route(
                    'campaign.invitation-decline',
                    [
                        'campaign' => $campaign,
                        'invitation' => $invitation->id,
                        'token' => $token,
                    ],
                ),
                'spam_url' => route(
                    'campaign.invitation-spam',
                    [
                        'campaign' => $campaign,
                        'invitation' => $invitation->id,
                        'token' => $token,
                    ],
                ),
                'invitation' => $invitation,
            ],
        );
    }

    public function show(Campaign $campaign): JsonResource
    {
        Gate::authorize('view', $campaign);
        return new CampaignResource($campaign);
    }

    public function view(Campaign $campaign): View
    {
        Gate::authorize('view', $campaign);
        return view(
            'campaign.view',
            [
                'campaign' => $campaign,
                'user' => Auth::user(),
            ]
        );
    }
}
