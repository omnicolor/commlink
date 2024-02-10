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

            <h1>Armor</h1>
        </div>
    </div>

    <div class="row">
        <div class="col-1"></div>
        <div class="col">
            <ul class="list-group" id="armor">
                @foreach ($character->getArmor() as $index => $armor)
                    <li class="list-group-item" data-index="{{ $index }}">
                        {{ $armor }}
                        <!--
                        <div class="float-end">
                            <button class="btn btn-primary btn-sm modify mr-1"
                                data-bs-target="#modification-modal"
                                data-bs-toggle="modal" type="button">
                                <span aria-hidden="true" class="bi bi-wrench"></span>
                                Modify
                            </button>
                            <button class="btn btn-danger btn-sm" type="button">
                                <span aria-hidden="true" class="bi bi-dash"></span>
                                Remove
                            </button>
                        </div>
                        -->
                    </li>
                @endforeach
                <li class="list-group-item" id="no-armor"
                    @if (0 !== count($character->getArmor()))
                        style="display:none"
                    @endif>No armor</li>
                <!--
                <li class="list-group-item">
                    <button class="btn btn-success" data-bs-toggle="modal"
                        data-bs-target="#armor-modal" type="button">
                        <span aria-hidden="true" class="bi bi-plus"></span>
                        Add armor
                    </button>
                </li>
                -->
            </ul>
        </div>
        <div class="col-3"></div>
    </div>

    @include('Shadowrun5e.create-next')
    </form>

    @include('Shadowrun5e.create-points')

    <x-slot name="javascript">
        <script>
            let character = @json($character);
        </script>
        <script src="/js/Shadowrun5e/create-common.js"></script>
        <script src="/js/Shadowrun5e/Points.js"></script>
    </x-slot>
</x-app>
