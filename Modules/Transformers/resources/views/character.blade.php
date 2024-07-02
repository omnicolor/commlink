<x-app>
    <x-slot name="title">{{ $character }}</x-slot>
    <x-slot name="head">
        <style>
            small {
                font-weight: normal;
            }
        </style>
    </x-slot>

    <x-slot name="navbar">
        <li class="nav-item">
            <a class="nav-link" href="{{ route('dashboard') }}">Home</a>
        </li>
        <li class="nav-item">
            <span class="nav-link active">{{ $character }}</span>
        </li>
    </x-slot>

    <div class="row mt-4">
        <div class="col">
            <strong>Name:</strong> {{ $character }}
        </div>
    </div>

    <div class="row">
        <div class="col-8">
            <table class="mb-0 table table-bordered">
                <tr>
                    <th>Allegiance:</th>
                    <td>{{ $character->allegiance }}</td>
                    <th>Alt.Mode:</th>
                    <td>{{ $character->alt_mode }}</td>
                </tr>
                <tr>
                    <th>Prime.Color:</th>
                    <td>{{ $character->color_primary }}</td>
                    <th rowspan="2">Sub-Groups:</th>
                    <td rowspan="2">
                        @foreach ($character->subgroups as $group)
                            <div data-bs-toggle="tooltip" data-bs-html="true"
                                title="<p>{{ str_replace('||', '</p><p>', $group->description) }}</p>">
                                {{ $group }}
                            </div>
                        @endforeach
                    </td>
                </tr>
                <tr>
                    <th>Secd.Color:</th>
                    <td>{{ $character->color_secondary }}</td>
                </tr>
                <tr>
                    <th>Size:</th>
                    <td>{{ $character->size }}</td>
                    <th>Toy Sales</th>
                    <td>TODO</td>
                </tr>
                <tr>
                    <th>Quote:</th>
                    <td colspan="3">TODO</td>
                </tr>
            </table>
        </div>
        <div class="border col"></div>
    </div>

    <div class="row">
        <div class="col mt-4">
            <table class="table table-bordered">
                <tr>
                    <th scope="row">HP:</th>
                    <td>
                        {{ $character->hp_current }} /
                        {{ $character->hp_base }}
                    </td>
                </tr>
                <tr>
                    <th scope="row">Energon:</th>
                    <td>
                        {{ $character->energon_current }} /
                        {{ $character->energon_base }}
                    </td>
                </tr>
                <tr>
                    <th scope="row">Damage:</th>
                    <td>&nbsp;</td>
                </tr>
            </table>
        </div>
    </div>

    <div class="row">
        <div class="col">
            <table class="table table-bordered">
                <thead>
                    <tr>
                        <th scope="col">Universal Actions</th>
                        <th scope="col">
                            Function: {{ ucfirst($character->programming->value) }}<br>
                            <small>Function Actions</small>
                        </th>
                        <th scope="col">
                            Robot.Mode<br>
                            <small>Statistics</small>
                        </th>
                        <th scope="col">
                            Alt.Mode<br>
                            <small>Statistics</small>
                        </th>
                        <th scope="col">
                            Alt.Functions<br>
                            <small>Gain Bonus</small>
                        </th>
                    </tr>
                </thead>
                <tbody>
                    <tr>
                        <th scope="row">
                            Strength <small>Lift, Grapple, Throw</small>
                        </th>
                        <td>{{ $character->programming->actions()['strength']->name }}</td>
                        <td>{{ $character->strength_robot }}</td>
                        <td>{{ $character->strength_alt }}</td>
                        <td>TODO</td>
                    </tr>
                    <tr>
                        <th scope="row">
                            Intelligence <small>Processor Speed</small>
                        </th>
                        <td>{{ $character->programming->actions()['intelligence']->name }}</td>
                        <td>{{ $character->intelligence_robot }}</td>
                        <td>{{ $character->intelligence_alt }}</td>
                        <td>TODO</td>
                    </tr>
                    <tr>
                        <th scope="row">Speed <small>Move, React</small></th>
                        <td>{{ $character->programming->actions()['speed']->name }}</td>
                        <td>{{ $character->speed_robot }}</td>
                        <td>{{ $character->speed_alt }}</td>
                        <td>TODO</td>
                    </tr>
                    <tr>
                        <th scope="row">Endurance <small>Hardness</small></th>
                        <td>{{ $character->programming->actions()['endurance']->name }}</td>
                        <td>{{ $character->endurance_robot }}</td>
                        <td>{{ $character->endurance_alt }}</td>
                        <td>TODO</td>
                    </tr>
                    <tr>
                        <th scope="row">
                            Rank <small>Transformation Options</small>
                        </th>
                        <td>{{ $character->programming->actions()['rank']->name }}</td>
                        <td>{{ $character->rank }}</td>
                        <td>{{ $character->rank }}</td>
                        <td>TODO</td>
                    </tr>
                    <tr>
                        <th scope="row">
                            Courage <small>Internal Functions</small>
                        </th>
                        <td>{{ $character->programming->actions()['courage']->name }}</td>
                        <td>{{ $character->courage_robot }}</td>
                        <td>{{ $character->courage_alt }}</td>
                        <td>TODO</td>
                    </tr>
                    <tr>
                        <th scope="row">
                            Firepower <small>Attach Ranged</small>
                        </th>
                        <td>{{ $character->programming->actions()['firepower']->name }}</td>
                        <td>{{ $character->firepower_robot }}</td>
                        <td>{{ $character->firepower_alt }}</td>
                        <td>TODO</td>
                    </tr>
                    <tr>
                        <th scope="row">
                            Skill <small>External Functions</small>
                        </th>
                        <td>{{ $character->programming->actions()['skill']->name }}</td>
                        <td>{{ $character->skill_robot }}</td>
                        <td>{{ $character->skill_alt }}</td>
                        <td>TODO</td>
                    </tr>
                </tbody>
            </table>
        </div>
    </div>

    <div class="row">
        <div class="col">
            <table class="table table-bordered">
                <thead>
                    <tr>
                        <th scope="col">Weapon</th>
                        <th scope="col">DMG</th>
                        <th scope="col">Structure</th>
                        <th scope="col">Range</th>
                        <th scope="col">Charges</th>
                        <th scope="col">Notes</th>
                    </tr>
                </thead>
                <tbody>
                </tbody>
            </table>
        </div>
    </div>
    <x-slot name="javascript">
        <script>
            $(function () {
                $('[data-bs-toggle="tooltip"]').tooltip();
            });
        </script>
    </x-slot>
</x-app>
