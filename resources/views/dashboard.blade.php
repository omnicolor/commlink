<x-app>
    <x-slot name="title">
        Dashboard
    </x-slot>

    <div class="row mt-4">
        <div class="col">
            <h1>Characters</h1>
            <ul class="list-group">
                @forelse ($user->characters()->get() as $character)
                <li class="list-group-item">
                    @switch ($character->system)
                        @case ('cyberpunkred')
                        @case ('expanse')
                        @case ('shadowrun5e')
                            <a href="/characters/{{ $character->system }}/{{ $character->id }}">
                                {{ $character->handle ?? $character->name }}</a>
                            ({{ config('app.systems')[$character->system] }})
                            @break
                        @default
                            @if(array_key_exists($character->system, config('app.systems')))
                            {{ $character->handle ?? $character->name }}
                            ({{ config('app.systems')[$character->system] }})
                            @else
                            {{ $character->handle ?? $character->name }}
                            ({{ $character->system }})
                            @endif
                            @break
                    @endswitch
                </li>
            @empty
                <li class="list-group-item">
                    You don't have any characters!
                </li>
            @endforelse
            </ul>
        </div>
        <div class="col">
            <h1>Campaigns</h1>
            <ul class="list-group">
                @if (0 === count($user->campaigns) && 0 === count($user->campaignsRegistered))
                <li class="list-group-item">
                    You don't have any campaigns!
                </li>
                @else
                    @foreach ($user->campaigns as $campaign)
                        <li class="list-group-item">
                            {{ $campaign->name }}
                            ({{ config('app.systems')[$campaign->system] }}) -
                            Gamemaster
                        </li>
                    @endforeach
                    @foreach ($user->campaignsRegistered as $campaign)
                        <li class="list-group-item">
                            {{ $campaign->name }}
                            ({{ config('app.systems')[$campaign->system] }}) -
                            Registered
                        </li>
                    @endforeach
                @endif
                <li class="list-group-item">
                    <a class="btn btn-primary" href="/campaigns/create">
                        Create campaign
                    </a>
                </li>
            </ul>
        </div>
    </div>
</x-app>
