<!doctype html>
<html lang="en-US">
<head>
    <meta charset="utf-8">
    <meta name="description" content="RPG character builder and campaign manager">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <link href="/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.9.1/font/bootstrap-icons.css">
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
                @if (isset($navbar))
                    {!! $navbar !!}
                @else
                <li class="nav-item active">
                    <a class="nav-link" href="/dashboard">Home</a>
                </li>
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
                <form method="POST" action="{{ route('logout') }}" id="logout-form">
                    @csrf
                    <span class="nav-item">
                        <a class="nav-link" href="#" id="logout">Logout</a>
                    </span>
                </form>
            @endif
        </div>
    </div>
</nav>

<div class="container-fluid">
    <main>
    {{ $slot }}
    </main>
</div>

<script src="/js/bootstrap.bundle.min.js"></script>
<script src="/js/jquery.min.js"></script>
<script src="/js/app.js"></script>
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
