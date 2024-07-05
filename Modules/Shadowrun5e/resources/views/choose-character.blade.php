<x-app>
    <x-slot name="title">Choose character</x-slot>

    <h1>Choose character</h1>

    <ul class="list-group">
        @foreach ($characters as $character)
            <li class="list-group-item">
                <a href="/characters/shadowrun5e/create/{{ $character->id }}">
                    <i class="bi bi-person"></i>
                    {{ $character->handle ?? 'Unnamed ' . $character->role }}
                </a>
            </li>
        @endforeach
        <li class="list-group-item">
            <a href="/characters/shadowrun5e/create/new">
                <i class="bi bi-person-plus"></i>
                Create a new Shadowrun 5th Edition character
            </a>
        </li>
    </ul>
</x-app>
