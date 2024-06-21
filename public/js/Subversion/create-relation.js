'use strict';

function replaceWithObject(values, id) {
    let retVal = null;
    $.each(values, function (unused, value) {
        if (value.id === id) {
            retVal = value;
            return;
        }
    });
    return retVal;
}

function addRelation() {
    let archetypesIds = [];
    let archetypeNames = [];
    let aspectsIds = [];
    let aspectNames = [];
    let skillsIds = [];
    let skillNames = [];

    const row = $($('#relation-template')[0].content.cloneNode(true));
    const relation = new Relation(
        $('#name').val(),
        $('#faction').prop('checked'),
        replaceWithObject(levels, $('#level').val()),
        $('#notes').val()
    );

    $.each(
        $('input[name="archetypes[]"]:checked'),
        function (index, archetype) {
            archetypesIds.push($(archetype).val());
            const archetypeObj = replaceWithObject(
                archetypes,
                $(archetype).val()
            );
            if (archetypeObj.id === 'dealer') {
                relation.category = $('#category').val();
                archetypeNames.push(archetypeObj.name + ' ('
                    + relation.category + ')');
                row.find('input[name="relation_category[]"]')
                    .val(relation.category);
            } else {
                archetypeNames.push(archetypeObj.name);
            }
            relation.archetypes.push(archetypeObj);
        }
    );
    $.each(
        $('input[name="skills[]"]:checked'),
        function (index, skill) {
            skillsIds.push($(skill).val());
            const skillObj = replaceWithObject(skills, $(skill).val());
            skillNames.push(skillObj.name);
            relation.skills.push(skill);
        }
    );
    $.each(
        $('input[name="aspects[]"]:checked'),
        function (index, aspect) {
            aspectsIds.push($(aspect).val());
            const aspectObj = replaceWithObject(aspects, $(aspect).val());
            relation.aspects.push(aspectObj);
            aspectNames.push(aspectObj.name);
        }
    );

    row.find('input[name="relation_archetype[]"]')
        .val(archetypesIds.join(','));
    if (aspectNames.length > 0) {
        row.find('.relation-archetype').html(
            archetypeNames.join(', ') + ' / ' + aspectNames.join(', ')
        );
    } else {
        row.find('.relation-archetype').html(archetypeNames.join(', '));
    }
    row.find('input[name="relation_aspects[]"]').val(aspectsIds.join(','));
    row.find('input[name="relation_level[]"]').val(relation.level.id);
    row.find('input[name="relation_name[]"]').val(relation.name);
    if (relation.faction) {
        row.find('.relation-name')
            .html(relation.name + ' (Faction)');
        row.find('input[name="relation_faction[]"]').val(true);
    } else {
        row.find('.relation-name').html(relation.name);
    }
    row.find('.relation-power').html(relation.level.power);
    row.find('.relation-regard').html(relation.level.regard);
    row.find('input[name="relation_notes[]"]').val(relation.notes);
    row.find('.relation-notes').html(relation.notes);
    row.find('input[name="relation_skill[]"]').val(skillsIds.join(','));
    row.find('.relation-skill').html(skillNames.join(', '));

    const noRelationsRow = $('#no-relations');
    row.insertBefore(noRelationsRow);
    $('#modal-form')[0].reset();
    $('#modal-form').removeClass('was-validated');
    $('#category-row').addClass('d-none');
    noRelationsRow.addClass('d-none');

    relationFortune = relationFortune - relation.cost();
    window.console.log(relationFortune);
    if (0 > relationFortune) {
        fortune = fortune + relationFortune;
        $('#relation-fortune').html(relationFortune);
    } else {
        $('#relation-fortune').html('+0');
    }
}

$(function () {
    const tooltipTriggerList = document.querySelectorAll('[data-bs-toggle="tooltip"]')
    const tooltipList = [...tooltipTriggerList].map(tooltipTriggerEl => new bootstrap.Tooltip(tooltipTriggerEl))

    const form = $('#modal-form');
    form.on('submit', function (e) {
        e.preventDefault();
        e.stopPropagation();
        $('#modal-form').addClass('was-validated');

        const skills = $('input[name="skills[]"]:checked').length;
        if (skills === 0) {
            $("input[name='skills[]']").addClass('is-invalid');
            $('#skills-feedback').addClass('d-block');
        } else {
            $("input[name='skills[]']").removeClass('is-invalid');
            $('#skills-feedback').removeClass('d-block');
        }

        const archetypes = $('input[name="archetypes[]"]:checked').length;
        if (archetypes === 0) {
            $("input[name='archetypes[]']").addClass('is-invalid');
            $('#archetypes-feedback').addClass('d-block');
        } else {
            $("input[name='archetypes[]']").removeClass('is-invalid');
            $('#archetypes-feedback').removeClass('d-block');
        }

        if (form[0].checkValidity()) {
            addRelation();
        }

        return false;
    });
    $('input[name="skills[]"]').on('change', function (e) {
        if (0 !== $('input[name="skills[]"]:checked').length) {
            $('input[name="skills[]"]').removeClass('is-invalid');
            $('#skills-feedback').removeClass('d-block');
        } else {
            $('input[name="skills[]"]').addClass('is-invalid');
            $('#skills-feedback').addClass('d-block');
        }
    });

    $('input[name="archetypes[]"]').on('change', function (e) {
        const archetypes = $('input[name="archetypes[]"]:checked');
        if (0 !== archetypes.length) {
            $('input[name="archetypes[]"]').removeClass('is-invalid');
            $('#archetypes-feedback').removeClass('d-block');
        } else {
            $('input[name="archetypes[]"]').addClass('is-invalid');
            $('#archetypes-feedback').addClass('d-block');
        }

        let faction = false;
        let additional = false;
        $.each(archetypes, function (index, el) {
            if (1 === $(el).data('faction')) {
                faction = true;
            }
            if (1 === $(el).data('additional')) {
                additional = true;
            }
        });

        if (faction) {
            // Don't uncheck the faction checkbox for the user if
            // they choose an archetype that doesn't require it to
            // be a faction, the user may still want it to be a
            // faction.
            $('#faction').prop('checked', true).change();
        }
        $('#faction').prop('disabled', faction)
            .prop('readonly', faction);

        if (additional) {
            $('#category-row').removeClass('d-none');
        } else {
            $('#category').val('');
            $('#category-row').addClass('d-none');
        }
    });

    $('#faction').on('change', function (e) {
        const faction = $('#faction').prop('checked');
        const factionAspects = $('[name="aspects[]"][data-faction="1"]');
        factionAspects.prop('disabled', !faction);

        if (!faction) {
            // Uncheck any that require it to be a faction when the relation is
            // no longer a faction.
            factionAspects.prop('checked', false);
        }
    });

    $('#aspect-multi-talented').on('change', function (e) {
        if ($('#aspect-multi-talented').prop('checked')) {
            $('input[name="archetypes[]"]')
                .prop('required', false)
                .prop('type', 'checkbox');
            $('input[name="skills[]"]')
                .prop('required', false)
                .prop('type', 'checkbox');
        } else {
            $('input[name="archetypes[]"]')
                .prop('required', true)
                .prop('type', 'radio');
            $('input[name="skills[]"]')
                .prop('required', true)
                .prop('type', 'radio');
        }
    });
});
