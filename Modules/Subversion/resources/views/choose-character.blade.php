<x-app>
    <x-slot name="title">Choose character</x-slot>

    <h1>Choose character</h1>

    <ul class="list-group">
        @foreach ($characters as $character)
            <li class="list-group-item">
                <a href="/characters/subversion/create/{{ $character->id }}">
                    <i class="bi bi-person"></i>
                    {{ $character->name ?? 'Unnamed' }}
                </a>
            </li>
        @endforeach
        <li class="list-group-item">
            <a href="/characters/subversion/create/new">
                <i class="bi bi-person-plus"></i>
                Create a new Subversion character
            </a>
        </li>
    </ul>
</x-app>
