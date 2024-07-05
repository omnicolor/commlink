<x-app>
    <x-slot name="title">
        Shadowrun 5E Characters
    </x-slot>

    <div class="row mt-4">
        <div class="col">
            <h1>Shadowrun Fifth Edition Characters</h1>
            <x-character-list
                :characters="\Auth::user()->characters('shadowrun5e')->get()" />
        </div>
    </div>
</x-app>
