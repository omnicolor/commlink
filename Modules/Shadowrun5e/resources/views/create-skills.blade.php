@php
use Modules\Shadowrun5e\Models\SkillGroup;
@endphp
<x-app>
    <x-slot name="title">Create character</x-slot>
    <x-slot name="head">
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

    <form action="{{ route('shadowrun5e.create-skills') }}" method="POST">
    @csrf

    <div class="row">
        <div class="col-1"></div>
        <div class="col">
            <h1>Skills</h1>
            <p>
                You now have the basic attributes of your character; the next
                step is to figure out your skills, the areas where you have
                particular abilities and gifts. Active skills are what the
                character can do physically: use firearms, drive a car, tell
                convincing lies, cast spells, register sprites, etc. Skill
                groups contain similar or complimentary skills that a player
                purchases as a bundle. When a skill group is purchased, the
                character is considered to have all the individual skills of the
                skill group at the rating of the group.
            </p>
        </div>
        <div class="col-3"></div>
    </div>

    <div class="row my-4">
        <div class="col-1"></div>
        <div class="col">
            <h3>Groups</h3>
            <ul class="list-group mb-3" id="skill-groups-list">
                @foreach ($character->skillGroups ?? [] as $id => $level)
                @php
                    $group = new SkillGroup($id, $level);
                @endphp
                <li class="list-group-item" data-id="{{ $id }}">
                    <div class="row">
                        <label class="col col-form-label text-nowrap name">{{ $group }}</label>
                        <div class="col">
                            <input name="group-names[]" type="hidden" value="{{ $id }}">
                            <input class="form-control text-center" min="0"
                                max="6" name="group-levels[]" step="1"
                                type="number" value="{{ $level }}">
                        </div>
                        <div class="col text-right">
                            <button class="btn btn-danger btn-sm">
                                <span aria-hidden="true" class="bi bi-dash"></span>
                                Group
                            </button>
                        </div>
                    </div>
                </li>
                @endforeach
                <li class="list-group-item" id="no-skill-groups"
                    @if (!empty($character->skillGroups))
                        style="display:none;"
                    @endif
                    >No skill groups</li>
                <li class="list-group-item" id="add-group-row">
                    <button class="btn btn-primary"
                        data-bs-target="#group-modal" data-bs-toggle="modal"
                        type="button">
                        <span aria-hidden="true" class="bi bi-plus"></span>
                        Add skill group
                    </button>
                </li>
            </ul>
        </div>
        <div class="col-3"></div>
    </div>

    <div class="row">
        <div class="col-1"></div>
        <div class="col">
            <h3>Active skills</h3>
            <ul class="list-group mb-3" id="active-skills-list">
                @foreach ($character->getSkills() as $skill)
                <li class="list-group-item" data-id="{{ $skill->id }}">
                    <div class="row">
                        <label class="col col-form-label text-nowrap name">
                            {{ $skill }}
                            @if (null !== $skill->specialization)
                            (+2 {{ $skill->specialization }})
                            @endif
                        </label>
                        <div class="col">
                            <input name="skill-names[]" type="hidden" value="{{ $skill->id }}">
                            <input class="form-control text-center" min="0"
                                max="6" name="skill-levels[]" step="1"
                                type="number" value="{{ $skill->level }}">
                            <input name="skill-specializations[]" type="hidden"
                                value="{{ $skill->specialization }}">
                        </div>
                        <div class="col text-right text-nowrap">
                            @if (null === $skill->specialization)
                            <button class="btn btn-success btn-sm specialize"
                                    data-bs-target="#specialize-modal"
                                    data-bs-toggle="modal" type="button">
                                <span aria-hidden="true" class="bi bi-plus"></span>
                                Specialization
                            </button>
                            @else
                                <button class="btn btn-danger btn-sm specialize"
                                    type="button">
                                    <span aria-hidden="true" class="bi bi-dash"></span>
                                    Specialization
                                </button>
                            @endif
                            <button class="btn btn-danger btn-sm skill" type="button">
                                <span aria-hidden="true" class="bi bi-dash"></span>
                                Skill
                            </button>
                        </div>
                    </div>
                </li>
                @endforeach
                <li class="list-group-item" id="no-active-skills"
                    @if (!empty($character->skills))
                        style="display:none;"
                    @endif
                >No active skills</li>
                <li class="list-group-item" id="add-skill-row">
                    <button class="btn btn-primary"
                        data-bs-target="#skill-modal" data-bs-toggle="modal"
                        type="button">
                        <span aria-hidden="true" class="bi bi-plus"></span>
                        Add active skill
                    </button>
                </li>
            </ul>
        </div>
        <div class="col-3"></div>
    </div>

    @include('shadowrun5e::create-next')
    </form>

    <div class="modal" id="group-modal" tabindex="-1" role="dialog">
        <div class="modal-dialog modal-lg" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Choose skill group</h5>
                    <button type="button" class="close" data-bs-dismiss="modal"
                        aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body row">
                    <div class="col">
                        <div class="row">
                            <table class="table table-sm" id="group-list" style="width:100%">
                                <thead>
                                    <tr>
                                        <th scope="col">Name</th>
                                        <th scope="col">Skills</th>
                                        <th></th>
                                    </tr>
                                </thead>
                                <tbody>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="modal" id="skill-modal" tabindex="-1" role="dialog">
        <div class="modal-dialog modal-xl" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Choose active skills</h5>
                    <button type="button" class="close" data-bs-dismiss="modal"
                        aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body row">
                    <div class="col">
                        <div class="row mx-1">
                            <input class="col form-control form-control-sm"
                                id="search-skills" placeholder="Search skills"
                                type="search">
                        </div>
                        <div class="row">
                            <table class="table" id="skill-list" style="width:95%;">
                                <thead>
                                    <tr>
                                        <th scope="col">Name</th>
                                        <th scope="col">Attribute</th>
                                        <th scope="col">Group</th>
                                    </tr>
                                </thead>
                                <tbody>
                                </tbody>
                            </table>
                        </div>
                    </div>
                    <div class="col">
                        <div id="skill-click-panel">
                            Click a skill for more information about it.
                        </div>
                        <div id="skill-info-panel" style="display: none;">
                            <h3 id="skill-name">.</h3>
                            <div class="row mt-2">
                                <p class="col" id="skill-description"></p>
                            </div>
                            <div class="row mt-2">
                                <div class="col-3">Attribute</div>
                                <div class="col" id="skill-attribute"></div>
                            </div>
                            <div class="row">
                                <div class="col-3">Group</div>
                                <div class="col" id="skill-group"></div>
                            </div>
                            <div class="row mb-4">
                                <div class="col-3">Default</div>
                                <div class="col" id="skill-default"></div>
                            </div>
                            <div class="row mt-4">
                                <label class="col-1 col-form-label" for="skill-level">
                                    Level
                                </label>
                                <div class="col">
                                    <input class="form-control" id="skill-level"
                                        min="1" max="6" type="number" value="1">
                                </div>
                                <div class="col">
                                    <button class="btn btn-primary" type="button">
                                        <span aria-hidden="true" class="bi bi-plus"></span>
                                        Add skill
                                    </button>&nbsp;
                                    <button class="btn btn-secondary" type="button">
                                        Cancel
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="modal" id="specialize-modal" tabindex="-1" role="dialog">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Choose specialization</h5>
                    <button type="button" class="close" data-bs-dismiss="modal"
                        aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <p>Choose your specialization for <span
                    id="specialization-skill-name"></span>.</p>
                    <div class="row">
                        <div class="col">
                            <input class="form-control"
                                id="specialization-entry" type="text">
                        </div>
                    </div>

                    <div class="row mt-4">
                        <div class="col">
                            <button class="btn btn-primary mr-1" type="button">
                                Specialize
                            </button>
                            <button class="btn btn-secondary" type="button">
                                Cancel
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <template id="skill-row">
        <li class="list-group-item">
            <div class="row">
                <label class="col col-form-label text-nowrap name"></label>
                <div class="col">
                    <input name="skill-names[]" type="hidden">
                    <input class="form-control text-center" min="0" max="6"
                        name="skill-levels[]" step="1" type="number">
                    <input name="skill-specializations[]" type="hidden">
                </div>
                <div class="col text-right text-nowrap">
                    <button class="btn btn-success btn-sm specialize"
                        data-bs-target="#specialize-modal" data-bs-toggle="modal"
                        type="button">
                        <span aria-hidden="true" class="bi bi-plus"></span>
                        Specialization
                    </button>
                    <button class="btn btn-danger btn-sm skill" type="button">
                        <span aria-hidden="true" class="bi bi-dash"></span>
                        Skill
                    </button>
                </div>
            </div>
        </li>
    </template>

    <template id="group-row">
        <li class="list-group-item" data-id="">
            <div class="row">
                <label class="col col-form-label text-nowrap name"></label>
                <div class="col">
                    <input name="group-names[]" type="hidden">
                    <input class="form-control text-center" min="0" max="6"
                        name="group-levels[]" step="1" type="number">
                </div>
                <div class="col text-right">
                    <button class="btn btn-danger btn-sm">
                        <span aria-hidden="true" class="bi bi-dash"></span>
                        Group
                    </button>
                </div>
            </div>
        </li>
    </template>

    @include('shadowrun5e::create-points')

    <x-slot name="javascript">
        <script>
            let character = @json($character);
        </script>
        <script src="/js/datatables.min.js"></script>
        <script src="/js/Shadowrun5e/create-common.js"></script>
        <script src="/js/Shadowrun5e/Points.js"></script>
        <script src="/js/Shadowrun5e/create-skills.js"></script>
    </x-slot>
</x-app>
