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
                    @if ('shadowrun5e' === $character->system)
                    <a href="/characters/shadowrun5e/{{ $character->id }}">
                        {{ $character->handle }}
                    </a> ({{ $character->type }})
                    @else
                    {{ $character->handle ?? $character->name }} ({{ $character->system }})
                    @endif
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
