<x-app>
    <x-slot name="title">Choose character</x-slot>

    <h1>Choose character</h1>

    <ul class="list-group">
        @foreach ($characters as $character)
            <li class="list-group-item">
                <a href="{{ route('alien.create', $character->id) }}">
                    <i class="bi bi-person"></i>
                    {{ $character }}
                </a>
            </li>
        @endforeach
        <li class="list-group-item">
            <a href="{{ route('alien.create', 'new') }}">
                <i class="bi bi-person-plus"></i>
                Create a new Alien character
            </a>
        </li>
    </ul>
</x-app>
