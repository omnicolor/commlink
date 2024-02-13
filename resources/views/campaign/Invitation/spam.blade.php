<x-app>
    <x-slot name="title">Campaign: {{ $campaign }}</x-slot>
    <x-slot name="navbar">
        <li class="nav-item">
            <a class="nav-link" href="/dashboard">Home</a>
        </li>
        <li class="nav-item">
            <span class="nav-link active">{{ $campaign }}</span>
        </li>
    </x-slot>

    <div class="row mt-4">
        <div class="col-lg-3"></div>
        <div class="col">
            <h1>Invitation to join {{ $campaign->name }} marked SPAM</h1>

            <p>
                So sorry to bother you! We've marked the invite as spam, and the
                person responsible for wasting your time has had this added to
                their <strong>permanent record</strong>.
            </p>
        </div>
        <div class="col-lg-3"></div>
    </div>
</x-app>
