$(function () {
    'use strict';

    /**
     * Add the user's input as a knowledge skill.
     */
    function addKnowledge() {
        const knowledge = $('#choose-knowledge').val();
        const skill = {
            category: $('#knowledge-type').val(),
            id: knowledge.replace(/ /g, '-').replace(/[^a-zA-Z0-9-]/g, ''),
            name: knowledge,
            level: parseInt($('#knowledge-level').val(), 10)
        };
        character.knowledgeSkills.push(skill);
        addSkillRow(skill);
        clearKnowledgeModal();

        points = new Points(character);
        updatePointsToSpendDisplay(points);
    }

    /**
     * User has chosen which language to add.
     */
    function addLanguage() {
        const knowledge = $('#choose-language').val();
        let skill = {
            category: 'language',
            id: knowledge.replace(/ /g, '-').replace(/[^a-zA-Z0-9-]/g, ''),
            name: knowledge,
            level: null
        };
        if ($('#native').prop('checked')) {
            skill.level = 'N';
        } else {
            skill.level = parseInt($('#language-level').val(), 10)
        }
        character.knowledgeSkills.push(skill);
        addLanguageRow(skill);
        clearLanguageModal();

        points = new Points(character);
        updatePointsToSpendDisplay(points);
    }

    /**
     * Add the given language skill to the knowledge skill list.
     * @param {!Object} skill Language to add
     */
    function addLanguageRow(skill) {
        const row = $($('#knowledge-row')[0].content.cloneNode(true));
        row.find('label').attr('for', skill.id);
        row.find('li').attr('data-id', skill.id);
        row.find('.name').html(skill.name);
        row.find('input[name="skill-names[]"]').val(skill.name);
        if ('N' === skill.level) {
            row.find('input[name="skill-levels[]"]')
                .prop('id', skill.id)
                .prop('type', 'text')
                .prop('readonly', true)
                .val('N');
        } else {
            row.find('input[name="skill-levels[]"]')
                .prop('id', skill.id)
                .val(skill.level);
        }

        row.insertBefore($('#no-languages'));
        $('#no-languages').hide();
    }

    /**
     * Add the given knowledge skill to the knowledge skill list.
     * @param {!Object} skill Skill to add
     */
    function addSkillRow(skill) {
        const row = $($('#skill-row')[0].content.cloneNode(true));
        row.find('li').attr('data-id', skill.id);
        row.find('.name').html(skill.name);
        row.find('input[name="skill-levels[]"]')
            .prop('id', skill.id)
            .val(skill.level);

        row.insertBefore($('#no-knowledge'));
        $('#no-knowledge').hide();
    }

    /**
     * Reset the knowledge form to it's pristine state.
     */
    function clearKnowledgeModal() {
        $('#choose-knowledge').val('').focus();
        $('#knowledge-type').val('');
        $('#knowledge-level').val('');
    }

    /**
     * Reset the inputs on the language modal.
     */
    function clearLanguageModal() {
        $('#native').prop('checked', false);
        $('#choose-language').val('').focus();
        $('#language-level').val('').prop('disabled', false);
        $('#language-modal .btn-primary').prop('disabled', true);
    }

    /**
     * When either the knowledge text box or knowledge type dropdown change,
     * update the disabled property on the submit button.
     */
    function enableAddKnowledgeButton() {
        const invalid = '' == $('#choose-knowledge').val()
            || '' == $('#knowledge-type').val()
            || !parseInt($('#knowledge-level').val(), 10);
        $('#knowledge-modal .btn-primary').prop('disabled', invalid);
    }

    /**
     * Enable or disable the language level selector when the native checkbox
     * changes, as well as the add language button.
     * @param {!Event} e Event that fired this handler
     */
    function enableLanguageButtons(e) {
        const native = $('#native').prop('checked');
        const languageSet = '' !== $('#choose-language').val();
        const levelSet = '' !== $('#language-level').val();

        if (native) {
            $('#language-level').val('').prop('disabled', true);
            $('#language-modal .btn-primary').prop('disabled', false);
            return;
        }

        $('#language-level').prop('disabled', false);
        $('#language-modal .btn-primary').prop(
            'disabled',
            '' === $('#language-level').val()
        );
    }

    /**
     * User has updated a knowledge level.
     * @param {!Event} e Event that fired this handler
     */
    function updateKnowledgeLevel(e) {
        const el = $(e.target);
        const id = el.parents('li').data('id');
        for (let i = 0, c = character.knowledgeSkills.length; i < c; i++) {
            if (character.knowledgeSkills[i].id !== id) {
                continue;
            }
            character.knowledgeSkills[i].level = parseInt(el.val(), 10);
            break;
        }

        points = new Points(character);
        updatePointsToSpendDisplay(points);
    }

    /**
     * User wants to specialize their knowledge skill.
     */
    var populateKnowledgeSpecializationModal = function (e) {
        var el = $(e.relatedTarget).parents('li');
        var skillId = el.data('id');;
        var skillName = el.find('.name').html();
        $('#knowledge-specialization-skill-name').html(skillName);
        $('#knowledge-specialization-entry')
            .data('id', skillId)
            .val('')
            .focus();
        $('#specialize-knowledge-modal .btn-primary').prop('disabled', true);
    };

    /**
     * User has chosen a specialization for a knowledge skill.
     */
    var addKnowledgeSpecialization = function () {
        var input = $('#knowledge-specialization-entry');
        var skillId = input.data('id');
        var foundSkill;
        $.each(chosenKnowledgeSkills, function (unused, skill) {
            if (foundSkill) {
                return;
            }
            if (skill.id == skillId) {
                foundSkill = skill;
            }
        });
        if (!foundSkill) {
            return;
        }
        var specialization = input.val();
        foundSkill.specialization = specialization;
        var li = $('li[data-id="' + skillId + '"]');
        var nameEl = li.find('.name');
        nameEl.html(
            nameEl.html() + ' (+2 ' + sr.htmlEncode(specialization) + ')'
        );
        var button = li.find('.btn-success');
        button.replaceWith('<button class="btn btn-danger btn-sm specialize" ' +
            'type="button">' +
            '<span aria-hidden="true" class="oi oi-minus"></span> ' +
            'Specialization' +
            '</button>');
        button.find('span').removeClass('oi-plus').addClass('oi-minus');
        $('#specialize-knowledge-modal').modal('hide')
    };

    /**
     * User has changed something language related.
     * @param {!Event} e Event that fired this handler
     */
    function updateLanguageLevel(e) {
        const el = $(e.target);
        const id = el.parents('li').data('id');
        for (var i = 0, c = character.knowledgeSkills.length; i < c; i++) {
            if (character.knowledgeSkills[i].id !== id) {
                continue;
            }
            window.conosl.log(character.knowledgeSkills[i].level);

            character.knowledgeSkills[i].level = parent.find('.language-level').val();
        }
    }

    /**
     * Remove a skill from the character.
     * @param {!Event} e Event that fired this handler
     */
    var removeSkill = function (e) {
        var parent = $(e.target).parents('li');
        var id = parent.data('id');
        if (parent.hasClass('language')) {
            for (var i = 0, c = chosenLanguages.length; i < c; i++) {
                if (id != chosenLanguages[i].id) {
                    continue;
                }
                chosenLanguages.splice(i, 1);
                break;
            }
        } else {
            for (var i = 0, c = chosenKnowledgeSkills.length; i < c; i++) {
                if (id != chosenKnowledgeSkills[i].id) {
                    continue;
                }
                chosenKnowledgeSkills.splice(i, 1);
                break;
            }
        }
        parent.remove();
    };

    let points = new Points(character);

    $('#knowledge-modal').on('shown.bs.modal', clearKnowledgeModal);
    $('#choose-knowledge')
        .on('keyup', enableAddKnowledgeButton)
        .on('change', enableAddKnowledgeButton);
    $('#knowledge-level')
        .on('keyup', enableAddKnowledgeButton)
        .on('change', enableAddKnowledgeButton);
    $('#knowledge-type').on('change', enableAddKnowledgeButton);
    $('#knowledge-modal .btn-primary').on('click', addKnowledge);

    // Update knowledge points when both knowledge and language levels are
    // changed.
    $('#skills')
        .on('change', 'input[name="skill-levels[]"]', updateKnowledgeLevel);

    /*
    $('#specialize-knowledge-modal')
        .on('shown.bs.modal', populateKnowledgeSpecializationModal);
    $('#knowledge-specialization-entry').on('keyup', function () {
        $('#specialize-knowledge-modal .btn-primary').prop(
            'disabled',
            '' == $('#knowledge-specialization-entry').val()
        );
    });
    $('#specialize-knowledge-modal .btn-primary')
        .on('click', addKnowledgeSpecialization);
    */

    $('#native').on('change', enableLanguageButtons);
    $('#language-level').on('keyup', enableLanguageButtons)
        .on('change', enableLanguageButtons);
    $('#language-modal .btn-primary').on('click', addLanguage);
    $('#language-modal').on('shown.bs.modal', clearLanguageModal);
    $('#skills .language-level').on('change', updateLanguageLevel);

    /*
    $('#skills .btn-danger.skill').on('click', removeSkill);
    */
});
