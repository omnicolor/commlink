<x-app>
    <x-slot name="title">
        Settings
    </x-slot>

    @if(session('successObj'))
    <div class="alert alert-success alert-dismissible fade mt-4 show" id="{{ session('successObj')['id'] }}" role="alert">
        {{ session('successObj')['message'] }}
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>
    @endif

    @if(session('success'))
    <div class="alert alert-success mt-4">
        {{ session('success') }}
    </div>
    @endif

    @if ($errors->any())
    <div class="alert alert-danger mt-4">
        There were problems saving your settings.
    </div>
    @endif

    @if(session('error'))
    <div class="alert alert-danger mt-4">
        {{ session('error') }}
    </div>
    @endif

    <h1>Settings</h1>

    <div class="row mt-4">
        <div class="col">
            <h2>Linked chat users</h2>
            <table class="table">
                <thead>
                    <tr>
                        <th scope="col">Type</th>
                        <th scope="col">Server ID</th>
                        <th scope="col">Server name</th>
                        <th scope="col">User ID</th>
                        <th scope="col">User name</th>
                        <th scope="col">Status</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($user->chatUsers->sortByDesc('verified') as $chatUser)
                        <tr>
                            <td>
                                <i class="bi bi-{{ $chatUser->server_type }}"></i>
                            </td>
                            <td>{{ $chatUser->server_id }}</td>
                            <td>
                                @if ($chatUser->server_name)
                                    {{ $chatUser->server_name }}
                                @else
                                    <small class="text-muted">
                                        Unable to load name
                                    </small>
                                @endif
                            </td>
                            <td>{{ $chatUser->remote_user_id }}</td>
                            <td>
                                @if ($chatUser->remote_user_name)
                                    {{ $chatUser->remote_user_name }}
                                @else
                                    <small class="text-muted">
                                        Unable to load name
                                    </small>
                                @endif
                            </td>
                            @if ($chatUser->verified)
                                <td title="User link is verified">
                                    <i class="bi bi-check-square-fill text-success"></i>
                                </td>
                            @else
                                <td id="{{ $chatUser->server_type }}-{{ $chatUser->server_id }}-{{ $chatUser->remote_user_id }}"
                                    title="User link is not verified">
                                    <div class="input-group">
                                        <span class="input-group-text">
                                            <i class="bi bi-question-square-fill text-danger"></i>
                                        </span>
                                        <span class="input-group-text user-select-all">
                                            /roll validateUser {{ $chatUser->verification }}
                                        </span>
                                        <button class="btn btn-outline-secondary copy-btn" type="button">
                                            <i class="bi bi-clipboard"></i>
                                        </button>
                                    </div>
                                </td>
                            @endif
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6">
                                You don't have any linked chat users!
                            </td>
                        </tr>
                    @endforelse
                </tbody>
                <tfoot>
                    <tr>
                        <td colspan="6">
                            <button class="btn btn-primary"
                                    data-bs-target="#link-user"
                                    data-bs-toggle="modal" type="button">
                                Link a user
                            </button>
                        </td>
                    </tr>
                </tfoot>
            </table>
        </div>
    </div>

    <div class="row mt-4">
        <div class="col">
            <h2>Linked channels</h2>
            <table class="table">
                <thead>
                    <tr>
                        <th scope="col">Type</th>
                        <th scope="col">Server ID</th>
                        <th scope="col">Server name</th>
                        <th scope="col">Channel ID</th>
                        <th scope="col">Channel name</th>
                        <th scope="col">Character</th>
                        <th scope="col">Campaign</th>
                        <th scope="col">System</th>
                        <th scope="col">Webhook</th>
                    </tr>
                </thead>
                <tbody id="channels">
                    @forelse ($user->channels as $channel)
                        @php
                            $chatUser = \App\Models\ChatUser::where('server_id', $channel->server_id)
                                ->where('user_id', \Auth::user()->id)
                                ->where('server_type', $channel->type)
                                ->first();
                            $character = null;
                            if ($chatUser) {
                                $chatCharacter = \App\Models\ChatCharacter::where('channel_id', $channel->id)
                                    ->where('chat_user_id', $chatUser->id)
                                    ->first();
                                if ($chatCharacter) {
                                    $character = $chatCharacter->getCharacter();
                                }
                            }
                        @endphp
                        <tr>
                            <td>
                                <i class="bi bi-{{ $channel->type }}"></i>
                            </td>
                            <td>{{ $channel->server_id }}</td>
                            <td>
                                @if ($channel->server_name)
                                    {{ $channel->server_name }}
                                @else
                                    <small class="text-muted">
                                        Unable to load name
                                    </small>
                                @endif
                            </td>
                            <td>{{ $channel->channel_id }}</td>
                            <td>
                                @if ($channel->channel_name)
                                    {{ $channel->channel_name }}
                                @else
                                    <small class="text-muted">
                                        Unable to load name
                                    </small>
                                @endif
                            </td>
                            <td>{{ $character }}</td>
                            <td>
                                @if ($channel->campaign)
                                    <a href="{{ route('campaign.view', $channel->campaign) }}">
                                        {{ $channel->campaign->name }}
                                    </a>
                                @else
                                    &nbsp;
                                @endif
                            </td>
                            <td>{{ $channel->getSystem() }}</td>
                            <td>
                                @if (\App\Models\Channel::TYPE_SLACK === $channel->type)
                                    <i class="bi bi-check-square-fill text-success" title="Slack channels don't require webhooks"></i>
                                @elseif ($channel->webhook)
                                    <i class="bi bi-check-square-fill text-success" title="{{ $channel->webhook }}"></i>
                                @else
                                    <i class="bi bi-question-square-fill text-danger"></i>
                                @endif
                            </td>
                        </tr>
                    @empty
                    <tr id="no-channels">
                        <td colspan="9">You haven't registered any channels</td>
                    </tr>
                    @endforelse
                </tbody>
                <tfoot>
                    <tr>
                        <td class="text-muted" colspan="9"><small>
                            To link a channel, type <code>/roll register
                            &lt;system&gt;</code>, where &lt;system&gt; is the
                            short code for the system you want this channel to
                            play. Type <code>/roll help</code> to see the list
                            of systems.
                            </small></td>
                    </tr>
                </tfoot>
            </table>
        </div>
    </div>

    <div aria-hidden="true" aria-labelledby="link-user-title" class="modal fade"
        id="link-user" tabindex="-1">
        <div class="modal-dialog">
            <div class="modal-content">
                <form action="/settings/link-user" method="POST">
                @csrf
                <div class="modal-header">
                    <h5 class="modal-title" id="link-user-title">Modal title</h5>
                    <button aria-label="Close" class="btn-close"
                        data-bs-dismiss="modal" type="button"></button>
                </div>
                <div class="modal-body">
                    <div class="row mt-4">
                        <div class="col">
                            <h2>Link your chat user</h2>
                            <p>
                                Linking your user will allow you to interact with
                                Commlink resources from your chat server and send
                                messages to the chat server from Commlink. This is a
                                two-step process requiring action in Commlink (here)
                                as well as in your chat channel to make sure you own
                                both sides.
                            </p>
                            <p>
                                <strong>Step 1.</strong>
                                Enter your server ID and user ID here. You can get
                                them from Commlink's bot by typing <code>/roll
                                info</code> on the server. If Commlink doesn't
                                respond, the server's administrators will need to
                                invite the bot to the server.
                            </p>
                            <p>
                                <strong>Step 2.</strong>
                                After linking, you'll need to copy a link and paste
                                it into the channel you want to link.
                            </p>
                        </div>
                    </div>
                    <div class="form-row mt-1">
                        <label class="col-4 col-form-label" for="server-id">
                            Server (required)
                        </label>
                        <div class="col">
                            <input aria-describedBy="server-help" autocomplete="off"
                                class="form-control @error('server-id') is-invalid @enderror"
                                id="server-id" name="server-id" required
                                type="text" value="{{ old('server-id') }}">
                            <small id="server-help" class="form-text text-muted">
                                Slack team IDs will look like
                                <code>T025GMATU</code>. Discord server IDs will look
                                like <code>473246380039733249</code>.
                            </small>
                            @error('server-id')
                            <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                    <div class="form-row mt-1">
                        <label class="col-4 col-form-label" for="user-id">
                            User (required)
                        </label>
                        <div class="col">
                            <input aria-describedBy="user-help" autocomplete="off"
                                class="form-control @error('user-id') is-invalid @enderror"
                                id="user-id" name="user-id" required type="text"
                                value="{{ old('user-id') }}">
                            <small id="user-help" class="form-text text-muted">
                                Slack user IDs look like <code>U025GMATW</code>.
                                Discord user IDs look like
                                <code>225743973845565441</code>.
                            </small>
                            @error('user-id')
                            <div class="invalid-feedback">
                                {{ $message }}
                            </div>
                            @enderror
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                    <button class="btn btn-primary" type="submit">
                        <svg xmlns="http://www.w3.org/2000/svg" width="16"
                            height="16" fill="currentColor" class="bi bi-link"
                            viewBox="0 0 16 16">
                            <path d="M6.354 5.5H4a3 3 0 0 0 0 6h3a3 3 0 0 0 2.83-4H9c-.086 0-.17.01-.25.031A2 2 0 0 1 7 10.5H4a2 2 0 1 1 0-4h1.535c.218-.376.495-.714.82-1z"/>
                            <path d="M9 5.5a3 3 0 0 0-2.83 4h1.098A2 2 0 0 1 9 6.5h3a2 2 0 1 1 0 4h-1.535a4.02 4.02 0 0 1-.82 1H12a3 3 0 1 0 0-6H9z"/>
                        </svg>
                        Link server
                    </button>
                </div>
                </form>
            </div>
        </div>
    </div>

<x-slot name="javascript">
    <script>
        $('.copy-btn').on('click', function (e) {
            if (!window.navigator.clipboard) {
                // Clipboard API not available
                return;
            }
            const text = $(e.target).parents('div')
                .first()
                .children('.user-select-all')
                .text()
                .trim();
            try {
                navigator.clipboard.writeText(text);
            } catch (err) {
                console.error('Failed to copy!', err);
            }
        });
        function updateAlert(id, message) {
            let el = $(id);
            if (el.length) {
                el.html(message);
            } else {
                $('main').prepend(
                    '<div class="alert alert-success alert-dismissible '
                    + 'fade mt-4 show" id="' + id + '" role="alert">'
                    + message + '</div>'
                );
            }
        }
        Echo.private(`users.{{ $user->id }}`)
            .listen('SlackUserLinked', (e) => {
                $('#slack-' + e.chatUser.server_id + '-' + e.chatUser.remote_user_id)
                    .html('<i class="bi bi-check-square-fill text-success"></i>');

                const message = 'Slack account (' + e.chatUser.server_name
                    + ' - ' + e.chatUser.remote_user_name
                    + ') linked <b>and</b> verified!'
                    + '<button type="button" class="btn-close" '
                    + 'data-bs-dismiss="alert" aria-label="Close"></button>';
                const id = '#success-slack-' + e.chatUser.server_id + '-'
                    + e.chatUser.remote_user_id;
                updateAlert(id, message);
            })
            .listen('DiscordUserLinked', (e) => {
                $('#discord-' + e.chatUser.server_id + '-' + e.chatUser.remote_user_id)
                    .html('<i class="bi bi-check-square-fill text-success"></i>');

                const message = 'Discord account (' + e.chatUser.server_name
                    + ' - ' + e.chatUser.remote_user_name
                    + ') linked <b>and</b> verified!'
                    + '<button type="button" class="btn-close" '
                    + 'data-bs-dismiss="alert" aria-label="Close"></button>';
                const id = '#success-discord-' + e.chatUser.server_id + '-'
                    + e.chatUser.remote_user_id;
                updateAlert(id, message);
            })
            .listen('ChannelLinked', (e) => {
                const channel = e.channel;
                let row = '<tr><td><i class="bi bi-' + channel.type + '"></i>'
                    + '</td><td>' + channel.server_id + '</td>';
                if (channel.server_name) {
                    row += '<td>' + channel.server_name + '</td>';
                } else {
                    row += '<td><small class="text-muted">Unable to load name</small></td>';
                }
                row += '<td>' + channel.channel_id + '</td>';
                if (channel.channel_name) {
                    row += '<td>' + channel.channel_name + '</td>';
                } else {
                    row += '<td><small class="text-muted">Unable to load name</small></td>';
                }
                row += '<td>&nbsp;</td>';
                if (channel.campaign) {
                    row += '<td><a href="/campaigns/' + channel.campaign.id
                        + '">' + channel.campaign.name + '</a></td>';
                } else {
                    row += '<td>&nbsp;</td>';
                }
                row += '<td>' + channel.system + '</td>';
                if ('slack' === channel.type) {
                    row += '<td><i class="bi bi-check-square-fill text-success" '
                        + 'title="Slack channels don\'t require webhooks"></i>'
                        + '</td>';
                } else if (channel.webhook) {
                    row += '<td><i class="bi bi-check-square-fill text-success"'
                        + ' title="' + channel.webhook + '"></i></td>';
                } else {
                    row += '<td><i class="bi bi-question-square-fill text-danger"></i></td>';
                }
                row += '</tr>';
                $('#no-channels').remove();
                $('#channels').append(row);
            });
    </script>
</x-slot>
</x-app>
