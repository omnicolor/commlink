<x-app>
    <x-slot name="title">
        Star Trek Adventures Characters
    </x-slot>

    <div class="row mt-4">
        <div class="col">
            <h1>Star Trek Adventures Characters</h1>
            <x-character-list :characters="$characters" />
        </div>
    </div>
</x-app>
