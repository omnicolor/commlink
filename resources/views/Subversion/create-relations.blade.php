@php
use Illuminate\Support\Js;
@endphp
<x-app>
    <x-slot name="title">Create character: Relations</x-slot>
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
                        <th scope="col">Archetype/Aspect</th>
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
                                type="button">
                                <span aria-hidden="true" class="bi bi-wrench"></span>
                            </button>
                            <button class="btn btn-danger btn-sm" type="button">
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
                <form id="modal-form">
                <div class="modal-header">
                    <h1 class="modal-title fs-5" id="relation-modal-label">Add relation</h1>
                    <button aria-label="Close" class="btn-close"
                        data-bs-dismiss="modal" type="button"></button>
                </div>
                <div class="modal-body">
                    <div class="my-1 row">
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
                        </div>
                    </div>
                    <div class="my-1 row">
                        <div class="col-2">
                            <label class="form-label" for="name">Name</label>
                        </div>
                        <div class="col">
                            <input autocomplete="off" class="form-control"
                                id="name" required type="text">
                        </div>
                    </div>
                    <div class="my-1 row">
                        <div class="col-2">
                            <label class="form-label" for="skill">Skill</label>
                        </div>
                        <div class="col">
                            <select class="form-control" id="skill" required>
                                <option value="">Choose skill</option>
                                @foreach ($skills as $skill)
                                <option title="{{ $skill->description }}"
                                    value="{{ $skill->id }}">{{ $skill }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                    <div class="my-1 row">
                        <div class="col-2">
                            <label class="form-label" for="archetype">Archetype</label>
                        </div>
                        <div class="col">
                            <select class="form-control" id="archetype" required>
                                <option value="">Choose archetype</option>
                                @foreach ($archetypes as $archetype)
                                <option data-additional="{{ (int)$archetype->has_additional }}"
                                    data-faction="{{ (int)$archetype->faction_only }}"
                                    title="{{ $archetype->description }}"
                                    value="{{ $archetype->id }}">
                                    {{ $archetype }}
                                    @if ($archetype->faction_only)
                                        (Faction only)
                                    @endif
                                </option>
                                @endforeach
                            </select>
                            <div class="form-check">
                                <input class="form-check-input" id="faction" type="checkbox" value="1">
                                <label class="form-check-label" for="faction" title="Is the relation a faction?">
                                    Faction
                                </label>
                            </div>
                        </div>
                    </div>
                    <div class="d-none my-1 row" id="category-row">
                        <div class="col-2">
                            <label class="form-label" for="category">
                                Category
                            </label>
                        </div>
                        <div class="col">
                            <input class="form-control" id="category" type="text">
                        </div>
                    </div>
                    <div class="my-1 row">
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
        <script>
            'use strict';

            let character = @json($character);
            let fortune = {{ $character->fortune }};
            let relationFortune = 30;
            const archetypes = {{ Js::from($archetypes); }};
            const levels = {{ Js::from($levels); }};
            const skills = {{ Js::from($skills); }};

            function addRelation(e) {
                e.preventDefault();

                let relation = {
                    archetype: $('#archetype').val(),
                    faction: $('#faction').prop('checked'),
                    level: $('#level').val(),
                    name: $('#name').val(),
                    notes: $('#notes').val(),
                    skill: $('#skill').val()
                };

                let archetype;
                $.each(archetypes, function (unused, value) {
                    if (value.id === relation.archetype) {
                        archetype = value;
                    }
                });
                let level;
                $.each(levels, function (unused, value) {
                    if (value.id === relation.level) {
                        level = value;
                    }
                });
                let skill;
                $.each(skills, function (unused, value) {
                    if (value.id === relation.skill) {
                        skill = value;
                    }
                });

                const row = $($('#relation-template')[0].content.cloneNode(true));
                row.find('input[name="relation_archetype[]"]')
                    .val(relation.archetype);

                if (1 === $('#archetype option:selected').data('additional')) {
                    relation.category = $('#category').val();
                    row.find('input[name="relation_category[]"]')
                        .val(relation.category);
                    row.find('.relation-archetype').html(
                        archetype.name + ' (' + relation.category + ')'
                    );
                } else {
                    row.find('.relation-archetype').html(archetype.name);
                }
                row.find('input[name="relation_level[]"]').val(relation.level);
                row.find('input[name="relation_name[]"]').val(relation.name);
                if (relation.faction) {
                    row.find('.relation-name')
                        .html(relation.name + ' (Faction)');
                    row.find('input[name="relation_faction[]"]').val(true);
                } else {
                    row.find('.relation-name').html(relation.name);
                }
                row.find('.relation-power').html(level.power);
                row.find('.relation-regard').html(level.regard);
                row.find('input[name="relation_notes[]"]').val(relation.notes);
                row.find('.relation-notes').html(relation.notes);
                row.find('input[name="relation_skill[]"]').val(relation.skill);
                row.find('.relation-skill').html(skill.name);

                const noRelationsRow = $('#no-relations');
                row.insertBefore(noRelationsRow);
                $('#modal-form')[0].reset();
                $('#category-row').addClass('d-none');
                noRelationsRow.addClass('d-none');
            }

            $(function () {
                $('#modal-form').on('submit', addRelation);
                $('#archetype').on('change', function (e) {
                    if (1 === $('#archetype option:selected').data('faction')) {
                        $('#faction').prop('checked', true)
                            .prop('readonly', true);
                    } else {
                        $('#faction').prop('checked', false)
                            .prop('readonly', false);
                    }
                    if (1 !== $('#archetype option:selected').data('additional')) {
                        $('#category').val('');
                        $('#category-row').addClass('d-none');
                    } else {
                        $('#category-row').removeClass('d-none');
                    }
                });
            });
        </script>
    </x-slot>
</x-app>
