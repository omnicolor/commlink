<x-app>
    <x-slot name="title">
        Dashboard
    </x-slot>

    <div class="row mt-4">
        <div class="col">
            <h1>Characters</h1>
            <ul class="list-group">
            @forelse ($characters as $character)
                <li class="list-group-item">
                    @switch ($character->system)
                        @case ('shadowrun5e')
                        @case ('cyberpunkred')
                            <a href="/characters/{{ $character->system }}/{{ $character->id }}">
                                {{ $character->handle }}
                            </a> ({{ $character->type }})
                            @break
                        @default
                            {{ $character->handle ?? $character->name }} ({{ $character->system }})
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
    </div>
</x-app>
