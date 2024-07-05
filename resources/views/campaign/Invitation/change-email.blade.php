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
            <h1>Join {{ $campaign->name }}?</h1>

            <p>
                Awesome! We can't wait to have you join the
                {{ config('app.name') }} community, and the other people
                involved in the campaign are undoubtedly shining their armor and
                sharpening their weapons in anticipation of your arrival! You've
                indicated that you already have an account. The gamemaster might
                have invited the wrong one.
            </p>

            <p>Your other options:</p>
            <ul>
                <li>
                    <a href="{{ $accept_url }}">I think I need to create a new account in order to join!</a>
                </li>
                <li>
                    <a href="{{ $decline_url }}">
                        Actually, I don't want to have fun in a fantasy world, decline the invitation. The party will have to fight on without me.
                    </a>
                </li>
                <li>
                    <a href="{{ $spam_url }}">
                        What is all this? I don't know who the GM is or what all this fantasy stuff is, stop spamming me!
                    </a>
                </li>
           </ul>

            <form action="{{ route('login') }}" class="card-body"
                id="login-form" method="POST" novalidate>
                @csrf
                <input name="invitation" type="hidden" value="{{ $invitation->id }}">
                <input name="token" type="hidden" value="{{ $invitation->hash() }}">

                <div class="form-row mb-2">
                    <label class="col-form-label col-md-3" for="email">
                        Email
                    </label>
                    <div class="col">
                        <input autocomplete="email" autofocus
                            class="form-control" id="email"
                            inputmode="email" name="email" required
                            type="email">
                        <div class="invalid-feedback">
                            Enter your email address.
                        </div>
                    </div>
                </div>

                <div class="form-row mb-2">
                    <label class="col-form-label col-md-3 text-nowrap"
                        for="password">
                        Password
                    </label>
                    <div class="col">
                        <input autocomplete="current-password"
                            class="form-control" id="password"
                            name="password" required type="password">
                        <div class="invalid-feedback">
                            Enter your password.
                        </div>
                    </div>
                </div>

                <div class="row">
                    <div class="col">
                        <div class="d-grid gap-2 my-2">
                            <button class="btn btn-primary col" type="submit">
                                Sign in and join
                            </button>
                        </div>
                    </div>
                </div>
            </form>
        </div>
        <div class="col-lg-3"></div>
    </div>
</x-app>
