@php
use Modules\Shadowrun5e\Models\Quality;
@endphp
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

    <datalist id="allergy-uncommon">
        <option value="Silver">
        <option value="Gold">
        <option value="Antibiotics">
        <option value="Grass">
    </datalist>
    <datalist id="allergy-common">
        <option value="Sunlight">
        <option value="Seafood">
        <option value="Bees">
        <option value="Pollen">
        <option value="Pollutants">
        <option value="Wi-Fi sensitivity">
        <option value="Soy">
        <option value="Wheat">
    </datalist>
    <datalist id="addictions">
        <option value="BTLs">
        <option value="Novacoke">
        <option value="Bliss">
        <option value="Tempo">
        <option value="Foci">
        <option value="Gambling">
        <option value="Sex">
        <option value="Alcohol">
    </datalist>

    <form action="{{ route('shadowrun5e.create-qualities') }}" method="POST">
    @csrf

    <div class="row">
        <div class="col-1"></div>
        <div class="col">
            <h1>Qualities</h1>
        </div>
    </div>

    <div class="row my-4">
        <div class="col-1"></div>
        <div class="col">
            <p>
                Qualities help round out your character’s personality while also
                providing a range of benefits or penalties. There are two types
                of Qualities—Positive Qualities, which provide gameplay bonuses
                and require an expenditure of Karma; and Negative Qualities,
                which impose gameplay penalties but also give bonus Karma the
                player can spend in other areas.
            </p>

            <p>
                As mentioned earlier, the character starts the character
                creation process with 25 Karma, and some of that can be spent to
                buy Qualities. Players can spend all of it, some of it, or none
                of it based on what they want their character to have and how
                much Karma they want to save for later. Additionally, at
                creation characters can only possess at most 25 Karma worth of
                Positive Qualities and 25 Karma worth of Negative Qualities.
            </p>
        </div>
        <div class="col-3"></div>
    </div>

    <div class="row">
        <div class="col-1"></div>
        <div class="col">
            <ul class="list-group" id="qualities">
                @foreach ($character->qualities ?? [] as $rawQuality)
                    @php
                        $quality = new Quality($rawQuality['id'], $rawQuality);
                    @endphp
                <li class="list-group-item">
                    @can('view data')
                    <span data-bs-html="true" data-bs-toggle="tooltip"
                        title="<p>{{ str_replace('||', '<\/p><p>', $quality->description) }}</p>">
                        {{ $quality }}
                    </span>
                    @else
                        {{ $quality }}
                    @endcan
                    <input name="quality[]" type="hidden" value="{{ $quality->id}}">
                    <div class="float-end">
                        <button class="btn btn-danger btn-sm"
                            data-id="{{ $quality->id }}" role="button">
                            <span class="bi bi-dash"></span>
                            Remove
                        </button>
                    </div>
                </li>
                @endforeach
                <li class="list-group-item" id="no-qualities"
                    @if (!empty($character->qualities ?? []))
                        style="display:none"
                    @endif >
                    No qualities selected
                </li>
                <li class="list-group-item">
                    <button class="btn btn-success" data-bs-toggle="modal"
                        data-bs-target="#quality-modal" id="add-quality"
                        type="button">
                        <span aria-hidden="true" class="bi bi-plus"></span>
                        Add quality
                    </button>
                </li>
            </ul>
        </div>
        <div class="col-3"></div>
    </div>

    @include('shadowrun5e::create-next')
    </form>

    <div class="modal" id="quality-modal" tabindex="-1" role="dialog">
        <div class="modal-dialog modal-xl" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Choose qualities</h5>
                    <button aria-label="Close" class="btn-close"
                        data-bs-dismiss="modal" type="button"></button>
                </div>
                <div class="modal-body row">
                    <div class="col">
                        <div class="row mx-1">
                            <select class="col form-control form-control-sm"
                                id="filter-class">
                                <option value="">All qualities</option>
                                <option value="positive">Positive</option>
                                <option value="negative">Negative</option>
                            </select>
                            <select class="col form-control form-control-sm mx-1"
                                id="filter-ruleset">
                                <option value="">All rulesets</option>
                                @foreach ($books as $id => $book)
                                    <option value="{{ $id }}">{{ $book }}</option>
                                @endforeach
                            </select>
                            <input class="col form-control form-control-sm"
                                id="search-qualities" placeholder="Search qualities"
                                type="search">
                        </div>
                        <div class="row">
                            <table class="table" id="quality-list"
                                style="width: 100%;">
                                <thead>
                                    <tr>
                                        <th scope="col">Name</th>
                                        <th scope="col">Karma</th>
                                        <th scope="col">Ruleset</th>
                                        <th scope="col">Class</th>
                                    </tr>
                                </thead>
                                <tbody>
                                </tbody>
                            </table>
                        </div>
                    </div>
                    <div class="col">
                        <div id="click-panel">
                            Click a quality for more information about it.
                        </div>
                        <div id="info-panel" style="display: none;">
                            <h3 id="quality-name">.</h3>
                            <small class="text-muted" id="quality-type"></small>
                            @can('view data')
                            <div class="row mt-2">
                                <p class="col" id="quality-description"></p>
                            </div>
                            @endcan
                            <div class="row mt-2">
                                <div class="col-2">Karma</div>
                                <div class="col" id="quality-karma"></div>
                            </div>
                            <div class="row">
                                <div class="col-2">Ruleset</div>
                                <div class="col" id="quality-ruleset"></div>
                            </div>
                            <div class="row">
                                <div class="col-2">Validation</div>
                                <div class="col" id="quality-validation"></div>
                            </div>
                            <div class="mt-2" id="quality-add"></div>
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
            const trusted = !!{{ (int)\Auth::user()->hasPermissionTo('view data') }};
        </script>
        <script src="/js/Shadowrun5e/create-common.js"></script>
        <script src="/js/Shadowrun5e/Points.js"></script>
        <script src="/js/datatables.min.js"></script>
        <script src="/js/Shadowrun5e/create-qualities.js"></script>
    </x-slot>
</x-app>
