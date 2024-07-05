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
                sharpening their weapons in anticipation of your arrival! We
                just need a few bits of information from you to get you
                registered with an account.
            </p>

            <p>Your other options:</p>
            <ul>
                <li>
                    <a href="{{ $change_url }}">
                        Wait! I already have a {{ config('app.name') }} account! Let me log in to {{ config('app.name') }} and join!
                    </a>
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

            <form action="{{ route('register') }}" class="card-body"
                id="register-form" method="POST" novalidate>
                @csrf
                <input name="invitation" type="hidden" value="{{ $invitation->id }}">
                <input name="token" type="hidden" value="{{ $invitation->hash() }}">

                <div class="form-row mb-2">
                    <label class="col-form-label col-md-3" for="name">
                        Username
                    </label>
                    <div class="col">
                        <input autocomplete="name" autofocus
                            aria-describedby="name-help"
                            class="form-control" id="name" name="name"
                            required type="text"
                            value="{{ $invitation->name }}">
                        <small class="form-text text-muted"
                            id="name-help">
                            What name you want your character(s) or
                            campaign(s) associated with.
                        </small>
                        <div class="invalid-feedback">
                            Enter your name.
                        </div>
                    </div>
                </div>

                <div class="form-row mb-2">
                    <label class="col-form-label col-md-3" for="email">
                        Email
                    </label>
                    <div class="col">
                        <input autocomplete="email"
                            aria-describedby="email-help"
                            class="form-control" id="email"
                            inputmode="email" name="email" required
                            type="email" value="{{ $invitation->email }}">
                        <small class="form-text text-muted"
                            id="email-help">
                            We'll never intentionally release your email
                            address at any time for any reason. Does not need to
                            be the email address you were invited with.
                        </small>
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
                        <input aria-describedby="password-help"
                            class="form-control" id="password"
                            name="password" required type="password">
                        <small class="form-text text-muted"
                            id="password-help">
                            Create a unique password for this site, or
                            have your
                            <a href="https://1password.com/">password manager</a>
                            do it for you.
                        </small>
                        <div class="invalid-feedback">
                            Password is required.
                        </div>
                    </div>
                </div>

                <div class="form-row mb-2">
                    <label class="col-form-label col-md-3 text-nowrap"
                        for="password-confirm">
                        Confirm password
                    </label>
                    <div class="col">
                        <input aria-describedby="confim-help"
                            class="form-control" id="password-confirm"
                            name="password_confirmation" required
                            type="password">
                        <small class="form-text text-muted"
                            id="confirm-help">
                            Makes sure there's no typos if you're still
                            typing passwords in.
                        </small>
                        <div class="invalid-feedback">
                            Enter your password again.
                        </div>
                    </div>
                </div>

                <div class="form-row">
                    <div class="col-3">&nbsp;</div>
                    <div class="col">
                        <button class="btn btn-primary" type="submit">
                            Create account
                        </button>
                    </div>
                </div>
            </form>
        </div>
        <div class="col-lg-3"></div>
    </div>
</x-app>
