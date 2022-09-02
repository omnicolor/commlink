<x-app>
    <x-slot name="title">Campaign: {{ $campaign }}</x-slot>
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

    <div class="card mt-4" style="width: 18rem;">
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
            <button type="button" class="btn btn-link">
                <i class="bi bi-stop-circle"></i>
            </button>
            <button type="button" class="btn btn-link">
                <i class="bi bi-play-circle"></i>
            </button>
            <button type="button" class="btn btn-link" data-bs-toggle="modal"
                data-bs-target="#add-combatant">
                <i class="bi bi-plus-circle"></i>
            </button>
            <button type="button" class="btn btn-link" id="reload">
                <i class="bi bi-arrow-clockwise"></i>
            </button>
        </div>
    </div>

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

    <x-slot name="javascript">
        <script>
            const campaign = {{ $campaign->id }};
        </script>
        <script src="/js/Shadowrun5e/gm-initiative.js"></script>
    </x-slot>
</x-app>
