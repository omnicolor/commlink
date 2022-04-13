<ul class="list-group">
    @forelse ($characters as $character)
        <li class="list-group-item">
        @switch ($character->system)
            @case ('capers')
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
                    <a class="dropdown-item" href="/characters/capers/create">
                        Capers
                        <span class="badge bg-success">New!</span>
                    </a>
                    @feature('cyberpunkred-chargen')
                    <a class="dropdown-item" href="/characters/cyberpunkred/create">
                        Cyberpunk Red
                        <span class="badge bg-danger">Not complete</span>
                    </a>
                    @endfeature
                    @feature('shadowrun5e-chargen')
                    <a class="dropdown-item" href="/characters/shadowrun5e/create">
                        Shadowrun 5th Edition
                        <span class="badge bg-danger">Not complete</span>
                    </a>
                    @endfeature
                </li>
            </ul>
        </div>
    </li>
</ul>
