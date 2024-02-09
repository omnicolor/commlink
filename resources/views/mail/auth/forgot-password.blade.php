<!doctype html>
<html lang="en-US">
<head>
    <meta charset="utf-8">
    <title>{{ config('app.name') }} password reset</title>
    <link rel="stylesheet" href="{{ config('app.url') }}/css/bootstrap.min.css">
</head>

<body>
<nav class="navbar navbar-expand-lg navbar-dark bg-dark">
    <div class="container-fluid">
        <a class="navbar-brand active" href="{{ config('app.url') }}">{{ config('app.name') }}</a>
    </div>
</nav>
<div class="container-fluid">
    <div class="row">
        <div class="col-lg-3"></div>
        <div class="col">
            <h1>{{ config('app.name') }} password reset</h1>

            <p>
                Someone has requested a password reset for your
                {{ config('app.name') }} account. If it wasn't you, you may
                safely delete this email. If it was you and you'd still like to
                change your password, click the button below.
            </p>

            <p>
                <a class="btn btn-primary btn-lg" href="{{ $url }}">
                    Reset password
                </a>
            </p>

            <p>
                If you're having trouble clicking the
                &ldquo;Reset password&rdquo; button, copy and paste this URL
                into your web browser (or just click it):
                <a href="{{ $url }}">{{ $url }}</a>
            </p>
        </div>
        <div class="col-lg-3"></div>
    </div>
</div>
</body>
</html>
