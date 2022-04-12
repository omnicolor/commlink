<x-app>
    <x-slot name="title">Create character</x-slot>
    <x-slot name="head">
        <link rel="stylesheet" href="/css/datatables.min.css">
        <style>
            .points {
                position: fixed;
                right: 0;
                top: 5em;
            }
            .tooltip-inner {
                max-width: 600px;
                text-align: left;
            }
            tr.invalid {
                opacity: .5;
            }
            #points-button {
                position: fixed;
                right: 0;
                top: 5rem;
            }
            .offcanvas {
                border-bottom: 1px solid rgba(0, 0, 0, .2);
                border-top: 1px solid rgba(0, 0, 0, .2);
                bottom: 5rem;
                top: 4.5rem;
                width: 300px;
            }
        </style>
    </x-slot>
    @include('shadowrun5e.create-navigation')

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

    <div class="row">
        <div class="col-1"></div>
        <div class="col">
            <h1>Vehicles</h1>
        </div>
    </div>

    <div class="row">
        <div class="col-1"></div>
        <div class="col">
        </div>
        <div class="col-3"></div>
    </div>

    @include('shadowrun5e.create-next')
    </form>

    @include('shadowrun5e.create-points')

    <x-slot name="javascript">
        <script>
            let character = @json($character);
            let rulebooks = @json($books);
        </script>
        <script src="/js/Shadowrun5e/create-common.js"></script>
        <script src="/js/Shadowrun5e/Points.js"></script>
    </x-slot>
</x-app>
