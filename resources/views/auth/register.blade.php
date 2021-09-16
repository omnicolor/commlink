<!doctype html>
<html lang="en-US">
<head>
    <meta charset="utf-8">
    <meta name="description" content="RPG manager">
    <meta name="viewport"
        content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <link rel="stylesheet" href="/css/bootstrap.min.css">
    <title>Commlink RPG Manager - Register</title>
</head>

<body id="register">
<nav class="navbar navbar-expand navbar-dark bg-dark justify-content-between">
    <a class="navbar-brand" href="/">Commlink</a>
    <div class="collapse navbar-collapse" id="navbarSupportedContent"></div>
</nav>

<div class="container-fluid">
    <div class="row mt-4">
        <div class="col-lg-3"></div>
        <div class="col">
            <div class="row"><div class="col">
                @foreach ($errors->all() as $error)
                <div class="row"><div class="col">
                    <div class="alert alert-danger alert-dismissible"
                        role="alert">
                        {{ $error }}
                        <button aria-label="close" class="close"
                            data-dismiss="alert" type="button">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                </div></div>
                @endforeach
                <div class="card">
                    <form action="{{ route('register') }}" class="card-body"
                        id="register-form" method="POST" novalidate>
                        @csrf

                        <div class="form-row mb-2">
                            <label class="col-form-label col-md-3" for="name">
                                Username
                            </label>
                            <div class="col">
                                <input autocomplete="name" autofocus
                                    aria-describedby="name-help"
                                    class="form-control" id="name" name="name"
                                    required type="text">
                                <small class="form-text text-muted"
                                    id="name-help">
                                    What name you want your character(s) or
                                    campaign(s) associated with.
                                </small>
                                <div class="invalid-feedback">
                                    Enter your username.
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
                                    type="email">
                                <small class="form-text text-muted"
                                    id="email-help">
                                    We'll never intentionally release your email
                                    address at any time for any reason.
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
                                    <a href="https://www.lastpass.com/">password</a>
                                    <a href="https://1password.com/">manager</a>
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
                                <a class="btn btn-outline-secondary"
                                    href="{{ route('login') }}">
                                    Already registered?
                                </a>
                            </div>
                        </div>
                    </form>
                </div>
            </div></div>
        </div>
        <div class="col-lg-3"></div>
    </div>
</div>

<script src="/js/jquery.min.js"></script>
<script src="/js/bootstrap.bundle.min.js"></script>
<script>
'use strict';

var form = $('#register-form');
form.on('submit', function(event) {
    if (form[0].checkValidity() === false) {
        event.preventDefault();
        event.stopPropagation();
    }
    form.addClass('was-validated');
});
</script>
</body>
</html>
