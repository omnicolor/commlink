@php
use App\Models\Transformers\AltMode;
$orderedAttributes = [
    'strength',
    'intelligence',
    'speed',
    'endurance',
    'rank',
    'courage',
    'firepower',
    'skill',
];
$orderedModes = [
    AltMode::Vehicle,
    AltMode::Machine,
    AltMode::Weapon,
    AltMode::Primitive,
];
$altMode = $character->alt_mode ?? null;
@endphp
<x-app>
    <x-slot name="title">Create character</x-slot>
    @include('Transformers.create-navigation')

    <div class="row">
        <div class="col">
            <h1>Create a new transformer</h1>
        </div>
    </div>

    <form action="{{ route('transformers.create-statistics') }}" method="post">
    @csrf

    @if ($errors->any())
        <div class="alert alert-danger">
            <ul>
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <div class="row">
        <div class="col">
            <h2>04. Alt.Mode</h2>

            <p>
                All Robots choose 1 Alternate Mode (Alt.Mode). This Alt.Mode has
                new Statistics (Stats), derived from the Robot.Mode Statistics.
                “Alt.m” is the number of rolls, greater or lesser, the player
                makes to generate the new Alt.Mode Statistic, then compares them
                to their Robot.Mode Stats and takes the higher + or lower -
                result. (+) means to roll that many more dice and take the
                highest Stat number for the Alt.Mode. When represented by a
                negative value (-), it means roll that many dice, taking the
                lowest number for the Alt.Mode Statistic. Where None is
                declared, this is simply not possible in that Alt.Mode. The dash
                alone (-) represents no change from Robot.Mode. Rolling for a
                positive value cannot force a negative value and vica versa.
            </p>
        </div>
    </div>

    <div class="row">
        <div class="col-2"></div>
        <div class="col">
            <table class="table">
                <thead>
                    <tr>
                        <th scope="col">Alt.Mode</th>
                        @foreach ($orderedModes as $mode)
                            <th class="mode-{{ $mode->value }}" scope="col">
                                {{ $mode->name }}
                            </th>
                        @endforeach
                    </tr>
                </thead>
                <tbody>
                    @foreach ($orderedAttributes as $attribute)
                    <tr>
                        <th scope="row">{{ ucfirst($attribute) }}</th>
                        @foreach ($orderedModes as $mode)
                        <td class="mode-{{ $mode->value }}">
                            @if (null === $mode->statisticModifier($attribute))
                                None
                            @elseif (0 === $mode->statisticModifier($attribute))
                                &mdash;
                            @elseif (0 < $mode->statisticModifier($attribute))
                                +{{ $mode->statisticModifier($attribute) }}
                            @else
                                {{ $mode->statisticModifier($attribute) }}
                            @endif
                        </td>
                        @endforeach
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
        <div class="col-2"></div>
    </div>


    @if ('Autobots' === $character->allegiance)
    <div class="row">
        <div class="col">
            <table class="table table-striped" id="alt-mode-table">
                <thead>
                    <tr>
                        <th scope="col">Rank</th>
                        <th scope="col">Vehicle</th>
                        <th scope="col">Machine</th>
                        <th scope="col">Weapon</th>
                        <th scope="col">Primitive</th>
                    </tr>
                </thead>
                <tbody>
                    <tr>
                        <td scope="row">1</td>
                        <td>
                            <div class="form-check">
                                <label class="form-check-label">
                                    <input class="form-input-check" name="mode"
                                        type="radio" value="Car">
                                    Car
                                </label>
                            </div>
                        </td>
                        <td>
                            <div class="form-check">
                                <label class="form-check-label">
                                    <input class="form-input-check" name="mode"
                                        type="radio" value="Deployer">
                                    Deployer
                                </label>
                            </div>
                        </td>
                        <td>&ndash;</td>
                        <td>
                            <div class="form-check">
                                <label class="form-check-label">
                                    <input class="form-input-check" name="mode"
                                        type="radio" value="Herbivore">
                                    Herbivore
                                </label>
                            </div>
                        </td>
                    </tr>
                    <tr>
                        <td scope="row">2</td>
                        <td>
                            <div class="form-check">
                                <label class="form-check-label">
                                    <input class="form-input-check" name="mode"
                                        type="radio" value="Small vehicle">
                                    Small vehicle
                                </label>
                            </div>
                            <div class="form-check">
                                <label class="form-check-label">
                                    <input class="form-input-check" name="mode"
                                        type="radio" value="Snowmobile">
                                    Snowmobile
                                </label>
                            </div>
                            <div class="form-check">
                                <label class="form-check-label">
                                    <input class="form-input-check" name="mode"
                                        type="radio" value="Sports car">
                                    Sports car
                                </label>
                            </div>
                            <div class="form-check">
                                <label class="form-check-label">
                                    <input class="form-input-check" name="mode"
                                        @if ('Truck' === $altMode) checked @endif
                                        type="radio" value="Truck">
                                    Truck
                                </label>
                            </div>
                            <div class="form-check">
                                <label class="form-check-label">
                                    <input class="form-input-check" name="mode"
                                        @if ('Van' === $altMode) checked @endif
                                        type="radio" value="Van">
                                    Van
                                </label>
                            </div>
                        </td>
                        <td>
                            <div class="form-check">
                                <label class="form-check-label">
                                    <input class="form-input-check" name="mode"
                                        @if ('Moon buggy' === $altMode) checked @endif
                                        type="radio" value="Moon buggy">
                                    Moon buggy
                                </label>
                            </div>
                        </td>
                        <td><div class="form-check">&ndash;</div></td>
                        <td><div class="form-check">&ndash;</div></td>
                    </tr>
                    <tr>
                        <td scope="row">3</td>
                        <td>
                            <div class="form-check">
                                <label class="form-check-label">
                                    <input class="form-input-check" name="mode"
                                        @if ('Dune buggy' === $altMode) checked @endif
                                        type="radio" value="Dune buggy">
                                    Dune buggy
                                </label>
                            </div>
                            <div class="form-check">
                                <label class="form-check-label">
                                    <input class="form-input-check" name="mode"
                                        @if ('Jeep' === $altMode) checked @endif
                                        type="radio" value="Jeep">
                                    Jeep
                                </label>
                            </div>
                            <div class="form-check">
                                <label class="form-check-label">
                                    <input class="form-input-check" name="mode"
                                        @if ('Race car' === $altMode) checked @endif
                                        type="radio" value="Race car">
                                    Race car
                                </label>
                            </select>
                        </td>
                        <td>
                            <div class="form-check">
                                <label class="form-check-label">
                                    <input class="form-input-check" name="mode"
                                        @if ('Farming' === $altMode) checked @endif
                                        type="radio" value="Farming">
                                    Farming
                                </label>
                            </div>
                        </td>
                        <td>&ndash;</td>
                        <td>
                            <div class="form-check">
                                <label class="form-check-label">
                                    <input class="form-input-check" name="mode"
                                        @if ('Carnivore' === $altMode) checked @endif
                                        type="radio" value="Carnivore">
                                    Carnivore
                                </label>
                            </div>
                        </td>
                    </tr>
                    <tr>
                        <td scope="row">4</td>
                        <td>
                            <div class="form-check">
                                <label class="form-check-label">
                                    <input class="form-input-check" name="mode"
                                        @if ('Ambulance' === $altMode) checked @endif
                                        type="radio" value="Ambulance">
                                    Ambulance
                                </label>
                            </div>
                            <div class="form-check">
                                <label class="form-check-label">
                                    <input class="form-input-check" name="mode"
                                        @if ('Motorcycle' === $altMode) checked @endif
                                        type="radio" value="Motorcycle">
                                    Motorcycle
                                </label>
                            </div>
                            <div class="form-check">
                                <label class="form-check-label">
                                    <input class="form-input-check" name="mode"
                                        @if ('Tow truck' === $altMode) checked @endif
                                        type="radio" value="Tow truck">
                                    Tow truck
                                </label>
                            </div>
                        </td>
                        <td>
                            <div class="form-check">
                                <label class="form-check-label">
                                    <input class="form-input-check" name="mode"
                                        @if ('Audio-visual' === $altMode) checked @endif
                                        type="radio" value="Audio-visual">
                                    Audio-visual
                                </label>
                            </div>
                        </td>
                        <td>
                            <div class="form-check">
                                <label class="form-check-label">
                                    <input class="form-input-check" name="mode"
                                        @if ('Platform' === $altMode) checked @endif
                                        type="radio" value="Platform">
                                    Platform
                                </label>
                            </div>
                        </td>
                        <td>&ndash;</td>
                    </tr>
                    <tr>
                        <td scope="row">5</td>
                        <td>
                            <div class="form-check">
                                <label class="form-check-label">
                                    <input class="form-input-check" name="mode"
                                        @if ('Boat' === $altMode) checked @endif
                                        type="radio" value="Boat">
                                    Boat
                                </label>
                            </div>
                            <div class="form-check">
                                <label class="form-check-label">
                                    <input class="form-input-check" name="mode"
                                        @if ('Camper van' === $altMode) checked @endif
                                        type="radio" value="Camper van">
                                    Camper van
                                </label>
                            </div>
                            <div class="form-check">
                                <label class="form-check-label">
                                    <input class="form-input-check" name="mode"
                                        @if ('Helicopter' === $altMode) checked @endif
                                        type="radio" value="Helicopter">
                                    Helicopter
                                </label>
                            </div>
                            <div class="form-check">
                                <label class="form-check-label">
                                    <input class="form-input-check" name="mode"
                                        @if ('Hovercraft' === $altMode) checked @endif
                                        type="radio" value="Hovercraft">
                                    Hovercraft
                                </label>
                            </div>
                            <div class="form-check">
                                <label class="form-check-label">
                                    <input class="form-input-check" name="mode"
                                        @if ('Plane' === $altMode) checked @endif
                                        type="radio" value="Plane">
                                    Plane
                                </label>
                            </div>
                        </td>
                        <td>
                            <div class="form-check">
                                <label class="form-check-label">
                                    <input class="form-input-check" name="mode"
                                        @if ('Satellite' === $altMode) checked @endif
                                        type="radio" value="Satellite">
                                    Satellite
                                </label>
                            </div>
                        </td>
                        <td>
                            <div class="form-check">
                                <label class="form-check-label">
                                    <input class="form-input-check" name="mode"
                                        @if ('Musical instrument' === $altMode) checked @endif
                                        type="radio" value="Musical instrument">
                                    Musical instrument
                                </label>
                            </div>
                        </td>
                        <td>
                            <div class="form-check">
                                <label class="form-check-label">
                                    <input class="form-input-check" name="mode"
                                        @if ('Bird' === $altMode) checked @endif
                                        type="radio" value="Bird">
                                    Bird
                                </label>
                            </div>
                            <div class="form-check">
                                <label class="form-check-label">
                                    <input class="form-input-check" name="mode"
                                        @if ('Dinosaur' === $altMode) checked @endif
                                        type="radio" value="Dinosaur">
                                    Dinosaur
                                </label>
                            </div>
                            <div class="form-check">
                                <label class="form-check-label">
                                    <input class="form-input-check" name="mode"
                                        @if ('Monster' === $altMode) checked @endif
                                        type="radio" value="Monster">
                                    Monster
                                </label>
                            </div>
                        </td>
                    </tr>
                    <tr>
                        <td scope="row">6</td>
                        <td>
                            <div class="form-check">
                                <label class="form-check-label">
                                    <input class="form-input-check" name="mode"
                                        @if ('Fire truck' === $altMode) checked @endif
                                        type="radio" value="Fire truck">
                                    Fire truck
                                </label>
                            </div>
                            <div class="form-check">
                                <label class="form-check-label">
                                    <input class="form-input-check" name="mode"
                                        @if ('Futuristic car' === $altMode) checked @endif
                                        type="radio" value="Futuristic car">
                                    Futuristic car
                                </label>
                            </div>
                            <div class="form-check">
                                <label class="form-check-label">
                                    <input class="form-input-check" name="mode"
                                        @if ('Monster truck' === $altMode) checked @endif
                                        type="radio" value="Monster truck">
                                    Monster truck
                                </label>
                            </div>
                            <div class="form-check">
                                <label class="form-check-label">
                                    <input class="form-input-check" name="mode"
                                        @if ('Submarine' === $altMode) checked @endif
                                        type="radio" value="Submarine">
                                    Submarine
                                </label>
                            </div>
                            <div class="form-check">
                                <label class="form-check-label">
                                    <input class="form-input-check" name="mode"
                                        @if ('Tank' === $altMode) checked @endif
                                        type="radio" value="Tank">
                                    Tank
                                </label>
                            </div>
                            <div class="form-check">
                                <label class="form-check-label">
                                    <input class="form-input-check" name="mode"
                                        @if ('Transport truck' === $altMode) checked @endif
                                        type="radio" value="Transport truck">
                                    Transport truck
                                </label>
                            </div>
                        </td>
                        <td></td>
                        <td></td>
                        <td></td>
                </tbody>
            </table>
        </div>
    </div>
    @else
    <div class="row">
        <div class="col">
            <table class="table table-striped" id="alt-mode-table">
                <thead>
                    <tr>
                        <th scope="col">Rank</th>
                        <th scope="col">Vehicle</th>
                        <th scope="col">Machine</th>
                        <th scope="col">Weapon</th>
                        <th scope="col">Primitive</th>
                    </tr>
                </thead>
                <tbody>
                    <tr>
                        <td scope="row">1</td>
                        <td>Jet</td>
                        <td>Deployer</td>
                        <td>&ndash;</td>
                        <td>Insect</td>
                    </tr>
                </tbody>
            </table>
        </div>
    </div>
    @endif

    <div class="row">
        <div class="col">
            <table class="table table-striped" id="mode-info">
                <thead>
                    <tr>
                        <th scope="col">Alt.Mode</th>
                        <th scope="col">Size</th>
                        <th scope="col">Restricted</th>
                        <th scope="col">Gain</th>
                        <th scope="col">Bot</th>
                        <th scope="col">Con</th>
                        <th scope="col">Notes</th>
                    </tr>
                </thead>
                <tbody>
                    <tr @if ('Transport Truck' === $altMode) class="table-primary" @endif id="transport-truck">
                        <td>Truck, Transport</td>
                        <td>4-5</td>
                        <td>&ndash;</td>
                        <td>Defence, Demolition</td>
                        <td>6</td>
                        <td>7</td>
                        <td>Granted Trailer (See Technology). The Trailer magically appears and disappears when Transforming. Does not alter Size.</td>
                    </tr>
                    <tr @if ('Van' === $altMode) class="table-primary" @endif id="mode-info-Van">
                        <td>Van</td>
                        <td>3-4</td>
                        <td>&ndash;</td>
                        <td>Defence</td>
                        <td>2</td>
                        <td>6</td>
                        <td>Can store 1 Robot of Size lower or equivalent</td>
                    </tr>
                </tbody>
            </table>
        </div>
    </div>

    <x-slot name="javascript">
        <script>
            function getRandomInt(max) {
                return Math.floor(Math.random() * 10) + 1;
            }

            function handleAltModeChange(e) {
            }

            $(function () {
                $('#alt-mode-table select').on('change', handleAltModeChange);
            });
        </script>
    </x-slot>
</x-app>
