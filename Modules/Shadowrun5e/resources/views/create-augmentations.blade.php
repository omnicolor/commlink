<x-app>
    <x-slot name="title">Create character</x-slot>
    <x-slot name="head">
        <link href="/css/datatables.min.css" rel="stylesheet">
        <link href="/css/Shadowrun5e/character-generation.css" rel="stylesheet">
    </x-slot>
    @include('shadowrun5e::create-navigation')

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
            <p>Current essence: <span id="essence">{{ $character->essence }}</span></p>

            <ul class="list-group" id="augmentations">
                @foreach ($character->getAugmentations() as $index => $augmentation)
                <li class="list-group-item">
                    {{ $augmentation }}
                </li>
                @endforeach
                <li class="list-group-item" id="no-augmentations"
                    @if (0 !== count($character->getAugmentations()))
                        style="display:none"
                    @endif>No augmentations</li>
                <!--
                <li class="list-group-item">
                    <button class="btn btn-success" data-target="#augmentations-modal"
                                                    data-toggle="modal" type="button">
                        <span aria-hidden="true" class="oi oi-plus"></span>
                        Add augmentation
                    </button>
                </li>
                -->
            </ul>
        </div>
        <div class="col-3"></div>
    </div>

    @include('shadowrun5e::create-next')
    </form>

    @include('shadowrun5e::create-points')

    <x-slot name="javascript">
        <script>
            let character = @json($character);
            let rulebooks = @json($books);
        </script>
        <script src="/js/Shadowrun5e/create-common.js"></script>
        <script src="/js/Shadowrun5e/Points.js"></script>
    </x-slot>
</x-app>
