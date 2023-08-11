@php
use App\Features\ApiAccess;
use App\Models\Channel;
use App\Models\ChatCharacter;
use App\Models\ChatUser;
use Laravel\Pennant\Feature;
@endphp
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
    <div class="alert alert-danger alert-dismissible fade mt-4 show" role="alert">
        There
        @if (1 === count($errors->all()))
            was a problem
        @else
            were problems
        @endif
        with your request:
        <ul>
        @foreach ($errors->all() as $message)
            <li>{!! $message !!}</li>
        @endforeach
        </ul>
    </div>
    @endif

    @if(session('error'))
    <div class="alert alert-danger mt-4">
        {{ session('error') }}
    </div>
    @endif

    <div class="row">
        <div class="col">
            <h1>Settings</h1>
        </div>
    </div>

    <div class="row mt-4">
        <div class="col">
            <div class="accordion mt-4" id="settings-accordion">
                <div class="accordion-item">
                    <h2 class="accordion-header" id="linked-chat-heading">
                        <button aria-controls="linked-chat-collapse"
                            aria-expanded="false" class="accordion-button"
                            data-bs-target="#linked-chat-collapse"
                            data-bs-toggle="collapse" type="button">
                        Linked chat users
                    </h2>
                    <div aria-labelledby="linked-chat-heading"
                        class="accordion-collapse collapse"
                        data-bs-parent="#settings-accordion"
                        id="linked-chat-collapse">
                        <div class="accordion-body">
                            <p>
                                Linked chat users are connections between your
                                account on a chat service (like Discord or
                                Slack) and {{ config('app.name') }}. Linking
                                your chat user to {{ config('app.name') }}
                                allows us to know who you are when you use
                                commands in your chat program to do things like
                                roll dice or flip coins.
                            </p>

                            <p>
                                You only need to register a user once per
                                server, no matter how many channels you play in
                                on that server.
                            </p>

                            <table class="table">
                                <thead>
                                    <tr>
                                        <th scope="col">Server</th>
                                        <th scope="col">User</th>
                                        <th scope="col">Status</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse ($user->chatUsers->sortByDesc('verified') as $chatUser)
                                        <tr>
                                            <td>
                                                <div class="d-flex align-items-center">
                                                    <i class="bi bi-{{ $chatUser->server_type !== 'irc' ? $chatUser->server_type : 'chat-text-fill' }} me-3"></i>
                                                    <div class="d-inline-block">
                                                        @if ($chatUser->server_name)
                                                            {{ $chatUser->server_name }}
                                                        @else
                                                            Unable to load name
                                                        @endif
                                                        <br>
                                                        <small class="text-muted">{{ $chatUser->server_id }}</small>
                                                    </div>
                                                </div>
                                            </td>
                                            <td>
                                                @if ($chatUser->remote_user_name)
                                                    {{ $chatUser->remote_user_name }}
                                                @else
                                                    Unable to load name
                                                @endif
                                                <br>
                                                <small class="text-muted">{{ $chatUser->remote_user_id }}</small>
                                            </td>
                                            @if ($chatUser->verified)
                                                <td title="User link is verified">
                                                    <i class="bi bi-check-square-fill text-success"></i>
                                                </td>
                                            @else
                                                <td id="{{ $chatUser->server_type }}-{{ $chatUser->server_id }}-{{ $chatUser->remote_user_id }}"
                                                    title="User link is not verified">
                                                    <div class="input-group input-group-sm">
                                                        <span class="input-group-text">
                                                            <i class="bi bi-question-square-fill text-danger"></i>
                                                        </span>
                                                        <span class="input-group-text user-select-all">
                                                            @if ($chatUser->server_type === 'irc')
                                                            :roll validate {{ $chatUser->verification }}
                                                            @else
                                                            /roll validate {{ $chatUser->verification }}
                                                            @endif
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
                </div>

                <div class="accordion-item">
                    <h2 class="accordion-header" id="linked-channels-heading">
                        <button aria-controls="linked-channels-collapse"
                            aria-expanded="false" class="accordion-button"
                            data-bs-target="#linked-channels-collapse"
                            data-bs-toggle="collapse" type="button">
                        Linked channels
                    </h2>
                    <div aria-labelledby="linked-channels-heading"
                        class="accordion-collapse collapse"
                        data-bs-parent="#settings-accordion"
                        id="linked-channels-collapse">
                        <div class="accordion-body">
                            <p>
                                Linked channels are connections between a chat
                                service (like Discord or Slack) and
                                {{ config('app.name') }}. The gamemaster for a
                                particular game is most likely the person that
                                will set this up, but any user in a channel can
                                register it. To link a channel, type <code>/roll
                                register &lt;system&gt;</code> in the channel
                                you'd like to register, where &lt;system&gt; is
                                the short code for the system you want this
                                channel to play. Type <code>/roll help</code> to
                                see the list of systems.
                            </p>

                            <p>
                                You need to have already linked your chat user
                                to {{ config('app.name') }} in order to register
                                a channel (see &rdquo;Linked chat users&ldquo;
                                above).
                            </p>

                            <p>
                                Each channel can then be linked to a single
                                campaign as well as a character for each linked
                                chat user in the channel. So, for example, you
                                can link a Slack channel called
                                <strong>#pits-and-wyverns</strong> to play
                                Dungeons and Dragons 5E by typing <code>/roll
                                register dnd5e</code>. Players in the channel
                                would then be able to link their character
                                sheets to the channel by typing <code>/roll link
                                &lt;characterId&gt;</code>.
                            </p>

                            <table class="table">
                                <thead>
                                    <tr>
                                        <th scope="col">Server</th>
                                        <th scope="col">Channel</th>
                                        <th scope="col">Character</th>
                                        <th scope="col">Campaign</th>
                                        <th scope="col">System</th>
                                        <th scope="col">Webhook</th>
                                    </tr>
                                </thead>
                                <tbody id="channels">
                                    @forelse ($user->channels as $channel)
                                        @php
                                            $chatUser = ChatUser::where('server_id', $channel->server_id)
                                                ->where('user_id', \Auth::user()->id)
                                                ->where('server_type', $channel->type)
                                                ->first();
                                            $character = null;
                                            if ($chatUser) {
                                                $chatCharacter = ChatCharacter::where('channel_id', $channel->id)
                                                    ->where('chat_user_id', $chatUser->id)
                                                    ->first();
                                                if ($chatCharacter) {
                                                    $character = $chatCharacter->getCharacter();
                                                }
                                            }
                                        @endphp
                                        <tr class="align-middle">
                                            <td>
                                                <div class="d-flex align-items-center">
                                                    <i class="bi bi-{{ $channel->type !== 'irc' ? $channel->type : 'chat-text' }} me-3"></i>
                                                    <div class="d-inline-block">
                                                        @if ($channel->server_name)
                                                            {{ $channel->server_name }}
                                                        @else
                                                            Unable to load name
                                                        @endif
                                                        <br>
                                                        <small class="text-muted">{{ $channel->server_id }}</small>
                                                    </div>
                                                </div>
                                            </td>
                                            <td>
                                                @if ($channel->channel_name)
                                                    {{ $channel->channel_name }}
                                                @else
                                                    Unable to load name
                                                @endif
                                                <br>
                                                <small class="text-muted">{{ $channel->channel_id }}</small>
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
                                                @if (Channel::TYPE_SLACK === $channel->type)
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
                                    </tr>
                                </tfoot>
                            </table>
                        </div>
                    </div>
                </div>

                @if (Feature::active(ApiAccess::class))
                <div class="accordion-item">
                    <h2 class="accordion-header" id="api-keys-heading">
                        <button aria-controls="api-keys-collapse"
                            aria-expanded="true" class="accordion-button"
                            data-bs-target="#api-keys-collapse"
                            data-bs-toggle="collapse" type="button">
                        API keys
                    </h2>
                    <div aria-labelledby="api-keys-heading"
                        class="accordion-collapse collapse"
                        data-bs-parent="#settings-accordion"
                        id="api-keys-collapse">
                        <div class="accordion-body" id="api-keys-table">
                            <p>
                                An API token is used to interact directly with
                                <a href="https://app.swaggerhub.com/apis/omnicolor/Commlink">Commlink's API</a>.
                                If you're not a software developer or don't know
                                what an API is, this probably isn't something
                                that you need to use. In fact, unless you are
                                building an integration with
                                {{ config('app.name') }}, this is really
                                something you should avoid playing with.
                            </p>

                            <table class="table">
                                <thead>
                                    <tr>
                                        <th scope="col">Name</th>
                                        <th scope="col">Expires at</th>
                                        <th scope="col">Last used</th>
                                        <th scope="col">Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                @foreach ($user->tokens as $token)
                                    <tr>
                                        <td>{{ $token->name }}</td>
                                        <td>{{ $token->expires_at?->format('Y-m-d') }}</td>
                                        <td>{{ $token->last_used_at }}</td>
                                        <td><button
                                            class="btn btn-outline-danger btn-sm"
                                            data-id="{{ $token->id }}"
                                            type="button">
                                            <i class="bi bi-trash3"></i>
                                        </button></td>
                                    </tr>
                                @endforeach
                                </tbody>
                                <tfoot>
                                    <tr>
                                        <td colspan="4">
                                            <button class="btn btn-primary"
                                                data-bs-target="#create-token"
                                                data-bs-toggle="modal" type="button">
                                                Create a token
                                            </button>
                                        </td>
                                    </tr>
                                </tfoot>
                            </table>
                        </div>
                    </div>
                </div>
                @endif
            </div>
        </div>
    </div>

    <div aria-hidden="true" aria-labelledby="link-user-title" class="modal fade"
        id="link-user" tabindex="-1">
        <div class="modal-dialog">
            <div class="modal-content">
                <form action="/settings/link-user" method="POST">
                @csrf
                <div class="modal-header">
                    <h5 class="modal-title" id="link-user-title">
                        Link your chat user
                    </h5>
                    <button aria-label="Close" class="btn-close"
                        data-bs-dismiss="modal" type="button"></button>
                </div>
                <div class="modal-body">
                    <div class="row">
                        <div class="col">
                            <p>
                                Linking your user will allow you to interact
                                with {{ config('app.name') }} resources from
                                your chat server and send messages to the chat
                                server from {{ config('app.name') }}. This is a
                                two-step process requiring action in
                                {{ config('app.name') }} (here) as well as in
                                your chat channel to make sure you own both
                                sides.
                            </p>

                            <p>
                                For Discord servers, we can automagically get
                                your user ID by <a href="{{ $discordOauthURL }}">
                                    clicking here</a>. Or
                                you can manually follow these two steps.
                            </p>

                            <p>
                                <strong>Step 1.</strong>
                                Enter your server ID and user ID here. You can
                                get them from {{ config('app.config') }}'s bot
                                by typing <code>/roll info</code> (Slack and
                                Discord) or <code>:roll info</code> (IRC) on the
                                server. If {{ config('app.config') }} doesn't
                                respond, the server's administrators will need
                                to invite the bot to the server.
                            </p>
                            <p>
                                <strong>Step 2.</strong>
                                After linking, you'll need to copy a link and
                                paste it into the channel you want to link.
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
                                <code>T025GMATU</code>. Discord server IDs will
                                look like <code>473246380039733249</code>. IRC
                                server IDs will look like
                                <code>chat.freenode.net:6667</code>.
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
                                <code>225743973845565441</code>. Your IRC user
                                ID is what you go by on IRC.
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

    <div aria-hidden="true" aria-labelledby="create-token-title" class="modal fade"
        id="create-token" tabindex="-1">
        <div class="modal-dialog">
            <div class="modal-content">
                <form id="api-token-form">
                <div class="modal-header">
                    <h5 class="modal-title" id="create-token-title">
                        Create a new API token
                    </h5>
                    <button aria-label="Close" class="btn-close"
                        data-bs-dismiss="modal" type="button"></button>
                </div>
                <div class="modal-body">
                    <div class="row">
                        <div class="col">
                            <p>
                                API keys should be protected. Anyone that has
                                your API key can do everything you're allowed to
                                do. For example, they can delete any campaigns
                                or characters you've created.
                            </p>
                        </div>
                    </div>
                    <div class="form-row mt-1">
                        <label class="form-label" for="token-name">
                            Token name (required)
                        </label>
                        <div class="col">
                            <input aria-describedBy="token-name-help" autocomplete="off"
                                class="form-control" id="token-name" required
                                type="text">
                            <small id="server-help" class="form-text text-muted">
                                If you're building an app, this might be the
                                name of the app you're building.
                            </small>
                        </div>
                    </div>
                    <div class="form-row mt-1">
                        <label class="form-label" for="token-expiration">
                            Expiration
                        </label>
                        <div class="col">
                            <input aria-describedBy="token-expiration-help"
                                autocomplete="off" class="form-control"
                                id="token-expiration" min="{{ date('Y-m-d') }}"
                                type="date">
                            <small id="token-expiration-help" class="form-text text-muted">
                                Token expiration is not required, but can help
                                keep your data safer by automatically expiring
                                and requiring you to recreate them.
                            </small>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                    <button class="btn btn-primary" disabled id="token-submit"
                        type="submit">
                        Create token
                    </button>
                </div>
                </form>
            </div>
        </div>
    </div>

    <div aria-hidden="true" aria-labelledby="create-token-title" class="modal fade"
        id="token-created" tabindex="-1">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="create-token-title">
                        New token created!
                    </h5>
                    <button aria-label="Close" class="btn-close"
                        data-bs-dismiss="modal" type="button"></button>
                </div>
                <div class="modal-body">
                    <div class="row">
                        <div class="col">
                            <p>
                                <strong>Remember!</strong> API keys should be
                                protected. Anyone that has your API key can do
                                everything you're allowed to do. For example,
                                they can delete any campaigns or characters
                                you've created.
                            </p>
                            <div class="input-group input-group-sm">
                                <span class="input-group-text user-select-all">
                                </span>
                                <button class="btn btn-outline-secondary copy-btn" type="button">
                                    <i class="bi bi-clipboard"></i>
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Done</button>
                </div>
            </div>
        </div>
    </div>

<x-slot name="javascript">
    <script>
        const csrfToken = '{{ csrf_token() }}';

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

        $('#token-name').on('keyup', function (e) {
            e = $(e.target);
            $('#token-submit').prop('disabled', '' === e.val().trim());
        });

        $('#api-token-form').on('submit', function (e) {
            e.preventDefault();
            $('#token-submit').prop('disabled', true);
            const settings = {
                accept: 'application/json',
                data: {
                    _token: csrfToken,
                    expires_at: $('#token-expiration').val(),
                    name: $('#token-name').val()
                },
                method: 'POST',
                url: '/api/users/' + {{ $user->id }} + '/token'
            };
            $.ajax(settings)
                .done(function (data) {
                    let expires = data.expires_at;
                    if (null !== expires) {
                        expires = expires.split('T')[0];
                    } else {
                        expires = '';
                    }
                    bootstrap.Modal.getInstance('#create-token').hide();
                    $('#token-created .user-select-all').text(data.plainText);
                    bootstrap.Modal.getInstance('#token-created').hide();
                    $('#token-expiration').val('');
                    $('#token-name').val('');
                    $('#api-keys-table tbody').append(
                        '<tr><td>' + data.name + '</td>' +
                        '<td>' + expires + '</td>' +
                        '<td>&nbsp;</td>' +
                        '<td><button class="btn btn-outline-danger btn-sm" ' +
                        'data-id="' + data.id + '" type="button">' +
                        '<i class="bi bi-trash3"></i></button></td>' +
                        '</tr>'
                    );
                })
                .fail(function (data) { window.console.log(data); });
        });

        $('#api-keys-table').on('click', '.btn-outline-danger', function (e) {
            e = $(e.target);
            if ('I' === e[0].nodeName) {
                e = e.parent();
            }
            const token = e.data('id');
            const settings = {
                accept: 'application/json',
                data: { _token: csrfToken },
                method: 'DELETE',
                url: '/api/users/' + {{ $user->id }} + '/token/' + token
            };
            $.ajax(settings)
                .done(function () { e.parents('TR').remove(); })
                .fail(function (data) { window.console.log(data); });
        });

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
