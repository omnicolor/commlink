<x-app>
    <x-slot name="title">Settings - Chat Users</x-slot>

    @if(session('successObj'))
    <div class="alert alert-success alert-dismissible fade mt-4 show" id="{{ session('successObj')['id'] }}" role="alert">
        {{ session('successObj')['message'] }}
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>
    @endif

    @if(session('success'))
    <div class="alert alert-success mt-4">{{ session('success') }}</div>
    @endif

    @if(session('error'))
    <div class="alert alert-danger mt-4">
        {{ session('error') }}
    </div>
    @endif

    <h2>Linked chat users</h2>

    <p>
        Linked chat users are connections between your account on a chat
        service (like Discord or Slack) and {{ config('app.name') }}. Linking
        your chat user to {{ config('app.name') }} allows us to know who you
        are when you use commands in your chat program to do things like roll
        dice or flip coins.
    </p>

    <p>
        You only need to register a user once per server, no matter how many
        channels you play in on that server.
    </p>

    <table class="table">
        <thead>
            <tr>
                <th scope="col">Server</th>
                <th scope="col">User</th>
                <th scope="col">Status</th>
                <th scope="col">Delete</th>
            </tr>
        </thead>
        <tbody>
            @forelse ($user->chatUsers->sortByDesc('verified') as $chat_user)
            <tr data-id="{{ $chat_user->id }}">
                <td>
                    <div class="d-flex align-items-center">
                        <i class="bi bi-{{ $chat_user->server_type !== 'irc' ? $chat_user->server_type : 'chat-text-fill' }} me-3"></i>
                        <div class="d-inline-block">
                            @if ($chat_user->server_name)
                                {{ $chat_user->server_name }}
                            @else
                                Unable to load name
                            @endif
                            <br>
                            <small class="text-muted">{{ $chat_user->server_id }}</small>
                        </div>
                    </div>
                </td>
                <td>
                    @if ($chat_user->remote_user_name)
                        {{ $chat_user->remote_user_name }}
                    @else
                        Unable to load name
                    @endif
                    <br>
                    <small class="text-muted">{{ $chat_user->remote_user_id }}</small>
                </td>
                @if ($chat_user->verified)
                    <td title="User link is verified">
                        <i class="bi bi-check-square-fill text-success"></i>
                    </td>
                @else
                    <td id="{{ $chat_user->server_type }}-{{ $chat_user->server_id }}-{{ $chat_user->remote_user_id }}"
                        title="User link is not verified">
                        <div class="input-group input-group-sm">
                            <span class="input-group-text">
                                <i class="bi bi-question-square-fill text-danger"></i>
                            </span>
                            <span class="input-group-text user-select-all">
                                @if ($chat_user->server_type === 'irc')
                                :roll validate {{ $chat_user->verification }}
                                @else
                                /roll validate {{ $chat_user->verification }}
                                @endif
                            </span>
                            <button class="btn btn-outline-secondary copy-btn" type="button">
                                <i class="bi bi-clipboard"></i>
                            </button>
                        </div>
                    </td>
                @endif
                <td>
                    <button class="btn btn-light" data-bs-target="#delete-user"
                        data-bs-toggle="modal" data-bs-id="{{ $chat_user->id }}"
                        type="button">
                        <i class="bi bi-trash"></i>
                    </button>
                </td>
            </tr>
            @empty
                <tr>
                    <td colspan="4">
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

    <div aria-hidden="true" aria-labelledby="delete-user-title"
        class="modal fade" id="delete-user" tabindex="-1">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h1 class="modal-title fs-5" id="delete-user-title">Delete user?</h1>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    Are you sure you want to delete this user? This action can
                    not be undone!
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Never mind</button>
                    <button type="button" class="btn btn-primary">Yes, I'm sure</button>
                </div>
            </div>
        </div>
    </div>

    <template id="alert">
    <div class="alert alert-danger alert-dismissible fade mt-4 show" role="alert">
        <span></span>
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>
    </template>

<x-slot name="javascript">
    <script>
        const csrfToken = '{{ csrf_token() }}';

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

        $('#delete-user').on('show.bs.modal', function (event) {
            const button = event.relatedTarget;
            const id = button.getAttribute('data-bs-id');
            $('#delete-user').data('id', id);
        });
        $('#delete-user .btn-primary').on('click', function () {
            const modal = $('#delete-user');
            const id = modal.data('id');
            $.ajax({
                data: {_token: csrfToken},
                method: 'DELETE',
                error: function (xhr, status, error) {
                    const template = document.querySelector('#alert');
                    const element = template.content.cloneNode(true);
                    let span = element.querySelectorAll('span');
                    span[0].textContent = 'There was an error deleting '
                        + 'the chat user: ' + error;
                    const heading = document.querySelector('h2');
                    heading.parentNode.insertBefore(element, heading);
                    modal.hide();
                    $('.modal-backdrop').remove();
                },
                success: function () {
                    $('tr[data-id="' + id + '"]').remove();
                    modal.hide();
                    $('.modal-backdrop').remove();
                },
                url: '/api/users/{{ $user->id }}/chat-users/' + id,
            });
        });
    </script>
</x-slot>
</x-app>
