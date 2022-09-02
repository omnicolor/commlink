<x-app>
    <x-slot name="title">
        {{ $character->name }}
    </x-slot>
    <x-slot name="head">
        <style>
            .value {
                display: inline-block;
                float: right;
            }
            .value span {
                color: #999;
                display: inline-block;
                text-align: center;
                white-space: nowrap;
                width: 2em;
            }
            .value span:last-child {
                color: #000;
                text-align: right;
            }
            .card {
                margin-top: 1em;
            }
            .tooltip-inner {
                text-align: left;
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

    <div class="row">
        <div class="col">
            <div class="card">
                <ul class="list-group list-group-flush">
                    <li class="list-group-item">
                        Name
                        <div class="value" id="name">
                            {{ $character->name }}
                        </div>
                    </li>
                    <li class="list-group-item">
                        Gender
                    </li>
                    <li class="list-group-item">
                        Age
                        <div class="value">
                            {{ $character->age }}
                        </div>
                    </li>
                    <li class="list-group-item">
                        Origin
                        <div class="value" data-bs-placement="top"
                            data-bs-toggle="tooltip"
                            title="{{ $character->origin->description }}">
                            {{ $character->origin }}
                        </div>
                    </li>
                    <li class="list-group-item">
                        Fortune
                    </li>
                    <li class="list-group-item">
                        Income
                    </li>
                    <li class="list-group-item">
                        Speed
                    </li>
                    <li class="list-group-item">
                        Defence
                    </li>
                    <li class="list-group-item">
                        Toughness
                    </li>
                </ul>
            </div>
        </div>

        <div class="col">
            <div class="card">
                <ul class="list-group list-group-flush">
                    <li class="list-group-item">
                        Social class
                        <div class="value" data-bs-placement="auto"
                            data-bs-toggle="tooltip"
                            title="{{ $character->socialClass->description }}">
                            {{ $character->socialClass }}
                        </div>
                    </li>
                    <li class="list-group-item">
                        Background
                        <div class="value" data-bs-placement="auto"
                            data-bs-toggle="tooltip"
                            title="{{ $character->background->description }}">
                            {{ $character->background }}
                        </div>
                    </li>
                    <li class="list-group-item">
                        Profession
                        <div class="value" id="profession">
                            {{ $character->profession }}
                        </div>
                    </li>
                    <li class="list-group-item">
                        Drive
                        <div class="value" id="drive">
                            {{ $character->drive }}
                        </div>
                    </li>
                    <li class="list-group-item">
                        Quality
                        <div class="value">
                            {{ $character->quality }}
                        </div>
                    </li>
                    <li class="list-group-item">
                        Downfall
                        <div class="value">
                            {{ $character->downfall }}
                        </div>
                    </li>
                    <li class="list-group-item">
                        Level
                        <div class="value">
                            {{ $character->level }}
                        </div>
                    </li>
                    <li class="list-group-item">
                        Description
                    </li>
                </ul>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col">
            <div class="card">
                <div class="card-header" style="border-bottom-width: 0">attributes</div>
                <table class="table table-bordered mb-0">
                    <thead>
                        <tr>
                            <th scope="col" style="border-left: 0">Accuracy</th>
                            <th scope="col">Dexterity</th>
                            <th scope="col">Constitution</th>
                            <th scope="col">Strength</th>
                            <th scope="col">Fighting</th>
                            <th scope="col">Willpower</th>
                            <th scope="col">Intelligence</th>
                            <th scope="col">Communication</th>
                            <th scope="col" style="border-right: 0">Perception</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <td style="border-left: 0">{{ $character->accuracy }}</td>
                            <td>{{ $character->dexterity }}</td>
                            <td>{{ $character->constitution }}</td>
                            <td>{{ $character->strength }}</td>
                            <td>{{ $character->fighting }}</td>
                            <td>{{ $character->willpower }}</td>
                            <td>{{ $character->intelligence }}</td>
                            <td>{{ $character->communication }}</td>
                            <td style="border-right: 0">{{ $character->perception }}</td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col">
            <div class="card">
                <div class="card-header">Foci</div>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col">
            <div class="card">
                <div class="card-header">Talents</div>
                <ul class="list-group list-group-flush">
                    @forelse ($character->getTalents() as $talent)
                        <li class="list-group-item">{{ $talent }}</li>
                    @empty
                        <li class="list-group-item">Character has no talents!</li>
                    @endforelse
                </ul>
            </div>
        </div>

        <div class="col">
            <div class="card">
                <div class="card-header">Specializations</div>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col">
            <div class="card">
                <div class="card-header">Armor &amp; Shields</div>
            </div>

            <div class="card">
                <div class="card-header">Weapons &amp; Notes</div>
            </div>

            <div class="card">
                <div class="card-header">Equipment</div>
            </div>
        </div>

        <div class="col">
            <div class="card">
                <div class="card-header">Conditions</div>
            </div>

            <div class="card">
                <div class="card-header">Notes</div>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col">
            <div class="card">
                <div class="card-header">Advancements</div>
            </div>
        </div>
    </div>
</x-app>
