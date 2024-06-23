@php
use App\Features\ChummerImport;
use App\Features\HeroLabImport;
use App\Features\CyberpunkCharacterGeneration;
use App\Features\WorldAnvilImport;
use App\Features\Shadowrun5eCharacterGeneration;
use App\Features\SubversionCharacterGeneration;
@endphp
<ul class="list-group">
    @forelse ($characters as $character)
        <li class="list-group-item">
        @switch ($character->system)
            @case ('avatar')
            @case ('capers')
            @case ('cyberpunkred')
            @case ('expanse')
            @case ('shadowrun5e')
            @case ('shadowrun6e')
            @case ('star-trek-adventures')
            @case ('subversion')
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
                    @feature(CyberpunkCharacterGeneration::class)
                    <a class="dropdown-item" href="/characters/cyberpunkred/create">
                        Cyberpunk Red
                        <span class="badge bg-danger">Not complete</span>
                    </a>
                    @endfeature
                    @feature(Shadowrun5eCharacterGeneration::class)
                    <a class="dropdown-item" href="/characters/shadowrun5e/create">
                        Shadowrun 5th Edition
                        <span class="badge bg-danger">Not complete</span>
                    </a>
                    @endfeature
                    @feature(SubversionCharacterGeneration::class)
                    <a class="dropdown-item" href="/characters/subversion/create">
                        Subversion
                        <span class="badge bg-danger">Not complete</span>
                    </a>
                    @endfeature
                    @feature(ChummerImport::class)
                    <a class="dropdown-item" href="{{ route('import.chummer5.view') }}">
                        Import a Chummer 5 character
                        <span class="badge bg-warning">Beta</span>
                    </a>
                    @endfeature
                    @feature(HeroLabImport::class)
                    <a class="dropdown-item" href="{{ route('import.herolab.view') }}">
                        Import a Hero Lab portfolio
                        <span class="badge bg-warning">Beta</span>
                    </a>
                    @endfeature
                    @feature(WorldAnvilImport::class)
                    <a class="dropdown-item" href="{{ route('import.world-anvil.view') }}">
                        Import a World Anvil character
                        <span class="badge bg-warning">Beta</span>
                    </a>
                    @endfeature
                </li>
            </ul>
        </div>
    </li>
</ul>
