<x-app>
    <x-slot name="title">
        Dashboard
    </x-slot>

    <div class="row mt-4">
        <div class="col">
            <h1>Characters</h1>
            <x-character-list :characters="$user->characters()->get()" />
        </div>
        <div class="col">
            <h1>Campaigns</h1>
            <x-campaign-list :gmed="$user->campaigns"
                :registered="$user->campaignsRegistered" />
        </div>
    </div>
</x-app>
