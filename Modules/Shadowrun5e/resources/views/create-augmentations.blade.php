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

    <form action="{{ route('shadowrun5e.create-augmentations') }}" method="POST">
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
                    <button class="btn btn-success" data-bs-target="#augmentations-modal"
                        data-bs-toggle="modal" type="button">
                        <span aria-hidden="true" class="bi bi-plus"></span>
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

    <div class="modal" id="augmentations-modal" tabindex="-1" role="dialog">
        <div class="modal-dialog modal-xl" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Choose augmentation</h5>
                    <button aria-label="Close" class="btn-close"
                        data-bs-dismiss="modal" type="button"></button>
                </div>
                <div class="modal-body row">
                    <div class="col">
                        <div class="row">
                            <table class="table" id="weapons-list" style="width:100%">
                                <thead>
                                    <tr>
                                        <th scope="col">Name</th>
                                        <th scope="col">Essence</th>
                                        <th scope="col">Avail.</th>
                                        <th scope="col">Cost</th>
                                        <th scope="col">Ruleset</th>
                                        <th scope="col">Type</th>
                                    </tr>
                                </thead>
                                <tbody>
                                </tbody>
                            </table>
                        </div>
                    </div>
                    <div class="col">
                        <div id="click-panel">
                            Click an augmentation for more information about it.
                        </div>
                        <div id="info-panel" class="d-none">
                            <h3 id="augmentation-name">.</h3>
                            <small class="text-muted" id="augmentation-type"></small>
                            @can('view data')
                            <p id="augmentation-description"></p>
                            @endcan

                            <div class="row">
                                <div class="col-2"><strong>Cost:</strong></div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    @include('shadowrun5e::create-points')

    <x-slot name="javascript">
        <script>
            let character = @json($character);
            let rulebooks = @json($books);
            const trusted = !!{{ (int)$user->hasPermissionTo('view data') }};
            $(function () {
                let points = new Points(character);
                updatePointsToSpendDisplay(points);
            });
        </script>
        <script src="/js/Shadowrun5e/create-common.js"></script>
        <script src="/js/Shadowrun5e/Points.js"></script>
    </x-slot>
</x-app>
