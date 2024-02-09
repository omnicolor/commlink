<!doctype html>
<html lang="en-US">
<head>
    <meta charset="utf-8">
    <meta name="description" content="RPG character builder and campaign manager">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <link rel="stylesheet" href="/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.9.1/font/bootstrap-icons.css">
    <link rel="icon" type="image/png" href="/favicon.ico" />
</head>

<body>
<nav class="navbar navbar-expand-lg navbar-dark bg-dark">
    <div class="container-fluid">
        <a class="navbar-brand active" href="/">{{ config('app.name') }}</a>
    </div>
</nav>

<div class="container-fluid">
    <div class="row mt-4">
        <div class="col-lg-3"></div>
        <div class="col">
            {{ $slot }}
        </div>
        <div class="col-lg-3"></div>
    </div>
</div>
</body>
</html>
