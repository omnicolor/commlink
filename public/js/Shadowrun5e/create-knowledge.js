$(function () {
    'use strict';

    function nameToId(name) {
        return name.replace(/ /g, '-').replace(/[^a-zA-Z0-9-]/g, '');
    }

    /**
     * Add the user's input as a knowledge skill.
     */
    function addKnowledge() {
        const knowledge = $('#choose-knowledge').val();
        const skill = {
            category: $('#knowledge-type').val(),
            id: nameToId(knowledge),
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
            id: nameToId(knowledge),
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
        const row = $($('#language-row')[0].content.cloneNode(true));
        row.find('label').attr('for', skill.id);
        row.find('li').attr('data-id', skill.id);
        row.find('.name').html(skill.name);
        row.find('input[name="skill-names[]"]').val(skill.name);
        if ('N' === skill.level) {
            row.find('input[name="skill-levels[]"]')
                .prop('id', skill.id + '-language')
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
        row.find('label').attr('for', skill.id);
        row.find('li').attr('data-id', skill.id);
        row.find('.name').html(skill.name);
        row.find('input[name="skill-names[]"]').val(skill.name);
        row.find('input[name="skill-levels[]"]')
            .prop('id', skill.id + '-' + skill.category)
            .val(skill.level);
        row.find('input[name="skill-categories[]"]').val(skill.category);

        row.insertBefore($('#no-knowledge'));
        $('#no-knowledge').hide();
    }

    /**
     * Reset the knowledge form to its pristine state.
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
        const parent = $(e.target).parents('li');
        const name = parent.find('.name').text().split(' (+2 ')[0].trim();
        const isLanguage = parent.hasClass('language');
        for (let i = 0, c = character.knowledgeSkills.length; i < c; i++) {
            let skill = character.knowledgeSkills[i];
            if (
                'language' !== skill.category && isLanguage
                || 'language' === skill.category && !isLanguage
                || name !== skill.name
            ) {
                continue;
            }
            character.knowledgeSkills[i].level = parseInt(el.val(), 10);
            break;
        }

        points = new Points(character);
        updatePointsToSpendDisplay(points);
    }

    /**
     * User wants to specialize a knowledge skill.
     */
    function populateKnowledgeSpecializationModal(e) {
        const el = $(e.relatedTarget).parents('li');
        const id = el.data('id');
        const name = el.find('.name').html().trim();
        const isLanguage = el.hasClass('language');
        if (isLanguage) {
            $('#knowledge-specialization-entry').data('language', true);
            $('#knowledge-specialization-entry')[0]
                .setAttribute('list', 'language-specializations')
        } else {
            $('#knowledge-specialization-entry').data('language', false);
            $('#knowledge-specialization-entry')[0]
                .setAttribute('list', null)
        }
        $('#knowledge-specialization-skill-name').html(name + '.');
        $('#knowledge-specialization-entry')
            .data('id', id)
            .data('name', name)
            .val('')
            .focus();
        $('#specialize-modal .btn-primary').prop('disabled', true);
    }

    /**
     * User has chosen a specialization for a knowledge skill.
     */
    function addKnowledgeSpecialization() {
        const input = $('#knowledge-specialization-entry');
        const name = input.data('name');
        const isLanguage = input.data('language');
        let foundSkill;
        $.each(character.knowledgeSkills, function (unused, skill) {
            if (foundSkill) {
                return;
            }
            if (
                isLanguage && 'language' !== skill.category
                || !isLanguage && 'language' === skill.category
            ) {
                return;
            }
            if (skill.name === name) {
                foundSkill = skill;
            }
        });
        if (!foundSkill) {
            return;
        }

        const specialization = input.val().trim();
        const id = input.data('id');
        foundSkill.specialization = specialization;
        const row = $('li[data-id="' + id + '"]');
        const nameEl = row.find('.name');
        nameEl.html(
            nameEl.html() + ' (+2 ' + htmlEncode(specialization) + ')'
        );
        row.find('.btn-success')
            .replaceWith(
                '<button class="btn btn-danger btn-sm specialize" ' +
                'type="button">' +
                '<span aria-hidden="true" class="bi bi-dash"></span> ' +
                'Specialization' +
                '</button>'
            );
        row.find('input[name="skill-specializations[]"]').val(specialization);

        $('#specialize-modal').modal('hide')

        points = new Points(character);
        updatePointsToSpendDisplay(points);
    }

    /**
     * User has changed something language related.
     * @param {!Event} e Event that fired this handler
     */
    function updateLanguageLevel(e) {
        const el = $(e.target);
        const id = el.parents('li').data('id');
        for (let i = 0, c = character.knowledgeSkills.length; i < c; i++) {
            if (character.knowledgeSkills[i].id !== id) {
                continue;
            }

            character.knowledgeSkills[i].level = parent.find('.language-level').val();
        }

        points = new Points(character);
        updatePointsToSpendDisplay(points);
    }

    /**
     * Remove a skill from the character.
     * @param {!Event} e Event that fired this handler
     */
    function removeSkill(e) {
        const parent = $(e.target).parents('li');
        const name = parent.find('.name').text().split(' (+2 ')[0].trim();
        parent.find('input[name="skill-specializations[]"]').val('');
        if (parent.hasClass('language')) {
            for (let i = 0, c = character.knowledgeSkills.length; i < c; i++) {
                let skill = character.knowledgeSkills[i];
                if ('language' !== skill.category || name !== skill.name) {
                    continue;
                }
                character.knowledgeSkills.splice(i, 1);
                break;
            }
        } else {
            for (let i = 0, c = character.knowledgeSkills.length; i < c; i++) {
                let skill = character.knowledgeSkills[i];
                if ('language' === skill.category || name !== skill.name) {
                    continue;
                }
                character.knowledgeSkills.splice(i, 1);
                break;
            }
        }

        let noKnowledge = true;
        let noLanguage = true;
        for (let i = 0, c = character.knowledgeSkills.length; i < c; i++) {
            if (character.knowledgeSkills[i].category === 'language') {
                noLanguage = false;
                continue;
            }
            noKnowledge = false;
        }
        $('#no-knowledge').toggle(noKnowledge);
        $('#no-languages').toggle(noLanguage);

        points = new Points(character);
        updatePointsToSpendDisplay(points);

        parent.remove();
    }

    function removeSpecialization(e) {
        const parent = $(e.target).parents('li');
        const nameEl = parent.find('.name');
        const name = nameEl.text().split(' (+2 ')[0].trim();
        parent.find('input[name="skill-specializations[]"]').val('');
        const isLanguage = parent.hasClass('language');
        for (let i = 0, c = character.knowledgeSkills.length; i < c; i++) {
            let skill = character.knowledgeSkills[i];
            if (
                'language' !== skill.category && isLanguage
                || 'language' === skill.category && !isLanguage
                || name !== skill.name
            ) {
                continue;
            }
            delete skill.specialization;
            break;
        }
        nameEl.text(name);
        parent.find('.btn-danger.specialize')
            .replaceWith(
                '<button class="btn btn-success btn-sm specialize" ' +
                'data-bs-target="#specialize-modal" data-bs-toggle="modal" ' +
                'type="button">' +
                '<span aria-hidden="true" class="bi bi-plus"></span>' +
                'Specialization</button>'
            );

        points = new Points(character);
        updatePointsToSpendDisplay(points);
    }

    let points = new Points(character);
    updatePointsToSpendDisplay(points);

    $('#knowledge-modal').on('shown.bs.modal', clearKnowledgeModal);
    $('#choose-knowledge')
        .on('keyup', enableAddKnowledgeButton)
        .on('change', enableAddKnowledgeButton);
    $('#knowledge-level')
        .on('keyup', enableAddKnowledgeButton)
        .on('change', enableAddKnowledgeButton);
    $('#knowledge-type').on('change', enableAddKnowledgeButton);
    $('#knowledge-modal .btn-primary').on('click', addKnowledge);

    // Update knowledge points when knowledge or language levels are changed.
    $('#skills')
        .on('change', 'input[name="skill-levels[]"]', updateKnowledgeLevel);

    $('#specialize-modal')
        .on('shown.bs.modal', populateKnowledgeSpecializationModal);
    $('#knowledge-specialization-entry').on('keyup', function () {
        $('#specialize-modal .btn-primary').prop(
            'disabled',
            '' == $('#knowledge-specialization-entry').val()
        );
    });
    $('#specialize-modal .btn-primary').on('click', addKnowledgeSpecialization);

    $('#native').on('change', enableLanguageButtons);
    $('#language-level').on('keyup', enableLanguageButtons)
        .on('change', enableLanguageButtons);
    $('#language-modal .btn-primary').on('click', addLanguage);
    $('#language-modal').on('shown.bs.modal', clearLanguageModal);
    $('#skills .language-level').on('change', updateLanguageLevel);
    $('#skills').on('click', '.btn-danger.skill', removeSkill);
    $('#skills').on('click', '.btn-danger.specialize', removeSpecialization);
});
