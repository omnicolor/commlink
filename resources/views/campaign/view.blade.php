<x-app>
    <x-slot name="title">Campaign: {{ $campaign }}</x-slot>

    <h1>{{ $campaign }}</h1>

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
                    </li>
                @empty
                    <li class="list-group-item">Campaign has no channels</li>
                @endforelse
                <li class="list-group-item"><a href="#">
                    <i class="bi bi-plus-circle"></i>
                    Add server
                </a></li>
            </ul>
        </div>
        <div class="col">
        </div>
    </div>
</x-app>
