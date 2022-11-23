<x-app>
    <x-slot name="title">Campaign: {{ $campaign }}</x-slot>
    <x-slot name="head">
        <style>
            .box {
                height: 1em;
                padding: 0 !important;
                width: 1em;
                border: 1px solid #dee2e6;
            }
            .box.used {
                background: #e66465;
            }
        </style>
    </x-slot>

    <x-slot name="navbar">
        <li class="nav-item">
            <a class="nav-link" href="/dashboard">Home</a>
        </li>
        <li class="nav-item">
            <a class="nav-link" href="/campaigns/{{ $campaign->id }}">{{ $campaign }}</a>
        </li>
        <li class="nav-item">
            <span class="nav-link active">GM Screen</span>
        </li>
    </x-slot>

    <div class="card float-start m-2">
        <div class="card-header">
            <h5 class="card-title">Initiative</h5>
        </div>
        <ul class="list-group list-group-flush" id="combatants">
            @forelse ($initiative as $combatant)
            <li class="list-group-item" data-id="{{ $combatant->id }}">
                {{ $combatant }}
                <span class="float-end">
                    <span class="score">{{ $combatant->initiative }}</span>
                    <span class="dropdown">
                        <button aria-expanded="false"
                            class="btn btn-link btn-sm"
                            data-bs-toggle="dropdown" type="button">
                            <i class="bi bi-three-dots-vertical"></i>
                        </button>
                        <ul class="dropdown-menu">
                            <li>
                                <button class="dropdown-item"
                                    data-bs-target="#rename-combatant"
                                    data-bs-toggle="modal"
                                    type="button">
                                    Change name
                                </button>
                            </li>
                            <li>
                                <button class="dropdown-item"
                                    data-bs-target="#change-initiative"
                                    data-bs-toggle="modal"
                                    type="button">
                                    Change initiative
                                </button>
                            </li>
                            <li>
                                <a class="dropdown-item remove" href="#">
                                    Remove from combat
                                </a>
                            </li>
                        </ul>
                    </span>
                </span>
            </li>
            @empty
            <li class="list-group-item" id="no-combatants">
                No combatants.
            </li>
            @endforelse
        </ul>
        <div class="card-footer">
            <button type="button" class="btn btn-link" title="Clear initiatives">
                <i class="bi bi-stop-circle"></i>
            </button>
            <button type="button" class="btn btn-link" title="Start/advance initiative">
                <i class="bi bi-play-circle"></i>
            </button>
            <button type="button" class="btn btn-link" data-bs-toggle="modal"
                data-bs-target="#add-combatant" title="Add combatant">
                <i class="bi bi-plus-circle"></i>
            </button>
            <button type="button" class="btn btn-link" id="reload" title="Next round">
                <i class="bi bi-arrow-clockwise"></i>
            </button>
        </div>
    </div>

    <div class="card float-start m-2">
        <div class="card-header">
            <h5 class="card-title">Monitors</h5>
        </div>
        <table class="card-body m-1">
            <tbody>
            @foreach ($characters as $character)
                <tr id="physical-{{ $character->id }}">
                    <td>
                        <a class="character-row" data-bs-target="#damage-modal"
                            data-bs-toggle="modal"
                            data-id="{{ $character->id }}" href="#">
                            {{ $character }}
                        </a>
                    </td>
                    <td>Physical: {{ $character->physical_monitor }}</td>
                @for ($i = 1; $i <= $max_monitor; $i++)
                    @if ($i > $character->physical_monitor)
                        <td>&nbsp;</td>
                        @php continue @endphp
                    @endif
                    <td class="box text-muted
                    @if ($i <= $character->damagePhysical ?? 0)
                        used
                    @endif
                        ">
                    @if ($i > 0 && $i % 3 === 0)
                        <small>-{{ $i / 3 }}</small>
                    @else
                        &nbsp;
                    @endif
                    </td>
                @endfor
                </tr>
                <tr id="stun-{{ $character->id }}">
                    <td class="ps-4">
                        <small>Melee Dodge: {{ $character->melee_defense }}</small>
                    </td>
                    <td>Stun: {{ $character->stun_monitor }}</td>
                @for ($i = 1; $i <= $max_monitor; $i++)
                    @if ($i > $character->stun_monitor)
                        <td>&nbsp;</td>
                        @php continue @endphp
                    @endif
                    <td class="box text-muted
                    @if ($i <= $character->damageStun ?? 0)
                        used
                    @endif
                        ">
                    @if ($i > 0 && $i % 3 === 0)
                        <small>-{{ $i / 3 }}</small>
                    @else
                        &nbsp;
                    @endif
                    </td>
                @endfor
                </tr>
                <tr id="overflow-{{ $character->id }}">
                    <td class="ps-4">
                        <small>Ranged Dodge: {{ $character->ranged_defense }}</small>
                    </td>
                    <td>Overflow</td>
                    @for ($i = 1; $i <= $max_monitor; $i++)
                        @if ($i > $character->overflow_monitor)
                            <td>&nbsp;</td>
                            @php continue @endphp
                        @endif
                        <td class="box
                        @if ($i < $character->damageOverflow ?? 0)
                            used
                        @endif
                            ">&nbsp;</td>
                    @endfor
                </tr>
                <tr id="edge-{{ $character->id }}">
                    <td class="ps-4">
                        <small>Soak: {{ $character->soak }}</small>
                    </td>
                    <td>Edge: {{ $character->edge }}</td>
                    @for ($i = 1; $i <= $max_monitor; $i++)
                        @if ($i > $character->edge)
                            <td>&nbsp;</td>
                            @php continue @endphp
                        @endif
                        <td class="box
                        @if ($i <= $character->edge - $character->edgeCurrent ?? 0)
                            used
                        @endif
                            ">&nbsp;</td>
                    @endfor
                </tr>
            @endforeach
            </tbody>
        </table>
        <div class="card-footer">
            <button type="button" class="btn btn-link" id="heal-all" title="Heal all">
                <i class="bi bi-bandaid"></i>
            </button>
        </div>
    </div>

    <div class="clearfix"></div>

    <div aria-hidden="true" aria-labelledby="add-combatant-label"
        class="modal fade" id="add-combatant" tabindex="-1">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="add-combatant-label">
                        Add a combatant
                    </h5>
                    <button aria-label="Close" class="btn-close"
                        data-bs-dismiss="modal" type="button"></button>
                </div>
                <div class="modal-body">
                    <div class="mb-3">
                        <label for="name" class="form-label">Name</label>
                        <input type="text" class="form-control" id="name">
                    </div>
                    <div class="mb-3">
                        <label for="grunt-id" class="form-label">
                            Grunt ID (optional)
                        </label>
                        <select class="form-control" id="grunt-id">
                            <option id="grunt-id-null" value="">&hellip;</option>
                        @foreach ($grunts as $grunt)
                            <option data-base="{{ $grunt->initiative_base ?? 1 }}"
                                data-dice="{{ $grunt->initiative_dice ?? 1 }}"
                                value="{{ $grunt->id }}">
                                {{ $grunt }} (PR-{{ $grunt->professional_rating }})
                            </option>
                        @endforeach
                        </select>
                    </div>
                    <div class="mb-3">
                        <div class="form-label">
                            Initiative type
                        </div>
                        <div class="form-check form-check-inline">
                            <input checked class="form-check-input" id="roll"
                                name="initiative-type" type="radio"
                                value="roll">
                            <label class="form-check-label" for="roll">Roll</label>
                        </div>
                        <div class="form-check form-check-inline">
                            <input class="form-check-input" id="assign"
                                name="initiative-type" type="radio"
                                value="assign">
                            <label class="form-check-label" for="assign">Assign</label>
                        </div>
                    </div>
                    <div class="mb-3" id="roll-form">
                        <div>
                            <label for="base" class="form-label">Base initiative</label>
                            <input class="form-control" id="base" min="1"
                                step="1" type="number">
                        </div>
                        <div>
                            <label for="dice" class="form-label">Initiative dice</label>
                            <input class="form-control" id="dice" max="5"
                                min="1" step="1" type="number" value="2">
                        </div>
                    </div>
                    <div class="mb-3" id="assign-form" style="display:none">
                        <div>
                            <label for="init" class="form-label">Initiative</label>
                            <input class="form-control" id="init" min="1"
                                step="1" type="number">
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                    <button type="button" class="btn btn-primary">Save changes</button>
                </div>
            </div>
        </div>
    </div>

    <div aria-hidden="true" aria-labelledby="rename-combatant-label"
        class="modal fade" id="rename-combatant" tabindex="-1">
        <div class="modal-dialog">
            <div class="modal-content">
                <form>
                <div class="modal-header">
                    <h5 class="modal-title" id="rename-combatant-label">
                        Rename combatant
                    </h5>
                    <button aria-label="Close" class="btn-close"
                        data-bs-dismiss="modal" type="button"></button>
                </div>
                <div class="modal-body">
                    <div class="mb-3">
                        <label for="name" class="form-label">Name</label>
                        <input type="text" class="form-control" id="rename-name">
                        <input type="hidden" id="rename-id">
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                    <button type="submit" class="btn btn-primary">Save changes</button>
                </div>
                </form>
            </div>
        </div>
    </div>

    <div aria-hidden="true" aria-labelledby="change-initiative-label"
        class="modal fade" id="change-initiative" tabindex="-1">
        <div class="modal-dialog">
            <div class="modal-content">
                <form>
                <div class="modal-header">
                    <h5 class="modal-title" id="change-initiative-label">
                        Change initiative
                    </h5>
                    <button aria-label="Close" class="btn-close"
                        data-bs-dismiss="modal" type="button"></button>
                </div>
                <div class="modal-body">
                    <div class="mb-3">
                        <label for="name" class="form-label">Initiative</label>
                        <input class="form-control" id="new-initiative" step="1"
                            type="number">
                        <input type="hidden" id="change-id">
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                    <button type="submit" class="btn btn-primary">Save changes</button>
                </div>
                </form>
            </div>
        </div>
    </div>

    <form class="needs-validation" id="damage-form" novalidate>
    <div aria-hidden="true" aria-labelledby="damage-header"
        class="modal" id="damage-modal" tabindex="-1" role="dialog">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="damage-header">
                        Apply damage/healing or use edge
                    </h5>
                    <button type="button" class="btn-close"
                        data-bs-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <div class="row">
                        <div class="col-form-label col-4">Character</div>
                        <div class="col" id="damage-handle"></div>
                    </div>
                    <div class="row">
                        <label class="col-form-label col-4" for="damage-type">
                            Damage type
                        </label>
                        <div class="col">
                            <select class="form-control" id="damage-type" required>
                                <option value="">&nbsp;
                                <option>Physical
                                <option>Stun
                                <option>Edge
                            </select>
                            <div class="invalid-feedback">
                                Please choose what kind of damage (or edge) to take.
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <label class="col-form-label col-4" for="damage-amount">
                            Amount
                        </label>
                        <div class="col">
                            <input class="form-control" id="damage-amount" required
                                step="1" type="number">
                            <div class="invalid-feedback">
                                Please enter a non-zero number.
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="submit" id="damage-save" class="btn btn-success">
                        Save
                    </button>
                    <button type="button" class="btn btn-secondary"
                        data-dismiss="modal">Cancel</button>
                </div>
            </div>
        </div>
    </div>
    </form>

    <x-slot name="javascript">
        <script>
            const campaign = {{ $campaign->id }};
            const csrfToken = '{{ csrf_token() }}';
        </script>
        <script src="/js/Shadowrun5e/gm-damage.js"></script>
        <script src="/js/Shadowrun5e/gm-initiative.js"></script>
    </x-slot>
</x-app>
