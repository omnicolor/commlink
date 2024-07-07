<x-app>
    <x-slot name="title">{{ $character->name }}</x-slot>
    <x-slot name="head">
        <style>
            .name {
                border: 3px solid #e8cd0c;
                padding: 8px;
                position: relative;
            }
            .name div {
                background: #e8cd0c;
                border: 1px solid #ffffff;
                color: #000000;
                left: 1em;
                padding: 2px;
                position: absolute;
                top: -1em;
            }
            .attributes .col div {
                background: #ffffff;
                border: 2px solid #e8cd0c;
                color: #000000;
                height: 7em;
                text-align: center;
                width: 7em;
            }
            .attributes .col div div {
                background: #e8cd0c;
                color: #000000;
                height: 2em;
                width: 100%;
            }
            .attributes span {
                font-size: xxx-large;
            }
            .meta {
                background: #97a8b2;
                padding: 0 2em;
                margin-right: 2em;
            }
            .meta .col {
                background: #ffffff;
                border: 3px solid #142e53;
                height: 3em;
                padding: 0.5em 0.5em 0.5em 8em;
                position: relative;
            }
            .meta .col div {
                background: #142e53;
                bottom: 0;
                color: #ffffff;
                height: 1.5em;
                left: 0;
                padding: 0 1em;
                position: absolute;
            }
            .values {
                background: #8fa0a8;
            }
            .values .col {
                background: #ffffff;
                border: 3px solid #ea76c7;
                padding: 1.5em;
                position: relative;
            }
            .values .col div {
                background: #ea76c7;
                color: #ffffff;
                height: 1.5em;
                left: 0;
                top: 0;
                position: absolute;
            }
            .def {
                align-items: center;
                background: #c7c7c7;
                display: flex;
                flex-wrap: wrap;
                justify-content: space-around;
                margin-top: 4vw;
            }
            .shape-outer {
                clip-path: polygon(25% 0%, 75% 0%, 100% 50%, 75% 100%, 25% 100%, 0% 50%);
                display: flex;
                flex-shrink: 0;
                height: calc(100px + 4vw);
                margin-bottom: -2vw;
                margin-top: -2vw;
                width: calc(100px + 4vw);
                -webkit-clip-path: polygon(25% 0%, 75% 0%, 100% 50%, 75% 100%, 25% 100%, 0% 50%);
            }
            .shape-inner {
                background: #ffffff;
                height: calc(80px + 4vw);
                width: calc(80px + 4vw);
                margin: auto;
                text-align: center;
                -webkit-clip-path: polygon(25% 0%, 75% 0%, 100% 50%, 75% 100%, 25% 100%, 0% 50%);
                clip-path: polygon(25% 0%, 75% 0%, 100% 50%, 75% 100%, 25% 100%, 0% 50%);
            }
            .def .blue {
                background-image: linear-gradient(45deg, #426386, #bec4de);
            }
            .def .initiative {
                background-image: linear-gradient(45deg, #434341, #a19e99);
            }
            .def .armor {
                background-image: linear-gradient(45deg, #b76904, #f9e294);
            }
            .def .blue div div {
                background: #b2b9cc;
                color: white;
                font-size: large;
                margin-bottom: .75vw;
            }
            .def .initiative div div {
                background: #a6a39e;
                color: white;
                font-size: large;
                margin-bottom: .75vw;
            }
            .def .armor div div {
                background: #eed690;
                color: white;
                font-size: large;
                margin-bottom: .75vw;
            }
            .def span {
                font-size: xxx-large;
            }
            .defense-title .col {
                background: #c7c7c7;
                color: #ffffff;
                font-size: xxx-large;
                text-align: center;
                padding-top: 2vw;
                width: 6em;
            }
            .defense-arrow {
                border-style: solid;
                border-color: #c7c7c7 transparent transparent transparent;
                border-width: 2em 7em 0 7em;
                line-height: 0;
                height: 0;
                width: 0;
            }
            .skills {
                background: #c1a985;
            }
            .skill-header {
                color: #ffffff;
            }
            .skill-title {
                color: #c1a985;
                font-size: xxx-large;
                margin-bottom: -1.1vw;
            }
            .skill-row {
                background: #ede9de;
            }
            .skill-name {
                font-size: large;
            }
            .skills .value {
                background: #faf9f5;
                margin: 0 1rem;
            }
        </style>
    </x-slot>

    <x-slot name="navbar">
        <li class="nav-item">
            <a class="nav-link" href="/dashboard">Home</a>
        </li>
        <li class="nav-item">
            <span class="nav-link active">{{ $character }}</span>
        </li>
    </x-slot>

    <div class="row mt-4">
        <div class="col">Subversion</div>
        <div class="col name">
            <div>Name</div>
            {{ $character }}
        </div>
        <div class="col"></div>
    </div>

    <div class="row">
        <div class="col meta">
            <div class="row my-2">
                <div class="col">
                    <div>Pronouns</div>
                    He/Him
                </div>
            </div>
            <div class="row my-2">
                <div class="col">
                    <div>Lineage</div>
                    @if (null !== $character->lineage)
                        <span title="{{ $character->lineage->description }}">
                            {{ $character->lineage }}
                        </span>
                    @endif
                </div>
            </div>
            <div class="row my-2">
                <div class="col">
                    <div>Origin</div>
                    @if (null !== $character->origin)
                        <span title="{{ $character->origin->description }}">
                            {{ $character->origin }}
                        </span>
                    @endif
                </div>
            </div>
            <div class="row my-2">
                <div class="col">
                    <div>Background</div>
                    @if (null !== $character->background)
                        <span title="{{ $character->background->description }}">
                            {{ $character->background }}
                        </span>
                    @endif
                </div>
            </div>
            <div class="row my-2">
                <div class="col">
                    <div>Fortune</div>
                </div>
            </div>
            <div class="row my-2">
                <div class="col">
                    <div>Caste</div>
                    @if (null !== $character->caste)
                        <span title="{{ $character->caste->description }}">
                            {{ $character->caste }}
                        </span>
                    @endif
                </div>
            </div>
            <div class="row my-2">
                <div class="col">
                    <div>Debt</div>
                </div>
            </div>
        </div>
        <div class="col attributes">
            <div class="row my-1">
                <div class="col">
                    <div>
                        <div>Agility</div>
                        <span>{{ $character->agility }}</span>
                    </div>
                </div>
                <div class="col">
                    <div>
                        <div>Brawn</div>
                        <span>{{ $character->brawn }}</span>
                    </div>
                </div>
            </div>
            <div class="row my-1">
                <div class="col">
                    <div>
                        <div>Wit</div>
                        <span>{{ $character->wit }}</span>
                    </div>
                </div>
                <div class="col">
                    <div>
                        <div>Charisma</div>
                        <span>{{ $character->charisma }}</span>
                    </div>
                </div>
            </div>
            <div class="row my-1">
                <div class="col">
                    <div>
                        <div>Awareness</div>
                        <span>{{ $character->awareness }}</span>
                    </div>
                </div>
                <div class="col">
                    <div>
                        <div>Will</div>
                        <span>{{ $character->will }}</span>
                    </div>
                </div>
            </div>
        </div>
        <div class="col values">
            <div class="row my-1">
                <div class="col">
                    <div>Ideology</div>
                    @if (null !== $character->ideology)
                        <strong>{{ $character->ideology }}</strong>:
                        <small>{{ $character->ideology->description }}</small>
                    @endif
                </div>
            </div>
            <div class="row my-1">
                <div class="col">
                    <div>Value</div>
                </div>
            </div>
            <div class="row my-1">
                <div class="col">
                    <div>Value</div>
                </div>
            </div>
            <div class="row">
                <div class="col">
                    <div>Value</div>
                </div>
            </div>
            <div class="row my-1">
                <div class="col">
                    <div>Value</div>
                </div>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col def">
            <div class="shape-outer blue">
                <div class="shape-inner">
                    <div>Guard</div>
                    <span>{{ $character->guard_defense }}</span>
                </div>
            </div>
            <div class="shape-outer blue">
                <div class="shape-inner">
                    <div>Vigilance</div>
                    <span>{{ $character->vigilance }}</span>
                </div>
            </div>
            <div class="shape-outer blue">
                <div class="shape-inner">
                    <div>Aegis</div>
                    <span>{{ $character->aegis }}</span>
                </div>
            </div>
            <div class="shape-outer initiative">
                <div class="shape-inner">
                    <div>Initiative</div>
                    <span>{{ $character->initiative }}</span>
                </div>
            </div>
            <div class="shape-outer armor">
                <div class="shape-inner">
                    <div>Armor</div>
                </div>
            </div>
            <div class="shape-outer armor">
                <div class="shape-inner">
                    <div>Adamant</div>
                </div>
            </div>
        </div>
    </div>
    <div class="row defense-title">
        <div class="col-5"></div>
        <div class="col">DEFENSE</div>
        <div class="col-5"></div>
    </div>
    <div class="row" style="margin-bottom:-1vw">
        <div class="col-5"></div>
        <div class="col defense-arrow">&nbsp;</div>
        <div class="col-5"></div>
    </div>

    <div class="row">
        <div class="col-4"></div>
        <div class="col-8 skill-title text-center">SKILLS</div>
    </div>
    <div class="row">
        <div class="col-4">
            <div class="row">
                <div class="col">Condition</div>
            </div>
            <div class="row">
                <div class="col">Consequence</div>
            </div>
            <div class="row">
                <div class="col">Impulses</div>
            </div>
        </div>
        <div class="col-8 skills">
            <div class="d-flex align-items-center row skill-header">
                <div class="col">&nbsp;</div>
                <div class="col-2">&nbsp;</div>
                <div class="col-1">Rank</div>
                <div class="col-1 text-center">Attribute</div>
                <div class="col text-center">Misc<br>modifier</div>
                <div class="col text-center">Roll</div>
            </div>
            @foreach ($character->skills as $skill)
            @php
                $attributes = $skill->attributes;
                array_walk(
                    $attributes,
                    function (string &$item): void {
                        $item = substr($item, 0, 3);
                    },
                );
                $attributes = implode('/', $attributes);
                $attributeValues = $skill->attributes;
                array_walk(
                    $attributeValues,
                    function (string &$item) use ($character): void {
                        $item = (string)$character->$item;
                    },
                );
                $attributeValues = implode('/', $attributeValues);
            @endphp
            <div class="d-flex align-items-center my-3 mx-3 row skill-row">
                <div class="col skill-name">{{ $skill }}</div>
                <div class="col-2 text-end">({{ $attributes }})</div>
                <div class="col-1 text-center value">{!! $skill->rank ?? '&nbsp;' !!}</div>
                <div class="col-1 text-center value">{{ $attributeValues }}</div>
                <div class="col value">&nbsp;</div>
                <div class="col my-1 py-0 text-end value">
                    <small>D<br>R</small>
                </div>
            </div>
            @endforeach
        </div>
    </div>

    <x-slot name="javascript">
        <script>
        </script>
    </x-slot>
</x-app>
