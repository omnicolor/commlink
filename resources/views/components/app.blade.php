<!doctype html>
<html lang="en-US">
<head>
    <meta charset="utf-8">
    <meta name="description" content="Shadowrun manager">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <link href="/css/bootstrap.min.css" rel="stylesheet">
    <link href="/css/open-iconic-bootstrap.min.css" rel="stylesheet">
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
<nav class="navbar navbar-expand navbar-dark bg-dark justify-content-between">
    <a class="navbar-brand" href="/">Commlink</a>
    <div class="collapse navbar-collapse" id="navbarSupportedContent">
        <ul class="navbar-nav mr-auto">
            <li class="nav-item active"><a class="nav-link" href="/dashboard">
                Home
            </a></li>
        </ul>
        @if (Auth::user())
        <span class="navbar-text">
            {{ Auth::user()->email }}
            <small><a href="/settings">
                <span class="oi oi-cog" title="Settings"></span>
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
</nav>

<div class="container-fluid">
    <main>
    {{ $slot }}
    </main>
</div>

<script src="/js/popper.min.js"></script>
<script src="/js/jquery-3.3.1.min.js"></script>
<script src="/js/bootstrap.bundle.min.js"></script>
<script>
$('#logout').on('click', function (e) {
    e.preventDefault();
    $('#logout-form').submit();
});
</script>
</body>
</html>
