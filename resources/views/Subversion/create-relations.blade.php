@php
use Illuminate\Support\Js;
@endphp
<x-app>
    <x-slot name="title">Create character: Relations</x-slot>
    <x-slot name="head">
        <style>
            .tooltip-inner {
                text-align: left;
            }
        </style>
    </x-slot>

    @include('Subversion.create-navigation')

    @if ($errors->any())
        <div class="my-4 row">
            <div class="col-1"></div>
            <div class="col">
                <div class="alert alert-danger">
                    <ul>
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            </div>
            <div class="col-4"></div>
        </div>
    @endif

    <div class="my-4 row">
        <div class="col-1"></div>
        <div class="col">
            <h1>Relations</h1>

            <p>
                Here, players determine their connections to other NPCs in the
                world. Each player may spend 30 Fortune to buy Relations from
                the Relation Paradigm (see &ldquo;Relation Paradigms&rdquo; on
                pg 132). This Fortune is not included in the starting 230
                Fortune and cannot be spent on other resources, though
                additional Fortune may be added to upgrade or add additional
                Relations.
            </p>
        </div>
        <div class="col-4"></div>
    </div>

    <form action="" id="form" method="POST">
    @csrf

    <div class="my-4 row">
        <div class="col-1"></div>
        <div class="col">
            <table class="table">
                <thead>
                    <tr>
                        <th scope="col">Name</th>
                        <th scope="col">Skill</th>
                        <th scope="col">Archetypes/Aspects</th>
                        <th scope="col">Power</th>
                        <th scope="col">Regard</th>
                        <th scope="col">Notes</th>
                        <td>&nbsp;</td>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($character->relations ?? [] as $relation)
                    <tr>
                        <td>
                            {{ $relation }}
                            @if ($relation->faction) (Faction) @endif
                        </td>
                        <td>{{ $relation->skill }}</td>
                        <td>{{ $relation->archetype }}</td>
                        <td>{{ $relation->power }}</td>
                        <td>{{ $relation->regard }}</td>
                        <td>{{ $relation->notes }}</td>
                        <td class="text-end">
                            <button class="btn btn-primary btn-sm modify mr-1"
                                data-id="{{ $relation->id }}" type="button">
                                <span aria-hidden="true" class="bi bi-wrench"></span>
                            </button>
                            <button class="btn btn-danger btn-sm"
                                data-id="{{ $relation->id }}" type="button">
                                <span aria-hidden="true" class="bi bi-dash"></span>
                            </button>
                        </td>
                    </tr>
                    @endforeach
                    <tr @if (0 !== count($character->relations ?? [])) class="d-none" @endif
                        id="no-relations">
                        <td colspan="7">You don't have any relations.</td>
                    </tr>
                </tbody>
                <tfoot>
                    <tr>
                        <td colspan="7">
                            <button class="btn btn-success"
                                data-bs-target="#relation-modal"
                                data-bs-toggle="modal" type="button">
                                Add relation
                            </button>
                        </td>
                    </tr>
                </tfoot>
            </table>
        </div>
        <div class="col-4"></div>
    </div>
    </form>

    <div aria-hidden="true" aria-labelledby="relation-modal-label"
        class="modal fade" id="relation-modal" tabindex="-1">
        <div class="modal-dialog">
            <div class="modal-content">
                <form class="needs-validation" id="modal-form" novalidate>
                <div class="modal-header">
                    <h1 class="modal-title fs-5" id="relation-modal-label">Add relation</h1>
                    <button aria-label="Close" class="btn-close"
                        data-bs-dismiss="modal" type="button"></button>
                </div>
                <div class="modal-body">
                    <div class="my-2 row">
                        <div class="col-2">
                            <label class="form-label" for="level">Level</label>
                        </div>
                        <div class="col">
                            <select class="form-control" id="level" required>
                                <option value="">Choose level</option>
                                @foreach ($levels as $level)
                                <option data-cost="{{ $level->cost }}"
                                    data-power="{{ $level->power }}"
                                    data-regard="{{ $level->regard }}"
                                    title="{{ $level->description }}"
                                    value="{{ $level->id }}">
                                    {{ ucfirst($level->level) }} - {{ $level }}
                                    (Cost: {{ $level->cost }},
                                    Power: {{ $level->power }},
                                    Regard: {{ $level->regard }})
                                </option>
                                @endforeach
                            </select>
                            <div class="invalid-feedback">
                                Please choose a level for the relation.
                            </div>
                        </div>
                    </div>
                    <div class="my-2 row">
                        <div class="col-2">
                            <label class="form-label" for="name">Name</label>
                        </div>
                        <div class="col">
                            <input autocomplete="off" class="form-control"
                                id="name" required type="text">
                            <div class="invalid-feedback">
                                Relations require a name.
                            </div>
                        </div>
                    </div>
                    <div class="mb-0 mt-2 row">
                        <div class="col-2">
                            <label class="form-label" for="skill">Skill</label>
                        </div>
                        <div class="col">
                            @foreach ($skills as $skill)
                            @continue($loop->even)
                            <div class="form-check">
                                <input aria-describedby="#skills-feedback"
                                    class="form-check-input"
                                    id="skill-{{ $skill->id }}"
                                    name="skills[]" required type="radio"
                                    value="{{ $skill->id }}">
                                <label class="form-check-label"
                                    @can('view data')
                                        data-bs-html="true"
                                        data-bs-toggle="tooltip"
                                        data-bs-title="<p>{{ str_replace('||', '</p><p>', $skill->description) }}</p>"
                                    @endcan
                                    for="skill-{{ $skill->id }}">
                                    {{ $skill }}
                                </label>
                            </div>
                            @endforeach
                        </div>
                        <div class="col">
                            @foreach ($skills as $skill)
                            @continue($loop->odd)
                            <div class="form-check">
                                <input class="form-check-input"
                                    id="skill-{{ $skill->id }}"
                                    name="skills[]" required type="radio"
                                    value="{{ $skill->id }}">
                                <label class="form-check-label"
                                    @can('view data')
                                        data-bs-html="true"
                                        data-bs-toggle="tooltip"
                                        data-bs-title="<p>{{ str_replace('||', '</p><p>', $skill->description) }}</p>"
                                    @endcan
                                    for="skill-{{ $skill->id }}">
                                    {{ $skill }}
                                </label>
                            </div>
                            @endforeach
                        </div>
                    </div>
                    <div class="my-0 row">
                        <div class="col-2"></div>
                        <div class="col">
                            <div class="invalid-feedback" id="skills-feedback">
                                Relations require at least one skill.
                            </div>
                        </div>
                    </div>
                    <div class="mb-0 mt-2 row">
                        <div class="col-2">
                            <div class="form-label">Archetype</div>
                        </div>
                        <div class="col">
                            @foreach ($archetypes as $archetype)
                            @continue($loop->even)
                            <div class="form-check">
                                <input class="form-check-input"
                                    data-additional="{{ (int)$archetype->has_additional }}"
                                    data-faction="{{ (int)$archetype->faction_only }}"
                                    id="archetype-{{ $archetype->id }}"
                                    name="archetypes[]" required type="radio"
                                    value="{{ $archetype->id }}">
                                <label class="form-check-label"
                                    @can('view data')
                                        data-bs-html="true"
                                        data-bs-toggle="tooltip"
                                        data-bs-title="<p>{{ str_replace('||', '</p><p>', $archetype->description) }}</p>"
                                    @endcan
                                    for="archetype-{{ $archetype->id }}">
                                    {{ $archetype }}
                                    @if ($archetype->faction_only)
                                        (Faction only)
                                    @endif
                                </label>
                            </div>
                            @endforeach
                        </div>
                        <div class="col">
                            @foreach ($archetypes as $archetype)
                            @continue($loop->odd)
                            <div class="form-check">
                                <input class="form-check-input"
                                    data-additional="{{ (int)$archetype->has_additional }}"
                                    data-faction="{{ (int)$archetype->faction_only }}"
                                    id="archetype-{{ $archetype->id }}"
                                    name="archetypes[]" required type="radio"
                                    value="{{ $archetype->id }}">
                                <label class="form-check-label"
                                    @can('view data')
                                        data-bs-html="true"
                                        data-bs-toggle="tooltip"
                                        data-bs-title="<p>{{ str_replace('||', '</p><p>', $archetype->description) }}</p>"
                                    @endcan
                                    for="archetype-{{ $archetype->id }}">
                                    {{ $archetype }}
                                    @if ($archetype->faction_only)
                                        (Faction only)
                                    @endif
                                </label>
                            </div>
                            @endforeach
                        </div>
                    </div>
                    <div class="my-0 row">
                        <div class="col-2"></div>
                        <div class="col">
                            <div class="invalid-feedback" id="archetypes-feedback">
                                Relations require at least one archetype.
                            </div>
                        </div>
                    </div>
                    <div class="my-2 row">
                        <div class="col-2">
                            <div class="form-label">
                                Faction
                            </div>
                        </div>
                        <div class="col">
                            <div class="form-check">
                                <label class="form-check-label">
                                    <input class="form-check-input" id="faction" type="checkbox" value="1">
                                    Relation is a faction
                                </label>
                            </div>
                        </div>
                    </div>
                    <div class="d-none my-2 row" id="category-row">
                        <div class="col-2">
                            <label class="form-label" for="category">
                                Category
                            </label>
                        </div>
                        <div class="col">
                            <input class="form-control" id="category" type="text">
                        </div>
                    </div>
                    <div class="my-2 row">
                        <div class="col-2"><div class="form-label">Aspects</div></div>
                        <div class="col">
                            @foreach ($aspects as $aspect)
                            @continue($loop->even)
                            <div class="form-check">
                                <label class="form-label"
                                    @can('view data')
                                    data-bs-toggle="tooltip"
                                    data-bs-html="true"
                                    data-bs-title="<p>{{ str_replace('||', '</p><p>', $aspect->description) }}</p>"
                                    @endcan
                                    >
                                    <input class="form-check-input"
                                        data-faction="{{ (int)$aspect->factionOnly }}"
                                        id="aspect-{{ $aspect->id }}"
                                        name="aspects[]" type="checkbox"
                                        value="{{ $aspect->id }}">
                                    {{ $aspect }}
                                </label>
                            </div>
                            @endforeach
                        </div>
                        <div class="col">
                            @foreach ($aspects as $aspect)
                            @continue($loop->odd)
                            <div class="form-check">
                                <label class="form-label"
                                    @can('view data')
                                    data-bs-toggle="tooltip"
                                    data-bs-html="true"
                                    data-bs-title="<p>{{ str_replace('||', '</p><p>', $aspect->description) }}</p>"
                                    @endcan
                                    >
                                    <input class="form-check-input"
                                        data-faction="{{ (int)$aspect->factionOnly }}"
                                        @if ($aspect->factionOnly) disabled @endif
                                        id="aspect-{{ $aspect->id }}"
                                        name="aspects[]" type="checkbox"
                                        value="{{ $aspect->id }}">
                                    {{ $aspect }}
                                </label>
                            </div>
                            @endforeach
                        </div>
                    </div>
                    <div class="my-2 row">
                        <div class="col-2">
                            <label class="form-label" for="notes">Notes</label>
                        </div>
                        <div class="col">
                            <textarea class="form-control" id="notes"></textarea>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button class="btn btn-secondary" data-bs-dismiss="modal"
                        type="button">Close</button>
                    <button class="btn btn-primary" type="submit">
                        Save changes
                    </button>
                </div>
                </form>
            </div>
        </div>
    </div>

    <template id="relation-template">
        <tr>
            <input name="relation_archetype[]" type="hidden">
            <input name="relation_aspects[]" type="hidden">
            <input name="relation_category[]" type="hidden">
            <input name="relation_faction[]" type="hidden">
            <input name="relation_level[]" type="hidden">
            <input name="relation_name[]" type="hidden">
            <input name="relation_notes[]" type="hidden">
            <input name="relation_skill[]" type="hidden">
            <td class="relation-name"></td>
            <td class="relation-skill"></td>
            <td class="relation-archetype"></td>
            <td class="relation-power"></td>
            <td class="relation-regard"></td>
            <td class="relation-notes"></td>
            <td class="text-end">
                <button class="btn btn-primary btn-sm modify mr-1" type="button">
                    <span aria-hidden="true" class="bi bi-wrench"></span>
                </button>
                <button class="btn btn-danger btn-sm" type="button">
                    <span aria-hidden="true" class="bi bi-dash"></span>
                </button>
            </td>
        </tr>
    </template>

    @include('Subversion.create-fortune')

    <x-slot name="javascript">
        <script src="/js/Subversion/Relation.js"></script>
        <script src="/js/Subversion/create-relation.js"></script>
        <script>
            'use strict';

            let character = @json($character);
            let fortune = {{ $character->fortune }};
            let relationFortune = {{ $character->relation_fortune }};
            const archetypes = {{ Js::from($archetypes); }};
            const aspects = {{ Js::from($aspects); }};
            const levels = {{ Js::from($levels); }};
            const skills = {{ Js::from($skills); }};
        </script>
    </x-slot>
</x-app>
