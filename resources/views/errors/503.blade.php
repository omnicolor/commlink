<!doctype html>
<html lang="en-US">
<head>
    <meta charset="utf-8">
    <meta name="description" content="RPG character builder and campaign manager">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <link rel="stylesheet" href="/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.9.1/font/bootstrap-icons.css">
    <link rel="icon" type="image/png" href="/favicon.ico" />
    <title>{{ config('app.name') }} RPG Manager - Login</title>
</head>

<body>
<nav class="navbar navbar-expand-lg navbar-dark bg-dark">
    <div class="container-fluid">
        <a class="navbar-brand active" href="/">{{ config('app.name') }}</a>
        <div class="collapse navbar-collapse" id="navbarSupportedContent">
            <ul class="navbar-nav me-auto mb-2 mb-lg-0">
                <li class="nav-item">
                    <a class="nav-link" href="/about">What is {{ config('app.name') }}?</a>
                </li>
            </ul>
        </div>
    </div>
</nav>

<div class="container-fluid">
    <div class="row mt-4">
        <div class="col-lg-3"></div>
        <div class="col">
            <div class="row">
                <div class="col">
                    <h1>Maintenance mode</h1>

                    <p>
                        Sorry for the inconvenience, we're currently upgrading
                        the system or doing some database work. We'll be back
                        shortly!
                    </p>

                    <p>
                        In the meantime, you can learn about
                        {{ config('app.name') }} and its features on the
                        <a href="/about">About</a> page.
                    </p>
                </div>
            </div>
        </div>
        <div class="col-lg-3"></div>
    </div>
</div>

</body>
</html>
