<?php
use App\Models\Shadowrun5e\ActiveSkill;
?>
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

    <datalist id="archetypes">
        @foreach (\App\Models\Shadowrun5e\Contact::archetypes() as $archetype)
        <option value="{{ $archetype }}">
        @endforeach
    </datalist>

    <x-slot name="navbar">
        <li class="nav-item">
            <a class="nav-link" href="/dashboard">Home</a>
        </li>
        <li class="nav-item">
            <a class="nav-link" href="/campaigns/{{ $campaign->id }}">{{ $campaign }}</a>
        </li>
        <li class="nav-item dropdown">
            <a class="active nav-link dropdown-toggle" href="#"
                id="gm-screen-dropdown" role="button" data-bs-toggle="dropdown"
                aria-expanded="false">
                GM screen
            </a>
            <ul class="dropdown-menu" aria-labelledby="gm-screen-dropdown">
                <li><a class="dropdown-item" data-bs-target="#contact-modal"
                    data-bs-toggle="modal" href="#">Add contact</a></li>
            </ul>
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

    <div class="card float-start m-2">
        <div class="card-header">
            <h5 class="card-title">Knowledge skills</h5>
        </div>
        <table class="card-body m-1">
            <thead>
                <th scope="col">Character</th>
                <th scope="col">Skill</th>
                <th scope="col"><small>Cat / Att<small></th>
                <th scope="col">Rat</th>
                <th scope="col">Att</th>
                <th class="text-end" scope="col">Dice</th>
            </thead>
            <tbody>
                @foreach ($characters as $character)
                @php
                    $knowledges = (array)$character->getKnowledgeSkills(onlyKnowledges: true);
                    $languages = (array)$character->getKnowledgeSkills(onlyLanguages: true);
                    $count = count($knowledges) + count($languages);
                @endphp
                @foreach (array_merge($languages, $knowledges) as $skill)
                    @if ($loop->first)
                    <tr class="border-top">
                        <td>{{ $character }}</td>
                    @else
                    <tr>
                    @endif
                    @if ($loop->index === 1)
                        <td class="align-top" rowspan="{{ $count - 1 }}">
                            <small class="text-muted">
                                Mental limit: {{ $character->mental_limit }}
                            </small>
                        </td>
                    @endif
                        <td>
                            {{ $skill }}
                            @if ($skill->specialization)
                                <br><small class="ps-4 text-muted">
                                    Specialization: {{ $skill->specialization }}
                                </small>
                            @endif
                        </td>
                        <td class="align-top">
                            <small>
                                {{ ucfirst($skill->short_category) }} /
                                {{ strtoupper(substr($skill->attribute, 0, 3)) }}
                            </small>
                        </td>
                        <td class="align-top text-center">{{ $skill->level }}</td>
                        <td class="align-top">{{ $character->{$skill->attribute} }}</td>
                        <td class="align-top text-end">
                            {{ ($skill->level === 'N' ? 12 : (int)$skill->level) + $character->{$skill->attribute} }}
                        </td>
                    </tr>
                @endforeach
                @endforeach
            </tbody>
        </table>
    </div>

    <div class="card float-start m-2">
        <div class="card-header">
            <h5 class="card-title">Active skills</h5>
        </div>
        <table class="card-body m-1">
            <thead class="border-bottom">
                <th scope="col">Skill</th>
                <th scope="col">Att</th>
                <th scope="col">Def</th>
                <th scope="col">Lim</th>
                @foreach ($characters as $character)
                    @php
                        $character->renderedSkills = $character->getSkills();
                    @endphp
                    <th colspan="2" scope="col">{{ $character }}</th>
                @endforeach
            </thead>
            <tbody>
                @foreach (ActiveSkill::all() as $skill)
                <tr>
                    <td>{{ $skill }}</td>
                    <td>{{ strtoupper(substr($skill->attribute, 0, 3)) }}</td>
                    <td>{{ $skill->default ? 'Y' : 'N' }}</td>
                    <td class="border-end">{{ $skill->limit }}</td>
                    @foreach ($characters as $character)
                        @if (isset($character->renderedSkills[$skill->id]))
                            @php
                                $activeSkill = $character->renderedSkills[$skill->id];
                            @endphp
                            <td class="text-center fw-bold">
                                {{ $activeSkill->level }}
                            </td>
                            <td class="text-center fw-bold
                                @if (!$loop->last) border-end @endif
                                    ">
                                    {{ $activeSkill->level + $character->getModifiedAttribute($skill->attribute) }}
                                    [{{ $character->getSkillLimit($activeSkill) }}]
                                </td>
                        @elseif ($skill->default)
                            <td class="text-center"><small class="text-muted fs-6 fw-light">0</small></td>
                            <td class="text-center @if (!$loop->last) border-end @endif ">
                                <small class="text-muted fs-6 fw-light">
                                {{ $character->getModifiedAttribute($skill->attribute) - 1 }}
                                [{{ $character->getSkillLimit($skill) }}]
                                </small>
                            </td>
                        @else
                            <td colspan="2" @if (!$loop->last) class="border-end" @endif>&nbsp;</td>
                        @endif
                    @endforeach
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>

    <div class="card float-start m-2">
        <div class="card-header">
            <h5 class="card-title">Contacts</h5>
        </div>
        <table class="card-body m-1">
            <thead>
                <tr>
                    <th scope="col">Name</th>
                    <th scope="col">Archetype</th>
                    <th scope="col">Con/Loy</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($campaign->contacts() as $contact)
                <tr class="bg-light">
                    <td>{{ $contact }}</td>
                    <td>{{ $contact->archetype }}</td>
                    <td>Con: {{ $contact->connection }}</td>
                </tr>
                    @foreach ($contact->characters as $character)
                        <tr>
                            <td class="ps-4">{{ $character['character'] }}</td>
                            <td>&nbsp;</td>
                            <td>Loy: {{ $character['loyalty'] }}</td>
                        </tr>
                    @endforeach
                @endforeach
            </tbody>
        </table>
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
                    <button aria-label="Close" class="btn-close"
                        data-bs-dismiss="modal" type="button"></button>
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

    <form class="needs-validation" id="contact-form" novalidate>
        <input id="contact-character-id" type="hidden">
        <input id="contact-id" type="hidden">
        <div class="modal" id="contact-modal" tabindex="-1"
            aria-labelledby="contact-header" aria-hidden="true">
            <div class="modal-dialog modal-xl">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="contact-header">
                            Add a contact
                        </h5>
                        <button type="button" class="btn-close"
                            data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        <div id="contact-characters">
                            <div class="form-label">
                                Contact should be added for
                            </div>
                            @foreach ($characters as $character)
                            <div class="form-check">
                                <input class="form-check-input" type="checkbox"
                                    value="{{ $character->id }}"
                                    id="contact-character-{{ $character->id }}"
                                    checked>
                                    <label class="form-check-label"
                                        for="contact-character-{{ $character->id }}">
                                        {{ $character }}
                                    </label>
                                </div>
                            @endforeach
                        </div>
                        <div class="my-3">
                            <label class="form-label" for="contact-name">
                                Name
                                <small class="text-muted">(required)</small>
                            </label>
                            <input aria-describedby="contact-name-help"
                                class="form-control" id="contact-name"
                                type="text">
                            <div id="contact-name-help" class="form-text">
                                Name the 'runners know the contact as.
                            </div>
                        </div>
                        <div class="mb-3">
                            <label class="form-label" for="contact-archetype">
                                Archetype
                                <small class="text-muted">(required)</small>
                            </label>
                            <input aria-describedby="archetype-help"
                                class="form-control" id="contact-archetype"
                                list="archetypes" required type="text">
                            <div id="archetype-help" class="form-text">
                                Generic description of the contact's role in
                                'runner society.
                            </div>
                        </div>
                        <div class="mb-3">
                            <label class="form-label" for="contact-connection">
                                Connection
                            </label>
                            <input aria-describedby="connection-help"
                                class="form-control" id="contact-connection"
                                max="6" min="0" type="number">
                            <div id="connection-help" class="form-text">
                                Connection represents how much reach and
                                influence a Contact has, both within the shadows
                                and in the world at large, to get things done or
                                to make things happen. See the
                                <a data-bs-toggle="tooltip" data-bs-html="true"
                                    href="#" title="<img src='/images/Shadowrun5e/contact-connection.png'>">
                                    connection rating table
                                </a> for more information.
                            </div>
                        </div>
                        <div class="mb-3">
                            <label class="form-label" for="contact-loyalty">
                                Loyalty
                            </label>
                            <input aria-describedby="loyalty-help"
                                class="form-control" id="contact-loyalty"
                                max="6" min="0" type="number">
                            <div id="loyalty-help" class="form-text">
                                Loyalty reflects how loyal the contact is to the
                                runner and how much they'll endure without
                                shattering whatever bond the two have. See the
                                <a data-bs-toggle="tooltip" data-bs-html="true"
                                    href="#" title="<img src='/images/Shadowrun5e/contact-loyalty.png'>">
                                    loyalty rating table
                                </a> for more information.
                            </div>
                        </div>
                        <div class="mb-3">
                            <label class="form-label" for="contact-notes">
                                Player notes
                            </label>
                            <textarea aria-describedby="contact-notes-help"
                                class="form-control" id="contact-notes"
                                row="3"></textarea>
                            <div class="form-text" id="contact-notes-help">
                                Notes that you want available to the player(s)
                                that have met the contact. This might be where
                                they met, a physical description, or favors
                                owed, for example.
                                <strong>This field is editable by players.</strong>
                            </div>
                        </div>
                        <div class="mb-3">
                            <label class="form-label" for="contact-gm-notes">
                                Gamemaster notes
                            </label>
                            <textarea aria-describedby="contact-gm-notes-help"
                                class="form-control" id="contact-gm-notes"
                                row="3"></textarea>
                            <div class="form-text" id="contact-gm-notes-help">
                                Notes you want to make about the contact, but
                                not show to the players. Might be things like
                                motivations or the voice you use when
                                roleplaying them, for example.
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary"
                            data-bs-dismiss="modal">Close</button>
                        <button type="submit" class="btn btn-primary" disabled
                            id="contact-submit">Save contact</button>
                    </div>
                </div>
            </div>
        </div>
    </form>

    <x-slot name="javascript">
        <script>
            const campaign = {{ $campaign->id }};
            const csrfToken = '{{ csrf_token() }}';
            const tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'));
            const tooltipList = tooltipTriggerList.map(function (tooltipTriggerEl) {
                return new bootstrap.Tooltip(tooltipTriggerEl);
            });
        </script>
        <script src="/js/Shadowrun5e/gm-damage.js"></script>
        <script src="/js/Shadowrun5e/gm-initiative.js"></script>
        <script src="/js/Shadowrun5e/gm-contacts.js"></script>
    </x-slot>
</x-app>
