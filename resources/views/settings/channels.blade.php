@php
use App\Models\Channel;
use App\Models\ChatCharacter;
use App\Models\ChatUser;
@endphp
<x-app>
    <x-slot name="title">Settings - Channels</x-slot>

    <div class="row">
        <div class="col">
            <h1>Settings - Linked channels</h1>
        </div>
    </div>

    <p>
        Linked channels are connections between a chat service (like Discord or
        Slack) and {{ config('app.name') }}. The gamemaster for a particular
        game is most likely the person that will set this up, but any user in a
        channel can register it. To link a channel, type <code>/roll register
        &lt;system&gt;</code> in the channel you'd like to register, where
        &lt;system&gt; is the short code for the system you want this channel
        to play. Type <code>/roll help</code> to see the list of systems.
    </p>

    <p>
        You need to have already linked your chat user to
        {{ config('app.name') }} in order to register a channel (see
        <a href="{{ route('settings.chat-users') }}">Linked chat users</a>).
    </p>

    <p>
        Each channel can then be linked to a single campaign as well as a
        character for each linked chat user in the channel. So, for example,
        you can link a Slack channel called <strong>#pits-and-wyverns</strong>
        to play Dungeons and Dragons 5E by typing <code>/roll register
        dnd5e</code>. Players in the channel would then be able to link their
        character sheets to the channel by typing <code>/roll link
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
                <td colspan="6">You haven't registered any channels</td>
            </tr>
            @endforelse
        </tbody>
    </table>

<x-slot name="javascript">
    <script>
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
    </script>
</x-slot>
</x-app>
