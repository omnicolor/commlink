<x-app>
    <x-slot name="title">
        Expanse Characters
    </x-slot>

    <div class="row mt-4">
        <div class="col">
            <h1>Expanse Characters</h1>
            <x-character-list
                :characters="\Auth::user()->characters('expanse')->get()" />
        </div>
    </div>
</x-app>
