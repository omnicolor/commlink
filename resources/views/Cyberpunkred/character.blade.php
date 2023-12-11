<x-app>
    <x-slot name="title">{{ $character->handle }}</x-slot>
    <x-slot name="head">
        <style>
            .value {
                display: inline-block;
                float: right;
            }
            .card {
                margin-top: 1em;
            }
            .skill :not(td:first-child, td:last-child) {
                text-align: center;
            }
            .skill td:last-child {
                text-align: right;
            }
        </style>
    </x-slot>

    @includeWhen($creating, 'Cyberpunkred.create-navigation')
    <x-slot name="navbar">
        <li class="nav-item">
            <a class="nav-link" href="/dashboard">Home</a>
        </li>
        <li class="nav-item">
            <span class="nav-link active">{{ $character }}</span>
        </li>
    </x-slot>

    <div class="row">
        <div class="col-2">
            <div class="card">
                <div class="card-header">metadata</div>
                <ul class="list-group list-group-flush">
                    <li class="list-group-item">
                        Handle
                        <div class="value" id="handle">
                            {{ $character->handle }}
                        </div>
                    </li>
                    <li class="list-group-item">
                        INT
                        <div class="value" id="intelligence">
                            {{ $character->intelligence }}
                        </div>
                    </li>
                    <li class="list-group-item">
                        REF
                        <div class="value" id="reflexes">
                            {{ $character->reflexes }}
                        </div>
                    </li>
                    <li class="list-group-item">
                        DEX
                        <div class="value" id="dexterity">
                            {{ $character->dexterity }}
                        </div>
                    </li>
                    <li class="list-group-item">
                        TECH
                        <div class="value" id="technique">
                            {{ $character->technique }}
                        </div>
                    </li>
                    <li class="list-group-item">
                        COOL
                        <div class="value" id="cool">
                            {{ $character->cool }}
                        </div>
                    </li>
                    <li class="list-group-item">
                        WILL
                        <div class="value" id="willpower">
                            {{ $character->willpower }}
                        </div>
                    </li>
                    <li class="list-group-item">
                        LUCK
                        <div class="value">
                            <span id="luck-current">{{ $character->luck }}</span> /
                            <span id="luck-total">{{ $character->luck }}</span>
                        </div>
                    </li>
                    <li class="list-group-item">
                        MOVE
                        <div class="value" id="movement">
                            {{ $character->movement }}
                        </div>
                    </li>
                    <li class="list-group-item">
                        BODY
                        <div class="value" id="body">
                            {{ $character->body }}
                        </div>
                    </li>
                    <li class="list-group-item">
                        EMP
                        <div class="value">
                            <span id="empathy-current">{{ $character->empathy }}</span> /
                            <span id="empathy-total">{{ $character->empathy_original }}</span>
                        </div>
                    </li>
                    <li class="list-group-item">
                        Humanity
                        <div class="value">
                            <span id="humanity-current">{{ $character->humanity }}</span> /
                            <span id="humanity-total">{{ $character->humanity }}</span>
                        </div>
                    </li>
                    <li class="list-group-item">
                        Hit points
                        <div class="value">
                            <span id="hit-points-current">{{ $character->hit_points_max }}</span> /
                            <span id="hit-points-total">{{ $character->hit_points_max }}</span>
                        </div>
                    </li>
                    @php
                        $roles = $character->getRoles();
                    @endphp
                    <li class="list-group-item">
                        @if (count($roles) === 1)
                        Role
                        @else
                        Roles
                        @endif
                        <ul>
                        @foreach ($roles as $role)
                            @can('view data')
                            <li data-bs-toggle="tooltip"
                                title="{{ $role->description }}">
                            @else
                                <li>
                            @endcan
                                {{ $role }}
                            </li>
                        @endforeach
                        </ul>
                    </li>
                </ul>
            </div>
        </div>
        <div class="col">
            <div class="row">
                @php
                    $skills = $character->getSkillsByCategory();
                @endphp
                <div class="col-4 mt-3">
                    @foreach (['Awareness', 'Body', 'Control', 'Fighting', 'Performance'] as $category)
                    <table class="table table-sm">
                        <thead>
                            <tr class="table-dark">
                                <th scope="col">{{ $category }} Skills</th>
                                <th scope="col" style="width: 3em;">Level</th>
                                <th scope="col" style="width: 3em;">Stat</th>
                                <th scope="col" style="width: 3em;">Base</th>
                            </tr>
                        </thead>
                        <tbody>
                        @foreach ($skills[$category] ?? [] as $skill)
                            <tr class="skill">
                                <td>{{ $skill }} ({{ $skill->getShortAttribute() }})</td>
                                <td>{{ $skill->level }}</td>
                                <td>{{ $character->{$skill->attribute} }}</td>
                                <td>{{ $skill->getBase($character) }}</td>
                            </tr>
                        @endforeach
                        </tbody>
                    </table>
                    @endforeach
                </div>
                <div class="col-4 mt-3">
                    @foreach (['Education', 'Ranged Weapon'] as $category)
                    <table class="table table-sm">
                        <thead>
                            <tr class="table-dark">
                                <th scope="col">{{ $category }} Skills</th>
                                <th scope="col" style="width: 3em;">Level</th>
                                <th scope="col" style="width: 3em;">Stat</th>
                                <th scope="col" style="width: 3em;">Base</th>
                            </tr>
                        </thead>
                        <tbody>
                        @foreach ($skills[$category] ?? [] as $skill)
                            <tr class="skill">
                                <td>{{ $skill }} ({{ $skill->getShortAttribute() }})</td>
                                <td>{{ $skill->level }}</td>
                                <td>{{ $character->{$skill->attribute} }}</td>
                                <td>{{ $skill->getBase($character) }}</td>
                            </tr>
                        @endforeach
                        </tbody>
                    </table>
                    @endforeach
                </div>
                <div class="col-4 mt-3">
                    @foreach (['Social', 'Technique'] as $category)
                    <table class="table table-sm">
                        <thead>
                            <tr class="table-dark">
                                <th scope="col">{{ $category }} Skills</th>
                                <th scope="col" style="width: 3em;">Level</th>
                                <th scope="col" style="width: 3em;">Stat</th>
                                <th scope="col" style="width: 3em;">Base</th>
                            </tr>
                        </thead>
                        <tbody>
                        @foreach ($skills[$category] ?? [] as $skill)
                            <tr class="skill">
                                <td>{{ $skill }} ({{ $skill->getShortAttribute() }})</td>
                                <td>{{ $skill->level }}</td>
                                <td>{{ $character->{$skill->attribute} }}</td>
                                <td>{{ $skill->getBase($character) }}</td>
                            </tr>
                        @endforeach
                        </tbody>
                    </table>
                    @endforeach
                </div>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col">
            <table class="table">
                <thead>
                    <tr>
                        <th>&nbsp;</th>
                        <th scope="col">Armor</th>
                        <th scope="col">SP</th>
                        <th scope="col">Penalty</th>
                    </tr>
                </thead>
                <tbody>
                    <tr>
                        <td scope="row">Head</td>
                        <td>{{ $character->armor['head'] ?? '' }}</td>
                        <td>{{ $character->armor['head']?->stopping_power }}</td>
                        <td>{{ $character->armor['head']?->penalty }}</td>
                    </tr>
                    <tr>
                        <td scope="row">Body</td>
                        <td>{{ $character->armor['body'] ?? '' }}</td>
                        <td>{{ $character->armor['body']?->stopping_power }}</td>
                        <td>{{ $character->armor['body']?->penalty }}</td>
                    </tr>
                    <tr>
                        <td scope="row">Shield</td>
                        <td>{{ $character->armor['shield'] ?? '' }}</td>
                        <td>{{ $character->armor['shield']?->stopping_power }}</td>
                        <td>{{ $character->armor['shield']?->penalty }}</td>
                    </tr>
                </tbody>
            </table>
        </div>
        <div class="col">
            <table class="table">
                <thead>
                    <tr>
                        <th scope="col">Weapon</th>
                        <th scope="col">DMG</th>
                        <th scope="col">Ammo</th>
                        <th scope="col">ROF</th>
                        <th scope="col">Conceal</th>
                        <th scope="col">Hands</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($character->getWeapons() as $weapon)
                    <tr>
                        @if ($weapon->name !== $weapon->type)
                            <td>
                                {{ $weapon }}
                                ({{ ucfirst($weapon->quality) }} {{ $weapon->type}})
                            </td>
                        @else
                        <td>{{ ucfirst($weapon->quality) }} {{ $weapon }}</td>
                        @endif
                        <td>{{ $weapon->damage }}</td>
                        <td>
                            @if ($weapon instanceof \App\Models\Cyberpunkred\RangedWeapon)
                            {{ $weapon->ammoRemaining }} / {{ $weapon->magazine }}
                            @endif
                        </td>
                        <td>{{ $weapon->rateOfFire }}</td>
                        <td>{{ $weapon->concealable ? 'Yes' : 'No' }}</td>
                        <td>{{ $weapon->handsRequired }}</td>
                    </tr>
                    @endforeach
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
