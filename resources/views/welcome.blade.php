<!doctype html>
<html lang="en-US">
<head>
    <meta charset="utf-8">
    <meta name="description" content="RPG character builder and campaign manager">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <link rel="stylesheet" href="/css/bootstrap.min.css">
    <title>Commlink RPG Manager - Login</title>
</head>

<body>
<nav class="navbar navbar-expand-lg navbar-dark bg-dark">
    <div class="container-fluid">
        <a class="navbar-brand active" href="/">Commlink</a>
        <div class="collapse navbar-collapse" id="navbarSupportedContent">
            <ul class="navbar-nav me-auto mb-2 mb-lg-0">
                <li class="nav-item">
                    <a class="nav-link" href="/about">What is Commlink?</a>
                </li>
            </ul>
        </div>
    </div>
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
                    <form action="{{ route('login') }}" class="card-body"
                        id="login-form" method="POST" novalidate>
                        @csrf

                        <div class="form-row mb-2">
                            <label class="col-form-label col-md-3" for="email">
                                Email
                            </label>
                            <div class="col">
                                <input autocomplete="email" autofocus
                                    aria-describedby="email-help"
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

                    <div class="form-row">
                        <div class="col-3">&nbsp;</div>
                        <div class="col">
                            <button class="btn btn-primary" type="submit">
                                Login
                            </button>
                            <a class="btn btn-outline-secondary"
                                href="{{ route('register') }}">
                                Create account
                            </a>
                            <a class="btn btn-outline-secondary"
                                href="{{ route('password.request') }}">
                                Forgot password
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

var form = $('#login-form');
form.on('submit', function(event) {
    if (form[0].checkValidity() === false) {
        event.preventDefault();
        event.stopPropagation();
    }
    var email = $('#email');
    email.toggleClass('is-invalid', email === '');
    var password = $('#password');
    password.toggleClass('is-invalid', password === '');
    form.addClass('was-validated');
});
</script>
</body>
</html>
