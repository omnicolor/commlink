<x-guest-layout>
    @foreach ($errors->all() as $error)
    <div class="row">
        <div class="col">
            <div class="alert alert-danger alert-dismissible fade show"
                role="alert">
                {{ $error }}
                <button type="button" class="btn-close" data-bs-dismiss="alert"
                    aria-label="Close"></button>
            </div>
        </div>
    </div>
    @endforeach

    <div class="card">
        <div class="card-body">
            <h1 class="card-title">Password reset</h1>
            <form method="POST" action="{{ route('password.update') }}">
                @csrf
                <input type="hidden" name="token" value="{{ $request->route('token') }}">

                <div class="form-row mb-2">
                    <label class="col-form-label col-md-3" for="email">
                        Email
                    </label>
                    <div class="col">
                        <input autocomplete="email" autofocus
                            class="form-control" id="email" inputmode="email"
                            name="email" required type="email">
                    </div>
                </div>

                <div class="form-row mb-2">
                    <label class="col-form-label col-md-3 text-nowrap"
                        for="password">
                        Password
                    </label>
                    <div class="col">
                        <input class="form-control" id="password"
                            name="password" required type="password">
                        <div class="invalid-feedback">
                            Enter your password.
                        </div>
                    </div>
                </div>

                <div class="form-row mb-2">
                    <label class="col-form-label col-md-3 text-nowrap"
                        for="password_confirmation">
                        Confirm password
                    </label>
                    <div class="col">
                        <input class="form-control" id="password_confirmation"
                            name="password_confirmation" required
                            type="password">
                        <div class="invalid-feedback">
                            Re-enter your password.
                        </div>
                    </div>
                </div>

                <div class="form-row mb-2">
                    <button class="btn btn-primary col" type="submit">
                        Reset password
                    </button>
                </div>
            </form>
        </div>
    </div>
</x-guest-layout>
