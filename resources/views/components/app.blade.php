<!doctype html>
<html lang="en-US">
<head>
    <meta charset="utf-8">
    <meta name="description" content="Shadowrun manager">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.0-beta3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-eOJMYsd53ii+scO/bJGFsiCZc+5NDVN2yr8+0RDqr0Ql0h+rP48ckxlpbzKgwra6" crossorigin="anonymous">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.4.1/font/bootstrap-icons.css">
    <title>
        {{ config('app.name') }}
        @if (isset($title))
            &mdash; {{ $title }}
        @endif
    </title>
    @if (isset($head))
        {!! $head !!}
    @endif
</head>

<body>
<nav class="navbar navbar-expand-lg navbar-dark bg-dark">
    <div class="container-fluid">
        <span class="navbar-brand">Commlink</span>
        <button aria-controls="navbarSupportedContent" aria-expanded="false"
            aria-label="Toggle navigation" class="navbar-toggler"
            data-bs-target="#navbarSupportedContent" data-bs-toggle="collapse"
            type="button">
            <span class="navbar-toggler-icon"></span>
        </button>
        <div class="collapse navbar-collapse" id="navbarSupportedContent">
            <ul class="navbar-nav mr-auto">
                <li class="nav-item active">
                    <a class="nav-link" href="/dashboard">Home</a>
                </li>
                @if (isset($navbar))
                    {!! $navbar !!}
                @endif
            </ul>
        </div>
        <div class="d-flex">
            @if (Auth::user())
                <span class="navbar-text">
                    {{ Auth::user()->email }}
                    <small><a href="/settings">
                        <i class="bi bi-gear" title="Settings"></i>
                    </a></small>
                </span>
                <span class="nav-item">
                    <form method="POST" action="{{ route('logout') }}" id="logout-form">
                        @csrf
                        <a class="nav-link" href="#" id="logout">Logout</a>
                    </form>
                </span>
            @endif
        </div>
    </div>
</nav>

<div class="container-fluid">
    <main>
    {{ $slot }}
    </main>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.0.0-beta3/dist/js/bootstrap.bundle.min.js" integrity="sha384-JEW9xMcG8R+pH31jmWH6WWP0WintQrMb4s7ZOdauHnUtxwoG2vI5DkLtS3qm9Ekf" crossorigin="anonymous"></script>
<script src="/js/jquery-3.3.1.min.js"></script>
<script>
$('#logout').on('click', function (e) {
    e.preventDefault();
    $('#logout-form').submit();
});
</script>
@if (isset($javascript))
    {!! $javascript !!}
@endif
</body>
</html>
