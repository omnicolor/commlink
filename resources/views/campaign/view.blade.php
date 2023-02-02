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
        @if ($user->is($campaign->gamemaster) && \Illuminate\Support\Facades\View::exists(sprintf('%s.gm-screen', ucfirst($campaign->system))))
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
                @forelse ($campaign->users as $user)
                    @if ('accepted' === $user->pivot->status)
                        <li class="list-group-item">
                            <i class="bi bi-person"></i>
                            {{ $user->name }} <small>&lt;{{ $user->email }}&gt;</small>
                        </li>
                    @else
                        <li class="list-group-item text-muted">
                            <i class="bi bi-person"></i>
                            {{ $user->name }} <small>&lt;{{ $user->email }}&gt;</small>
                            ({{ $user->pivot->status }})
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
        </div>
        <div class="col">
            <h2>Characters</h2>

            <ul class="list-group">
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
        </div>
    </div>

    <div class="row mt-4">
        <div class="col">
            <h2>Chat servers</h2>

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
        </div>
        <div class="col">
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

    <x-slot name="javascript">
        <script>
            const discordWebhookFailed = function (xhr, status, errorThrown) {
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
        </script>
    </x-slot>
</x-app>
