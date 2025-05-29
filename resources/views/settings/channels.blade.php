@php
use App\Enums\ChannelType;
use App\Models\ChatCharacter;
use App\Models\ChatUser;
use Illuminate\Support\Facades\Auth;
@endphp
<x-app>
    <x-slot name="title">Settings - Channels</x-slot>

    <div class="row mt-4">
        <div class="col-lg-1"></div>
        <div class="col">
            <h1>Settings - Linked channels</h1>
        </div>
        <div class="col-lg-1"></div>
    </div>

    <div class="row mt-4">
        <div class="col-lg-1"></div>
        <div class="col">
            <p>
                Linked channels are connections between a chat service (like
                Discord or Slack) and {{ config('app.name') }}. The gamemaster
                for a particular game is most likely the person that will set
                this up, but any user in a channel can register it. To link a
                channel, type <code>/roll register &lt;system&gt;</code> in the
                channel you'd like to register, where &lt;system&gt; is the
                short code for the system you want this channel to play. Type
                <code>/roll help</code> to see the list of systems.
            </p>

            <p>
                You need to have already linked your chat user to
                {{ config('app.name') }} in order to register a channel (see
                <a href="{{ route('settings.chat-users') }}">Linked chat users</a>).
            </p>

            <p>
                Each channel can then be linked to a single campaign as well as
                a character for each linked chat user in the channel. So, for
                example, you can link a Slack channel called
                <strong>#pits-and-wyverns</strong> to play Dungeons and Dragons
                5E by typing <code>/roll register dnd5e</code>. Players in the
                channel would then be able to link their character sheets to
                the channel by typing <code>/roll link
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
                        <th class="text-center" scope="col">Webhook</th>
                        <th class="text-center" scope="col">Delete</th>
                    </tr>
                </thead>
                <tbody id="channels">
                    @foreach ($user->channels as $channel)
                        @php
                            $chatUser = ChatUser::where('server_id', $channel->server_id)
                                ->where('user_id', Auth::user()->id)
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
                        <tr class="align-middle" data-id="{{ $channel->id }}">
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
                            <td class="text-center">
                                @if (ChannelType::Slack === $channel->type)
                                    <i class="bi bi-check-square-fill text-success" title="Slack channels don't require webhooks"></i>
                                @elseif ($channel->webhook)
                                    <i class="bi bi-check-square-fill text-success" title="{{ $channel->webhook }}"></i>
                                @else
                                    <i class="bi bi-question-square-fill text-danger"></i>
                                @endif
                            </td>
                            <td class="text-center">
                                <button class="btn btn-light"
                                    data-bs-target="#delete-channel"
                                    data-bs-toggle="modal"
                                    data-bs-id="{{ $channel->id }}"
                                    type="button">
                                    <i class="bi bi-trash"></i>
                                </button>
                            </td>
                        </tr>
                    @endforeach
                    <tr @class(['d-none' => 0 !== count($user->channels)]) id="no-channels">
                        <td colspan="7">You haven't registered any channels</td>
                    </tr>
                </tbody>
            </table>
        </div>
        <div class="col-lg-1"></div>
    </div>

    <div aria-hidden="true" aria-labelledby="delete-channel-title"
        class="modal fade" id="delete-channel" tabindex="-1">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h1 class="modal-title fs-5" id="delete-channel-title">Delete channel?</h1>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    Are you sure you want to delete this channel? This action
                    can not be undone!
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

        $('#delete-channel').on('show.bs.modal', function (event) {
            const button = event.relatedTarget;
            const id = button.getAttribute('data-bs-id');
            $('#delete-channel').data('id', id);
        });
        $('#delete-channel .btn-primary').on('click', function () {
            const modal = $('#delete-channel');
            const id = modal.data('id');
            $.ajax({
                data: {_token: csrfToken},
                method: 'DELETE',
                error: function (xhr, status, error) {
                    const template = document.querySelector('#alert');
                    const element = template.content.cloneNode(true);
                    let span = element.querySelectorAll('span');
                    span[0].textContent = 'There was an error deleting '
                        + 'the channel: ' + error;
                    const heading = document.querySelector('h1');
                    heading.parentNode.insertBefore(element, heading);
                    modal.hide();
                    $('.modal-backdrop').remove();
                },
                success: function () {
                    $('tr[data-id="' + id + '"]').remove();
                    if (2 === $('tr').length) {
                        $('#no-channels').removeClass('d-none');
                    }
                    modal.hide();
                    $('.modal-backdrop').remove();
                },
                url: '/api/channels/' + id,
            });
        });
    </script>
</x-slot>
</x-app>
