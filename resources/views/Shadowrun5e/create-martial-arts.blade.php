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

    <form action="{{ route('shadowrun5e.create-martial-arts') }}" method="POST">
    @csrf

    <div class="row mt-4">
        <div class="col-1"></div>
        <div class="col">
            <h1>Martial arts styles</h1>

            <p>
                Each martial art style has six techniques for a character to
                choose. Buying a new style costs 7 Karma, and when you buy that
                style you may then choose a technique to go with it. Buying
                additional techniques costs 5 Karma. At character creation, you
                can buy up to 5 total techniques, in a single style, which costs
                27 Karma. You can only buy one style at character creation.
            </p>
        </div>
        <div class="col-3"></div>
    </div>

    <div class="row">
        <div class="col-1"></div>
        <div class="col">
            <ul class="list-group" id="martial-arts-styles">
                @if (0 !== count($styles))
                <li class="list-group-item style">
                    <span data-bs-toggle="tooltip" data-bs-html="true"
                        title="<p>{{ str_replace('||', '</p><p>', $styles[0]->description) }}</p>">
                        {{ $styles[0] }}
                    </span>
                    <div class="float-end">
                        <button class="btn btn-danger btn-sm" role="button">
                            <span aria-hidden="true" class="bi bi-dash"></span>
                            Remove
                        </button>
                    </div>
                    <input name="style" type="hidden" value="{{ $styles[0]->id }}">
                </li>
                <li class="list-group-item no-styles" style="display:none">
                    No styles learned
                </li>
                <li class="list-group-item no-styles" style="display:none">
                    <button class="btn btn-success"
                        data-bs-target="#styles-modal" data-bs-toggle="modal"
                        id="add-style" type="button">
                        <span aria-hidden="true" class="bi bi-plus"></span>
                        Add style
                    </button>
                </li>
                @else
                <li class="list-group-item no-styles">No styles learned</li>
                <li class="list-group-item no-styles">
                    <button class="btn btn-success"
                        data-bs-target="#styles-modal" data-bs-toggle="modal"
                        id="add-style" type="button">
                        <span aria-hidden="true" class="bi bi-plus"></span>
                        Add style
                    </button>
                </li>
                @endif
            </ul>
        </div>
        <div class="col-3"></div>
    </div>

    @if (0 === count($styles))
    <div class="row my-4" style="display:none" id="martial-art-technique-div">
    @else
    <div class="row my-4" id="martial-art-technique-div">
    @endif
        <div class="col-1"></div>
        <div class="col">
            <h1>Martial arts techniques</h1>
            <ul class="list-group" id="martial-arts-techniques">
                @foreach ($techniques as $technique)
                <li class="list-group-item">
                    <span class="tooltip-anchor" data-bs-html="true"
                        data-bs-toggle="tooltip"
                        title="<p>{{ str_replace('||', '</p><p>', $technique->description) }}</p>">
                        {{ $technique }}
                    </span>
                    <div class="float-end">
                        <button class="btn btn-danger btn-sm"
                            data-id="{{ $technique->id }}" type="button">
                            <span aria-hidden="true" class="bi bi-dash"></span>
                            Remove
                        </button>
                    </div>
                    <input name="techniques[]" type="hidden" value="{{ $technique->id }}">
                </li>
                @endforeach
                <li class="list-group-item" id="no-techniques"
                    @if (0 !== count($techniques))
                        style="display:none"
                    @endif
                    >
                    No techniques learned
                </li>
                <li class="list-group-item">
                    <button class="btn btn-success"
                        data-bs-target="#techniques-modal"
                        data-bs-toggle="modal" id="add-technique" type="button">
                        <span aria-hidden="true" class="bi bi-plus"></span>
                        Add technique
                    </button>
                </li>
            </ul>
        </div>
        <div class="col-3"></div>
    </div>

    @include('Shadowrun5e.create-next')
    </form>

    <div class="modal" id="styles-modal" tabindex="-1" role="dialog">
        <div class="modal-dialog modal-lg" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Choose style</h5>
                    <button type="button" class="close" data-bs-dismiss="modal"
                        aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body row">
                    <div class="col">
                        <div class="row mx-1">
                            <input class="col form-control form-control-sm"
                                id="search-styles" placeholder="Search styles"
                                type="search">
                        </div>
                        <div class="row">
                            <table class="table" id="styles-list">
                                <thead>
                                    <tr>
                                        <th scope="col">Name</th>
                                    </tr>
                                </thead>
                                <tbody>
                                </tbody>
                            </table>
                        </div>
                    </div>
                    <div class="col">
                        <div id="styles-click-panel">
                            <p>You may learn a martial arts style for 7 karma. A
                            character may only learn 1 during character
                            creation. Each style comes with 1 free
                            technique.</p>

                            <p>Click a style for more information about it.</p>
                        </div>
                        <div id="styles-info-panel" style="display: none;">
                            <h3 id="style-name">.</h3>
                            <p id="style-description"></p>
                            <p>Ruleset: <span id="style-ruleset"></span></p>
                            <h5>Available techniques:</h5>
                            <ul id="style-techniques"></ul>
                            <button class="btn btn-success" type="button">
                                <span aria-hidden="true" class="bi bi-plus"></span>
                                Learn style
                            </button>
                            <button class="btn btn-secondary"
                                data-bs-dismiss="modal" type="button">
                                Cancel
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="modal" id="techniques-modal" tabindex="-1" role="dialog">
        <div class="modal-dialog modal-xl" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Choose technique</h5>
                    <button type="button" class="close" data-bs-dismiss="modal"
                        aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body row">
                    <div class="col">
                        <div class="row mx-1">
                            <input class="form-control form-control-sm"
                                id="search-techniques"
                                placeholder="Search techniques" type="search">
                        </div>
                        <div class="row">
                            <table class="table" id="techniques-list">
                                <thead>
                                    <tr>
                                        <th scope="col">Name</th>
                                    </tr>
                                </thead>
                                <tbody>
                                </tbody>
                            </table>
                        </div>
                    </div>
                    <div class="col">
                        <div id="techniques-click-panel">
                            <p>
                                A character may choose one technique to go with
                                their martial arts style. Additional techniques
                                cost 5 karma.
                            </p>

                            <p>Select a technique for more information.</p>
                        </div>
                        <div id="techniques-info-panel" style="display: none;">
                            <h3 id="technique-name">.</h3>
                            <p id="technique-description"></p>
                            <p>Ruleset: <span id="technique-ruleset"></span></p>
                            <button class="btn btn-success" type="button">
                                <span aria-hidden="true" class="oi oi-plus"></span>
                                Learn technique
                            </button>
                            <button class="btn btn-secondary"
                                data-bs-dismiss="modal" type="button">
                                Cancel
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    @include('Shadowrun5e.create-points')

    <x-slot name="javascript">
        <script>
            let character = @json($character);
            if (undefined === character.martialArts || null === character.martialArts) {
                character.martialArts = {
                    styles: [],
                    techniques: []
                };
            }
            let rulebooks = @json($books);
        </script>
        <script src="/js/Shadowrun5e/create-common.js"></script>
        <script src="/js/Shadowrun5e/Points.js"></script>
        <script src="/js/datatables.min.js"></script>
        <script src="/js/Shadowrun5e/create-martial-arts.js"></script>
    </x-slot>
</x-app>
