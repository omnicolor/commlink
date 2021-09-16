<!doctype html>
<html lang="en-US">
<head>
    <meta charset="utf-8">
    <meta name="description" content="RPG manager">
    <meta name="viewport"
        content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <link rel="stylesheet" href="/css/bootstrap.min.css">
    <title>Commlink RPG Manager - Forgot Password</title>
</head>

<body>
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
                @if (session('status'))
                <div class="row"><div class="col">
                    <div class="alert alert-success alert-dismissible"
                        role="alert">
                        {{ session('status') }}
                        <button aria-label="close" class="close"
                            data-dismiss="alert" type="button">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                </div></div>
                @endif
                <div class="card">
                    <form action="{{ route('password.email') }}"
                        class="card-body" id="forgot-form" method="POST"
                        novalidate>
                        @csrf

                        <div class="form-row mb-2">
                            <p>
                                Forgot your password? No problem. Just let us
                                know your email address and we will email you a
                                password reset link that will allow you to
                                choose a new one.
                            </p>

                            <label class="col-form-label col-md-3" for="email">
                                Email
                            </label>
                            <div class="col">
                                <input autocomplete="email" autofocus
                                    aria-describedby="email-help"
                                    class="form-control" id="email"
                                    inputmode="email" name="email" required
                                    type="email" value="{{ old('email') }}">
                                <div class="invalid-feedback">
                                    Enter your email address.
                                </div>
                            </div>
                        </div>

                        <div class="form-row">
                            <div class="col-3">&nbsp;</div>
                            <div class="col">
                                <button class="btn btn-primary" type="submit">
                                    Send link
                                </button>
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

var form = $('#forgot-form');
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
