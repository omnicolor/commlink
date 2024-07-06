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

            <h1>Magic</h1>
        </div>
    </div>

    @if ('magician' === $character->priorities['magic'])
    <div class="row">
        <div class="col-1"></div>
        <div class="col">
            <h2>Tradition</h2>

            <ul class="list-group" id="traditions">
                <li class="list-group-item no-tradition" id="tradition"
                    @if (null === $character->getTradition())
                        style="display:none"
                    @endif>
                    {{ $character->getTradition() }}
                </li>
                <li class="list-group-item no-tradition" id="no-tradition"
                    @if (null !== $character->getTradition())
                        style="display:none"
                    @endif>
                    No tradition chosen
                </li>
                <!--
                <li class="list-group-item no-tradition"
                    @if (null !== $character->getTradition())
                        style="display:none"
                    @endif>
                    <button class="btn btn-success"
                        data-bs-target="#traditions-modal"
                        data-bs-toggle="modal" type="button">
                        <span aria-hidden="true" class="bi bi-plus"></span>
                        Choose tradition
                    </button>
                </li>
                -->
            </ul>
        </div>
        <div class="col-3"></div>
    </div>

    <div class="row mt-4">
        <div class="col-1"></div>
        <div class="col">
            <h2>Spells</h2>

            <ul class="list-group" id="spells">
                @foreach ($character->getSpells() as $index => $spell)
                <li class="list-group-item">{{ $spell }}</li>
                @endforeach
                <li class="list-group-item" id="no-spells"
                    @if (0 !== count($character->getSpells()))
                        style="display:none"
                    @endif
                    >No spells</li>
                <!--
                <li class="list-group-item">
                    <button class="btn btn-success"
                        data-bs-target="#spells-modal" data-bs-toggle="modal"
                        type="button">
                        <span aria-hidden="true" class="bi bi-plus"></span>
                        Learn spell
                    </button>
                </li>
                -->
            </ul>
        </div>
        <div class="col-3"></div>
    </div>

    @elseif ('adept' === $character->priorities['magic'])
    <div class="row">
        <div class="col-1"></div>
        <div class="col">
            <h2>Adept powers</h2>

            <ul class="list-group" id="powers">
                <li class="list-group-item" id="no-powers">No powers</li>
                <!--
                <li class="list-group-item">
                    <button class="btn btn-success" data-target="#powers-modal"
                        data-toggle="modal" type="button">
                        <span aria-hidden="true" class="oi oi-plus"></span>
                        Add adept power
                    </button>
                </li>
                -->
            </ul>
        </div>
        <div class="col-3"></div>
    </div>
    @endif

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
