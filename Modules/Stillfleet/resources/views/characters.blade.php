<x-app>
    <x-slot name="title">Stillfleet Characters</x-slot>

    <div class="row mt-4">
        <div class="col">
            <h1>Stillfleet Characters</h1>
            <x-character-list
                :characters="\Auth::user()->characters('stillfleet')->get()" />
        </div>
    </div>
</x-app>
