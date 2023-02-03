<x-app>
    <x-slot name="title">
        Choose Discord Guild(s)
    </x-slot>

    <div class="row">
        <div class="col">
            <h1 class="mt-4">Choose Discord
                @if (1 === count($guilds))
                    guild
                @else
                    guilds
                @endif
            </h1>
        </div>
    </div>

    <form action="{{ route('discord.save') }}" method="POST">
    @csrf
    <div class="row">
        <div class="col-1"></div>
        <div class="col d-flex align-items-center justify-content-center">
            <div class="card" style="width: 14rem;">
                @if (null !== $discordUser['avatar'])
                    <img alt="User's avatar" class="card-img-top"
                        src="{{ $discordUser['avatar'] }}">
                @else
                    <img alt="Default avatar" class="card-img-top"
                        src="https://cdn.discordapp.com/embed/avatars/{{ $discordUser['discriminator'] % 5 }}.png">
                @endif
                <div class="card-body">
                    <h5 class="card-title text-center">
                        {{ $discordUser['username'] }}#{{ $discordUser['discriminator'] }}
                    </h5>
                </div>
            </div>
        </div>
        <div class="col-1 d-flex align-items-center justify-content-center">
            <svg xmlns="http://www.w3.org/2000/svg" width="64" height="64"
                fill="currentColor" class="bi bi-three-dots"
                viewBox="0 0 16 16">
                <path d="M3 9.5a1.5 1.5 0 1 1 0-3 1.5 1.5 0 0 1 0 3zm5 0a1.5 1.5 0 1 1 0-3 1.5 1.5 0 0 1 0 3zm5 0a1.5 1.5 0 1 1 0-3 1.5 1.5 0 0 1 0 3z"/>
            </svg>
        </div>
        <div class="col d-flex align-items-center justify-content-center">
            <div class="card">
                <div class="card-body">
                    <h5 class="card-title">Discord guilds</h5>
                </div>
                <ul class="list-group list-group-flush">
                    @foreach ($guilds as $guild)
                        <li class="list-group-item align-middle">
                            <input class="form-check-input"
                                @if ($registeredGuilds->has($guild['snowflake']))
                                    checked disabled title="Guild already linked"
                                @endif
                                id="guild-{{ $guild['snowflake'] }}"
                                name="guilds[]" type="checkbox"
                                value="{{ $guild['snowflake'] }}">
                            <label class="form-check-label"
                                for="guild-{{ $guild['snowflake'] }}">
                                <img class="mx-3" src="{{ $guild['icon'] }}"
                                    style="height:32px">
                                {{ $guild['name'] }}
                            </label>
                        </li>
                    @endforeach
                </ul>
            </div>
        </div>
        <div class="col-1"></div>
    </div>

    <div class="row mt-4">
        <div class="col text-end">
            <a href="{{ route('settings') }}" class="btn btn-secondary">
                Never mind
            </a>
            <button type="submit" class="btn btn-primary">
                Link with Discord
            </button>
        </div>
        <div class="col-1"></div>
    </div>
    </form>
</x-app>
