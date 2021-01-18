<x-app>
    <x-slot name="title">
        Dashboard
    </x-slot>

    <div class="row mt-4">
        <div class="col">
            <h1>Characters</h1>
            <ul class="list-group">
            @foreach ($characters as $character)
                <li class="list-group-item">
                    {{ $character->handle ?? $character->name }} ({{ $character->type }})
                </li>
            @endforeach
            </ul>
        </div>
    </div>
</x-app>
