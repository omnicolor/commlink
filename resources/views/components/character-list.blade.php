<ul class="list-group">
    @forelse ($characters as $character)
        <li class="list-group-item">
        @switch ($character->system)
            @case ('cyberpunkred')
            @case ('expanse')
            @case ('shadowrun5e')
                <a href="/characters/{{ $character->system }}/{{ $character->id }}">
                    {{ $character }}</a>
                    @if ($character->campaign())
                        ({{ $character->campaign() }} &mdash;
                        {{ $character->getSystem() }})
                    @else
                        ({{ $character->getSystem() }})
                    @endif
                @break
            @default
                {{ $character->handle ?? $character->name }}
                @if ($character->campaign())
                    ({{ $character->campaign() }} &mdash;
                    {{ $character->getSystem() }})
                @else
                    ({{ $character->getSystem() }})
                @endif
            @break
        @endswitch
        </li>
    @empty
        <li class="list-group-item">
            You don't have any characters!
        </li>
    @endforelse
    <li class="list-group-item">
        <div class="dropdown">
            <a aria-expanded="false" class="btn btn-primary dropdown-toggle"
                data-bs-toggle="dropdown" href="#" id="create-character"
                role="button">
                Create character
            </a>

            <ul aria-labelledby="create-character" class="dropdown-menu">
                <li>
                    <a class="dropdown-item" href="/characters/cyberpunkred/create">
                        Cyberpunk Red
                    </a>
                </li>
            </ul>
        </div>
    </li>
</ul>
