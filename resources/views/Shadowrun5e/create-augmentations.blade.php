<x-app>
    <x-slot name="title">Create character</x-slot>
    <x-slot name="head">
        <link href="/css/datatables.min.css" rel="stylesheet">
        <link href="/css/Shadowrun5e/character-generation.css" rel="stylesheet">
    </x-slot>
    @include('Shadowrun5e.create-navigation')

    @if ($errors->any())
        <div class="alert alert-danger mt-4">
            <ul>
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <form action="" method="POST">
    @csrf

    <div class="row mt-3">
        <div class="col-1"></div>
        <div class="col">
            <div class="alert alert-danger" role="alert">
                This page is currently under development.
            </div>

            <h1>Augmentations</h1>
        </div>
    </div>

    <div class="row">
        <div class="col-1"></div>
        <div class="col">
        </div>
        <div class="col-3"></div>
    </div>

    @include('Shadowrun5e.create-next')
    </form>

    @include('Shadowrun5e.create-points')

    <x-slot name="javascript">
        <script>
            let character = @json($character);
            let rulebooks = @json($books);
        </script>
        <script src="/js/Shadowrun5e/create-common.js"></script>
        <script src="/js/Shadowrun5e/Points.js"></script>
    </x-slot>
</x-app>
