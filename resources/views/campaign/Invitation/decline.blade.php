<x-app>
    <x-slot name="title">Campaign: {{ $campaign }}</x-slot>
    <x-slot name="navbar">
        <li class="nav-item">
            <a class="nav-link" href="{{ route('dashboard') }}">Home</a>
        </li>
        <li class="nav-item">
            <span class="nav-link active">{{ $campaign }}</span>
        </li>
    </x-slot>

    <div class="row mt-4">
        <div class="col-lg-3"></div>
        <div class="col">
            <h1>Decline invitation to join {{ $campaign->name }}?</h1>

            <p>
                Awww, bummer! The community at {{ config('app.name') }} was
                looking forward to playing some games with you. Keep us in mind
                if you ever need an online platform to engage with other role
                players on the internet.
            </p>

            <p>
                We've let {{ $campaign->gamemaster->name }} know that you've
                declined the invitation.
            </p>
        </div>
        <div class="col-lg-3"></div>
    </div>
</x-app>
