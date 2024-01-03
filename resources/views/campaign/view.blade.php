@php
use App\Models\Event;
use App\Policies\EventPolicy;
use Illuminate\Support\Facades\View;

$eventPolicy = new EventPolicy();
@endphp
<x-app>
    <x-slot name="title">Campaign: {{ $campaign }}</x-slot>
    <x-slot name="navbar">
        <li class="nav-item">
            <a class="nav-link" href="/dashboard">Home</a>
        </li>
        <li class="nav-item">
            <span class="nav-link active">{{ $campaign }}</span>
        </li>
    </x-slot>

    <h1>
        {{ $campaign }}
        @if ($user->is($campaign->gamemaster) && View::exists(sprintf('%s.gm-screen', ucfirst($campaign->system))))
            <a class="btn btn-primary" href="{{ route('campaign.gm-screen', $campaign) }}">
                Launch GM screen
            </a>
        @endif
    </h1>

    <p>
        A {{ $campaign->getSystem() }} campaign.
        @if ($campaign->gamemaster && $campaign->gamemaster->id === $campaign->registeredBy->id)
            Registered and run by {{ $campaign->gamemaster->name }}.
        @elseif ($campaign->gamemaster && $campaign->registeredBy)
            Registered by {{ $campaign->registeredBy->name }} and run by {{ $campaign->gamemaster->name }}.
        @else
            Registered by {{ $campaign->registeredBy->name }} with no gamemaster yet.
        @endif
    </p>

    @if ($campaign->description)
        <p>{{ $campaign->description }}</p>
    @endif

    <div class="row mt-4">
        <div class="col">
            <h2>Players</h2>

            <ul class="list-group">
                @forelse ($campaign->users as $player)
                    @if ('accepted' === $player->pivot->status)
                        <li class="list-group-item">
                            <i class="bi bi-person"></i>
                            {{ $player->name }} <small>&lt;{{ $player->email }}&gt;</small>
                        </li>
                    @else
                        <li class="list-group-item text-muted">
                            <i class="bi bi-person"></i>
                            {{ $player->name }} <small>&lt;{{ $player->email }}&gt;</small>
                            ({{ $player->pivot->status }})
                        </li>
                    @endif
                @empty
                    <li class="list-group-item">Campaign has no players</li>
                @endforelse
                <li class="list-group-item"><a href="#">
                    <i class="bi bi-person-plus"></i>
                    Invite player
                </a></li>
            </ul>

            <h2 class="mt-4">Chat servers</h2>

            <ul class="list-group">
                @forelse ($campaign->channels as $channel)
                    <li class="list-group-item">
                        <i class="bi bi-{{ $channel->type }}"></i>
                        {{ $channel->server_name ?? $channel->server_id }}
                        - #{{ $channel->channel_name ?? $channel->channel_id }}
                        @if ('slack' === $channel->type)
                            <button class="btn btn-link float-end" type="button">
                            <i class="bi bi-check-square-fill text-success"
                                title="Slack channels do not require web hooks"></i>
                            </button>
                        @elseif ($channel->webhook)
                            <button class="btn btn-link float-end" type="button">
                            <i class="bi bi-check-square-fill text-success"
                                title="{{ $channel->webhook }}"></i>
                            </button>
                        @else
                            <button class="btn btn-link float-end"
                                data-bs-channel-id="{{ $channel->id }}"
                                data-bs-channel-name="{{ $channel->channel_name }}"
                                data-bs-target="#add-webhook-{{ $channel->type }}"
                                data-bs-toggle="modal" type="button">
                                <i class="bi bi-question-square-fill text-danger"></i>
                            </button>
                        @endif
                        <ul>
                        @forelse ($channel->characters() as $character)
                            <li>{{ $character }}</li>
                        @empty
                            <li>No characters</li>
                        @endforelse
                        </ul>
                    </li>
                @empty
                    <li class="list-group-item">Campaign has no channels</li>
                @endforelse
                @if (Auth::user()->id === $campaign->gm || Auth::user()->id === $campaign->registered_by)
                <li class="list-group-item"><small class="text-muted">
                    To link a channel to this campaign, type
                    <code>/roll campaign {{ $campaign->id }}</code>.
                </small></li>
                @endif
            </ul>

            <h2 class="mt-4">Upcoming events</h2>

            <ul class="list-group" id="upcoming-events">
            @php
                $upcomingEvents = Event::forCampaign($campaign)
                    ->future()
                    ->with(['responses'])
                    ->get();
            @endphp
            @forelse ($upcomingEvents as $event)
                <li class="list-group-item">
                    @if ($eventPolicy->delete($user, $event))
                    <button class="btn btn-outline-danger btn-sm float-end" data-id="{{ $event->id }}" type="button">
                        <i class="bi bi-trash3"></i>
                    </button>
                    @endif
                    <div class="fs-4">{{ $event }}</div>
                    <div class="fs-6 text-muted">{{ $event->real_start->toRfc7231String() }}</div>
                    @if (null !== $event->description)
                    <div>{{ $event->description }}</div>
                    @endif
                    <ul>@forelse ($event->responses as $response)
                        <li>{{ $response->user->name }}: {{ ucfirst($response->response) }}</li>
                    @empty
                        <li>No responses</li>
                    @endforelse
                    </ul>
                </li>
            @empty
                <li class="list-group-item" id="no-events">No upcoming events</li>
            @endforelse
            @if ($eventPolicy->createForCampaign($user, $campaign))
                <li class="list-group-item" id="new-event-row">
                    <button class="btn btn-link btn-sm"
                        data-bs-target="#add-event" data-bs-toggle="modal"
                        type="button">
                        <i class="bi bi-calendar-plus"></i>
                        Schedule an event
                    </button>
                </li>
            @endif
            </ul>
        </div>

        <div class="col">
            <h2>Characters</h2>

            <ul class="list-group mb-3">
            @forelse ($campaign->characters() as $character)
                <li class="list-group-item">
                    <a href="/characters/{{ $character->system }}/{{ $character->id }}">
                        <i class="bi bi-file-earmark-person"></i>
                        {{ $character }}</a>
                    ({{ $character->user()->name }})
                </li>
            @empty
                <li class="list-group-item">
                    The campaign has no characters.
                </li>
            @endforelse
            </ul>

            <x-campaign-options :campaign=$campaign/>
        </div>
    </div>

    <div aria-hidden="true" aria-labelledby="add-webhook-discord-label"
        class="modal fade" id="add-webhook-discord" tabindex="-1">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="add-webhook-discord-label">
                        Adding a Discord webhook
                    </h5>
                    <button aria-label="Close" class="btn-close"
                        data-bs-dismiss="modal" type="button"></button>
                </div>
                <div class="modal-body">
                    <p>
                    Webhooks allow {{ config('app.name') }} to send messages to
                    chat servers on behalf of players and the GM. It can act as
                    a bridge from Slack to Discord, or different servers of the
                    same type. It allows users to use {{ config('app.name') }}'s
                    web app to roll dice and have the results posted to the chat
                    channel. {{ config('app.name') }} can automatically add the
                    webhook for Discord servers. Would you like to add a web
                    hook for <code id="webhook-discord-channel-name"></code>?
                    </p>

                    <p class="discord-manual d-none">
                        To find the webhook URL, click the gear next to the
                        channel you want to integrate {{ config('app.name') }}
                        into.<br>
                        <img alt="Screenshot showing where the gear is"
                            src="/images/discord-setup-gear.png">
                    </p>

                    <p class="discord-manual d-none">
                        Then click "Integrations".<br>
                        <img alt="Screenshot showing the integrations menu option"
                            src="/images/discord-setup-integrations.png">
                    </p>

                    <p class="discord-manual d-none">
                        Next, click "View webhooks".<br>
                        <img alt="Screenshot showing the View Webhooks option"
                            src="/images/discord-setup-webhooks.png">
                    </p>

                    <p class="discord-manual d-none">
                        Then, click the "Create Webhook" button.<br>
                        <img alt="Screenshot showing the Create Webhook button"
                            src="/images/discord-setup-create-webhook.png">
                    </p>

                    <p class="discord-manual d-none">
                        Give the integration a name. We obviously recommend
                        {{ config('app.name') }}.<br>
                        <img alt="Screenshot showing the webhook info screen"
                            src="/images/discord-setup-rename.png">
                        <br>
                        Also, you can upload an image. We suggest the
                        {{ config('app.name') }} logo:<br>
                        <img alt="Commlink logo" src="/images/commlink.png">
                    </p>

                    <p class="discord-manual d-none">
                        Click the "Copy Webhook URL" button, and paste it below.
                        If you renamed the integration or uploaded a logo,
                        you'll need to click the "Save Changes" button.<br>
                        <img alt="Screenshot showing the Copy Webhook URL button"
                            src="/images/discord-setup-copy-url.png">
                    </p>

                    <div class="discord-manual d-none row mt-4">
                        <label class="col-4 col-form-label" for="slack-webhook">Webhook URL</label>
                        <div class="col-8">
                            <input class="form-control" id="discord-webhook" type="url">
                            <div class="invalid-feedback">
                                Invalid webhook URL.
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer" id="add-webhook-discord-footer-initial">
                    <button class="btn btn-link" data-bs-dismiss="modal"
                        type="button">
                        No thanks
                    </button>
                    <button class="btn btn-secondary"
                        id="add-webhook-discord-manual" type="button">
                        Manual entry
                    </button>
                    <button class="btn btn-primary"
                        id="add-webhook-discord-auto" type="button">
                        Hook it up for me!
                    </button>
                </div>
                <div class="modal-footer discord-manual d-none">
                    <button class="btn btn-link" data-bs-dismiss="modal"
                        type="button">
                        Cancel
                    </button>
                    <button class="btn btn-primary" type="button">
                        Add webhook
                    </button>
                </div>
            </div>
        </div>
    </div>

    <div aria-hidden="true" aria-labelledby="add-event-title"
        class="modal fade" id="add-event" tabindex="-1">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h1 class="modal-title fs-5" id="add-event-title">
                        Schedule an event
                    </h1>
                    <button aria-label="Close" class="btn-close"
                        data-bs-dismiss="modal" type="button"></button>
                </div>
                <form action="#" class="needs-validation" id="event-form" novalidate>
                <div class="modal-body">
                    <div class="mb-3">
                        <label for="event-start" class="form-label">Start (required)</label>
                        <input aria-describedby="event-start-help"
                            class="form-control" id="event-start" required
                            type="datetime-local">
                        <div class="invalid-feedback">Start is required</div>
                        <div class="form-text" id="event-start-help">
                            Date and time for the event, in your local time
                            zone.
                        </div>
                    </div>

                    <div class="mb-3">
                        <label for="event-end" class="form-label">End</label>
                        <input aria-describedby="event-end-help"
                            class="form-control" id="event-end"
                            type="datetime-local">
                        <div class="form-text" id="event-end-help">
                            Date and time the event is scheduled to end, in your
                            local time zone.
                        </div>
                    </div>

                    <div class="mb-3">
                        <label for="event-name" class="form-label">Event name</label>
                        <input aria-describedby="event-name-help"
                            class="form-control" id="event-name">
                        <div class="form-text" id="event-name-help">
                            Optional name for the event, like Session Zero or a
                            creative name for what the table will be doing. If
                            left blank will default to the session's start time.
                        </div>
                    </div>

                    <div class="mb-3">
                        <label for="event-description" class="form-label">Description</label>
                        <textarea aria-describedby="event-description-help"
                            class="form-control" id="event-description"></textarea>
                        <div class="form-text" id="event-description-help">
                            Optional description of the event. You might
                            describe what happened previously, what to expect in
                            this session, what to bring to a physical game, etc.
                        </div>
                    </div>

                    <div class="mb-3">
                        <input checked class="form-check-input"
                            id="event-attending" type="checkbox" value="true">
                        <label class="form-check-label" for="event-attending">
                            I'm attending
                        </label>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                    <button type="submit" class="btn btn-primary">Save changes</button>
                </div>
                </form>
            </div>
        </div>
    </div>

    <x-slot name="javascript">
        <script>
            const campaignId = {{ $campaign->id }};
            const discordWebhookFailed = function (xhr, status, errorThrown) {
                let message;
                switch (errorThrown) {
                    case 'Forbidden':
                        message = 'You don\'t have permission to change that channel!';
                        break;
                    case 'Not Found':
                        message = 'That channel no longer seems to exist.';
                        break;
                    case 'Unprocessable Entity':
                        message = [];
                        $.each(xhr.responseJSON.errors, function (field, errorBag) {
                            $.each(errorBag, function (key, error) {
                                message.push(error);
                            });
                        });
                        message = message.join('<br>');
                        break;
                    default:
                        message = 'An unknown error has occurred: ' + errorThrown;
                        break;
                }
                $('#add-webhook-discord .modal-body').prepend(
                    '<div class="alert alert-danger alert-dismissible fade show" role="alert">'
                        + message
                        + '<button type="button" class="btn-close" '
                        + 'data-bs-dismiss="alert" aria-label="Close"></button>'
                        + '</div>'
                );
            };
            const discordWebhookSucceeded = function (data, status, xhr) {
                const id = $('#add-webhook-discord').data('bs-channel-id');
                $('#add-webhook-discord .modal-body .alert').remove();
                $('button[data-bs-channel-id="' + id + '"]').replaceWith(
                    '<button class="btn btn-link float-end">'
                    + '<i class="bi bi-check-square-fill text-success"></i>'
                    + '</button>'
                );
                $('#add-webhook-discord').hide();
                $('.modal-backdrop').remove();
            };

            $('#add-webhook-discord').on('show.bs.modal', function (event) {
                let button = $(event.relatedTarget);
                $('#webhook-discord-channel-name').html(button.data('bs-channel-name'));
                $('#add-webhook-discord').data('bs-channel-id', button.data('bs-channel-id'));
                $('#add-webhook-discord-footer-initial').removeClass('d-none');
                $('.discord-manual').addClass('d-none');
            });

            $('#add-webhook-discord').on('hidden.bs.modal', function () {
                $('#add-webhook-discord-footer-initial').removeClass('d-none');
                $('.discord-manual').addClass('d-none');
                $('#add-webhook-discord .modal-dialog').removeClass('modal-xl');
            });

            $('#add-webhook-discord-auto').on('click', function (event) {
                $.ajax({
                    data: {
                        auto: 1
                    },
                    error: discordWebhookFailed,
                    headers: {
                        'X-CSRF-TOKEN': '{{ csrf_token() }}'
                    },
                    method: 'PATCH',
                    success: discordWebhookSucceeded,
                    url: '/api/channels/' + $('#add-webhook-discord').data('bs-channel-id')
                });
            });
            $('.discord-manual .btn-primary').on('click', function (event) {
                $.ajax({
                    data: {
                        webhook: $('#discord-webhook').val()
                    },
                    error: discordWebhookFailed,
                    headers: {
                        'X-CSRF-TOKEN': '{{ csrf_token() }}'
                    },
                    method: 'PATCH',
                    success: discordWebhookSucceeded,
                    url: '/api/channels/' + $('#add-webhook-discord').data('bs-channel-id')
                });
            });
            $('#add-webhook-discord-manual').on('click', function (event) {
                $('#add-webhook-discord-footer-initial').addClass('d-none');
                $('.discord-manual').removeClass('d-none');
                $('#add-webhook-discord .modal-dialog').addClass('modal-xl');
            });

            const createEventFailed = function (xhr, status, errorThrown) {
                let message;
                switch (errorThrown) {
                    case 'Forbidden':
                        message = 'You don\'t have permission to add events!';
                        break;
                    case 'Not Found':
                        message = 'The campaign doesn\'t seem to exist...';
                        break;
                    case 'Unprocessable Entity':
                        message = [];
                        $.each(xhr.responseJSON.errors, function (field, errorBag) {
                            $.each(errorBag, function (key, error) {
                                message.push(error);
                            });
                        });
                        message = message.join('<br>');
                        break;
                    default:
                        message = 'An unknown error has occurred: ' + errorThrown;
                        break;
                }
                $('#add-event .modal-body').prepend(
                    '<div class="alert alert-danger alert-dismissible fade show" role="alert">'
                        + message
                        + '<button type="button" class="btn-close" '
                        + 'data-bs-dismiss="alert" aria-label="Close"></button>'
                        + '</div>'
                );
            };
            const createEventSucceeded = function (response, status, xhr) {
                $('#add-event .modal-body .alert').remove();
                $('#event-description').val('');
                $('#event-name').val('');
                $('#event-start').val('');
                $('#event-end').val('');
                $('#no-events').remove();
                const event = response.data;
                let real_start = new Date(event.real_start);
                real_start = real_start.toUTCString();

                let html = '<li class="list-group-item">'
                    + '<button class="btn btn-outline-danger btn-sm float-end" '
                    + 'data-id="' + event.id + '" type="button">'
                    + '<i class="bi bi-trash3"></i>'
                    + '</button>'
                    + '<div class="fs-4">' + event.name + '</div>'
                    + '<div class="fs-6 text-muted">' + real_start + '</div>';
                if (null !== event.description) {
                    html += '<div>' + event.description + '</div>';
                }
                html += '<ul>';
                if ($('#event-attending').prop('checked')) {
                    html += '<li>{{ $user->name }}: Accepted</li>';
                    $.ajax({
                        data: {
                            response: 'accepted'
                        },
                        headers: {
                            'X-CSRF-TOKEN': '{{ csrf_token() }}'
                        },
                        method: 'PUT',
                        url: '/api/events/' + event.id + '/rsvp'
                    });
                } else {
                    html += '<li>No responses</li>';
                }
                html += '</ul></li>';
                $('#new-event-row').before(html);
                $('#event-attending').prop('checked', true);
            };
            $('#event-form').on('submit', function (event) {
                event.preventDefault();
                event.stopPropagation();
                const form = $('#event-form');
                form.addClass('was-validated');
                if (!form[0].checkValidity()) {
                    return;
                }
                form.removeClass('was-validated');
                let name = $('#event-name').val().trim();
                const start = $('#event-start').val();
                if ('' === name) {
                    name = new Date(start);
                    name = name.toUTCString();
                }
                $.ajax({
                    data: {
                        description: $('#event-description').val(),
                        name: name,
                        real_start: start,
                        real_end: $('#event-end').val()
                    },
                    error: createEventFailed,
                    headers: {
                        'X-CSRF-TOKEN': '{{ csrf_token() }}'
                    },
                    method: 'POST',
                    success: createEventSucceeded,
                    url: '/api/campaigns/' + campaignId + '/events'
                });
            });

            const deleteEventFailed = function (xhr, status, errorThrown) {
                let message;
                switch (errorThrown) {
                    case 'Forbidden':
                        message = 'You don\'t have permission to delete events!';
                        break;
                    case 'Not Found':
                        message = 'The event doesn\'t seem to exist...';
                        break;
                    default:
                        message = 'An unknown error has occurred: ' + errorThrown;
                        break;
                }
                $('#add-event .modal-body').prepend(
                    '<div class="alert alert-danger alert-dismissible fade show" role="alert">'
                        + message
                        + '<button type="button" class="btn-close" '
                        + 'data-bs-dismiss="alert" aria-label="Close"></button>'
                        + '</div>'
                );
            };
            const deleteEventSucceeded = function (response, status, xhr) {
                let id = this.url.replace('/api/events/', '');
                $('#upcoming-events .btn-outline-danger[data-id="' + id + '"]')
                    .parent('li')
                    .remove();
                const list = $('#upcoming-events > li');
                if (1 === list.length) {
                    $('#upcoming-events').prepend('<li class="list-group-item" id="no-events">No upcoming events</li>');
                }
            };
            $('#upcoming-events').on('click', '.btn-outline-danger', function (event) {
                let el = $(event.target);
                if ('I' === el[0].nodeName) {
                    el = el.parent('button');
                }
                $.ajax({
                    error: deleteEventFailed,
                    headers: {
                        'X-CSRF-TOKEN': '{{ csrf_token() }}'
                    },
                    method: 'DELETE',
                    success: deleteEventSucceeded,
                    url: '/api/events/' + el.data('id')
                });
            });
        </script>
    </x-slot>
</x-app>
