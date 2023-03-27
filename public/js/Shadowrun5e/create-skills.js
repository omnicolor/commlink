$(function () {
    'use strict';

    /**
     * List of common active skills.
     * @type {!Object}
     */
    let activeSkills = {};

    /**
     * List of skill groups.
     * @type {!Object}
     */
    let skillGroups = {};

    /**
     * User has chosen to add a skill.
     * @param {Event} e Click that fired this handler
     */
    function addActiveSkill(e) {
        let el = $(e.target);
        if ('SPAN' === el[0].nodeName) {
            el = el.parent();
        }
        const skill = activeSkills[el.data('id')];
        if (typeof skill === 'undefined') {
            return;
        }
        skill.level = parseInt($('#skill-level').val(), 10);
        character.skills.push({
            id: skill.id,
            name: skill.name,
            level: skill.level,
        });
        addActiveSkillRow(skill);
        resetActiveSkillModal();
        points = new Points(character);
        updatePointsToSpendDisplay(points);
    }

    /**
     * Add the given skill to the page.
     * @param {!Object} skill Skill object
     */
    function addActiveSkillRow(skill) {
        let max = 6;

        // See if the character has any qualities that change the maximum.
        $.each(character.qualities, function (unused, quality) {
            if (!quality.effects || [] === quality.effects) {
                return;
            }
            $.each(quality.effects, function (effect, amount) {
                if ('maximum-' + skill.id !== effect) {
                    // The effect is not about this skill.
                    return;
                }
                max += amount;
            });
        });

        const row = $($('#skill-row')[0].content.cloneNode(true));
        if (trusted) {
            row.find('.name').html(
                '<span data-bs-html="true" data-bs-toggle="tooltip" ' +
                'title="<p>' + cleanDescription(skill.description) + '</p>">' +
                skill.name + '</span>'
            );
        } else {
            row.find('.name').html(skill.name);
        }
        row.find('li').attr('data-id', skill.id);
        row.find('input[name="skill-levels[]"]')
            .prop('id', skill.id)
            .prop('max', max)
            .val(skill.level);
        row.find('input[name="skill-names[]"]').val(skill.id);
        row.insertBefore($('#no-active-skills'));

        $('#no-active-skills').hide();
    }

    /**
     * User has chosen to add a skill group.
     * @param {!Event} e Event that fired this handler
     */
    function addSkillGroup(e) {
        const id = $(e.target).parents('tr').data('id');
        const group = {id: id, level: 1};

        character.skillGroups.push(group);
        addSkillGroupRow(group);
        filterGroupList();

        points = new Points(character);
        updatePointsToSpendDisplay(points);
    }

    /**
     * Add the given skill group displayed skill groups.
     * @param {!Object} skillGroup Skill group to add
     */
    function addSkillGroupRow(skillGroup) {
        const row = $(
            document.querySelector('#group-row').content.cloneNode(true)
        );
        row.find('li').attr('data-id', skillGroup.id);
        row.find('.name').html(
            skillGroup.id[0].toUpperCase() +
            skillGroup.id.slice(1).replace('-', ' ')
        );
        row.find('input[name="group-names[]"]').val(skillGroup.id);
        row.find('input[name="group-levels[]"]').val(skillGroup.level);
        row.insertBefore($('#no-skill-groups'));
        $('#no-skill-groups').hide();
    };

    /**
     * Add a specialization to a skill.
     */
    function addSpecialization() {
        const el = $('#specialization-entry');
        const skillId = el.data('id');
        const specialization = el.val();
        if (!specialization) {
            return;
        }

        // Find the skill in the chosen skills list
        let skill;
        $.each(character.skills, function (unused, potentialSkill) {
            if (skillId === potentialSkill.id) {
                skill = potentialSkill;
                return false;
            }
        });
        skill.specialization = specialization;
        $('#specialize-modal').modal('hide');

        const li = $('li[data-id="' + skillId + '"]');
        const nameEl = li.find('.name');
        nameEl.html(nameEl.html() + ' (+2 ' + htmlEncode(specialization) +
            ')');
        li.find('input[name="skill-specializations[]"]').val(specialization);
        const button = li.find('.btn-success');
        button.replaceWith('<button class="btn btn-danger btn-sm specialize" ' +
            'type="button">' +
            '<span aria-hidden="true" class="bi bi-dash"></span> ' +
            'Specialization' +
            '</button>');
        button.find('span').removeClass('bi-plus').addClass('bi-dash');

        points = new Points(character);
        updatePointsToSpendDisplay(points);
    }

    /**
     * Set the invalid class on rows and disabled property on buttons when
     * drawing the skills group modal.
     */
    function filterGroupList() {
        const rows = $('#group-list tbody tr');
        $.each(rows, function (unused, row) {
            const valid = isSkillGroupValid(skillGroups[$(row).data('id')])
            $(row).toggleClass('invalid', !valid);
            $(row).find('button').prop('disabled', !valid);
        });
    }

    /**
     * User clicked the name of a skill in the list.
     * @param {Event} e Event that fired this handler
     */
    function handleSkillClick(e) {
        let el = $(e.target);
        if ('TD' === el[0].nodeName) {
            el = el.parent();
        }
        const skill = activeSkills[el.data('id')];
        $('#skill-name').html(skill.name);
        $('#skill-description').html(cleanDescription(skill.description));
        $('#skill-attribute').html(
            ucfirst(skill.attribute) + ' &ndash; ' +
            character[skill.attribute]
        );
        if (skill.group) {
            $('#skill-group').html(ucfirst(skill.group.replace('-', ' ')));
        } else {
            $('#skill-group').html('None');
        }
        if (skill.default) {
            $('#skill-default').html('Yes');
        } else {
            $('#skill-default').html('No');
        }
        $('#skill-info-panel .btn-primary')
            .data('id', skill.id)
            .prop('disabled', !isActiveSkillValid(skill));

        $('#skill-click-panel').hide();
        $('#skill-info-panel').show();
        $('#skill-level').focus();
    }

    /**
     * Return whether the character can add a skill.
     * @param {!Object} skill Skill to test
     * @return {!boolean}
     */
    function isActiveSkillValid(skill) {
        const magicTypes = ['magician', 'aspected', 'mystic'];
        if (!skill) {
            return false;
        }

        if (
            -1 === magicTypes.indexOf(character.priorities.magic)
            && skill.magicOnly
        ) {
            // Skill requires magic but the character isn't awakened.
            return false;
        }

        // If the character is not a technomancer, but the attribute is
        // resonance, they can't use it.
        if (
            'technomancer' !== character.priorities.magic
            && 'resonance' === skill.attribute
        ) {
            return false;
        }

        if (skill.group) {
            let inSkillGroup = false;

            // See if the skill is in a skill group the character already has.
            $.each(character.skillGroups, function (unused, group) {
                if (skill.group == group.id) {
                    inSkillGroup = true;
                }
            });

            if (inSkillGroup) {
                return false;
            }
        }

        /** @type {!boolean} */
        let notChosen = true;

        // See if the skill has already been chosen.
        $.each(character.skills, function (unused, potentialSkill) {
            if (potentialSkill.id === skill.id) {
                notChosen = false;
                return;
            }
        });
        return notChosen;
    }

    /**
     * Return whether the given character can add a skill group.
     * @param {!Object} group Group to test
     * @return {!boolean}
     */
    function isSkillGroupValid(group) {
        if (undefined !== character.skillGroups[group.id]) {
            // Character already has the skill group.
            return false;
        }

        let valid = true;

        // Check all of the skills in the group for validity. If the skills
        // require magic or resonance and the character doesn't have that, the
        // group can't be used. If the character has any skills in the group
        // already, they can't add the group.
        $.each(group.skills, function (unused, potentialSkill) {
            // Ignore the text properties of the group.
            if ('object' !== typeof potentialSkill) {
                return;
            }

            // Either the character can't use a skill (not awakened for magic or
            // resonance skills) or they've already chosen a skill in the group.
            if (!isActiveSkillValid(potentialSkill)) {
                valid = false;
                return false;
            }
        });

        return valid;
    }

    /**
     * User wants to add a specialization.
     * @param {!Event} e Event that fired this handler
     */
    function populateSpecializeModal(e) {
        const skillId = $(e.relatedTarget).parents('li').data('id');;
        const list = $('#' + skillId + '-specializations');
        const skill = activeSkills[skillId];
        if (!list.length && skill.specializations) {
            // Specializations list doesn't exist, add it.
            const specializations = skill.specializations;
            $(document.body).append(
                '<datalist id="' + skillId + '-specializations">' +
                '<option value="' + specializations.join('"><option value="') +
                '"></datalist>'
            );
        }

        $('#specialization-skill-name').html(skill.name);
        $('#specialization-entry')
            .data('id', skillId)
            .attr('list', skillId + '-specializations')
            .val('')
            .focus();
        $('#specialize-modal .btn-primary').prop('disabled', true);
    }

    /**
     * Process the AJAX response from the server with active skills.
     * @param {Object} data Data from the server
     */
    function processActiveSkills(data) {
        let skill;
        let html = '';

        for (let cnt = data.data.length, i = 0; i < cnt; i++) {
            skill = data.data[i];
            activeSkills[skill.id] = skill;
            html += '<tr data-id="' + skill.id + '">' +
                '<td>' + skill.name + '</td>' +
                '<td>' + ucfirst(skill.attribute) + '</td>' +
                '<td>';
            if (skill.group) {
                html += ucfirst(skill.group.replace('-', ' '));
            } else {
                html += '&nbsp;';
            }
            html += '</td></tr>';
        }
        $('#skill-list tbody').append(html);
        const table = $('#skill-list').DataTable({
            columns: [
                {}, // name
                {}, // attribute
                {orderable: false} // group
            ],
            info: false
        });
        $('#skill-list_length').remove();
        $('#skill-list_filter').remove();
        $('#search-skills').on('keyup', function () {
            table.search(this.value).draw();
        });
        table.on('draw', updateSkillValidity);
    }

    /**
     * Process the AJAX response from the server with skill groups.
     * @param {Object} data Data from the server
     */
    function processSkillGroups(data) {
        let html = '';
        let group;
        let skills;
        data.data.sort(sortById);
        for (let cnt = data.data.length, i = 0; i < cnt; i++) {
            group = data.data[i];
            skillGroups[data.data[i].id] = group;
            skills = [];
            $.each(group.skills, function (unused, skill) {
                skills.push(skill.name);
            });
            html += '<tr data-id="' + group.id + '">' +
                '<td class="text-nowrap">' +
                ucfirst(group.id.replace('-', ' ')) + '</td>' +
                '<td>' + skills.join(', ') + '</td>' +
                '<td>' +
                '<button class="btn btn-success btn-sm text-nowrap" type="button">' +
                '<span aria-hidden="true" class="bi bi-plus"></span> ' +
                'Add group</button></td>' +
                '</tr>';
        }
        $('#group-list tbody').append(html);
    }

    /**
     * User wants to remove a skill.
     * @param {!Event} e Event that fired this handler
     */
    function removeSkill(e) {
        const el = $(e.target).parents('li');
        const skillId = el.data('id');

        el.remove();

        for (let i = 0, c = character.skills.length; i < c; i++) {
            if (character.skills[i].id === skillId) {
                character.skills.splice(i, 1);
                break;
            }
        }

        if (!character.skills.length) {
            $('#no-active-skills').show();
        }
        points = new Points(character);
        updatePointsToSpendDisplay(points);
    }

    /**
     * User wants to remove a skill group.
     * @param {!Event} e Event that fired this handler
     */
    function removeSkillGroup(e) {
        const row = $(e.target).parents('li');
        const groupId = row.data('id');

        let groups = [];
        $.each(character.skillGroups, function (index, group) {
            if (groupId === group.id) {
                delete character.skillGroups[index];
                return;
            }
            groups.push(group);
        });
        character.skillGroups = groups;

        if (isEmpty(character.skillGroups)) {
            $('#no-skill-groups').show();
        }
        row.remove();

        filterGroupList();
        points = new Points(character);
        updatePointsToSpendDisplay(points);
    }

    /**
     * User wants to remove a specialization.
     * @param {!Event} e Event that fired this handler
     */
    function removeSpecialization(e) {
        const skillId = $(e.target).parents('li').data('id');
        let skill;
        for (let i = 0, c = character.skills.length; i < c; i++) {
            if (skillId == character.skills[i].id) {
                skill = character.skills[i];
                break;
            }
        }

        skill.specialization = null;
        const li = $('li[data-id="' + skillId + '"]');
        li.find('.name').html(activeSkills[skillId].name);
        li.find('input[name="skill-specializations[]"]').val('');
        let button = li.find('.btn-danger.specialize');
        button.replaceWith(
            '<button class="btn btn-success btn-sm specialize" ' +
            'data-bs-target="#specialize-modal" data-bs-toggle="modal" ' +
            'type="button">' +
            '<span aria-hidden="true" class="bi bi-plus"></span> ' +
            'Specialization</button>'
        );

        points = new Points(character);
        updatePointsToSpendDisplay(points);
    }

    /**
     * Reset active skills modal.
     */
    function resetActiveSkillModal() {
        $('#skill-click-panel').show();
        $('#skill-info-panel').hide();
        updateSkillValidity();
        $('#search-skills').focus();
    }

    /**
     * Given two things with id members, compare the two items on the ID.
     * @param {!Object} a
     * @param {!Object} b
     * @return {integer}
     */
    function sortById(a, b) {
        if (a.id < b.id) {
            return -1;
        }
        if (a.id > b.id) {
            return 1;
        }
        return 0;
    }

    /**
     * Handle user updating a skill group level.
     * @param {!Event} e Event that fired this handler
     */
    function updateSkillGroupRating(e) {
        const id = $(e.target).parents('li').data('id');
        $.each(character.skillGroups, function (index, group) {
            if (group.id !== id) {
                return;
            }

            character.skillGroups[index] = {
                id: id,
                level: parseInt(e.target.value, 10)
            };
        });

        points = new Points(character);
        updatePointsToSpendDisplay(points);
    }

    /**
     * Handle user updating a skill level.
     * @param {!Event} e Event that fired this handler
     */
    function updateSkillRating(e) {
        const id = $(e.target).parents('li').data('id');
        $.each(character.skills, function (index, skill) {
            if (skill.id === id) {
                skill.level = parseInt(e.target.value, 10);
            }
        });
        points = new Points(character);
        updatePointsToSpendDisplay(points);
    }

    /**
     * Update the active skills table, marking invalid skills as invalid.
     */
    function updateSkillValidity() {
        const rows = $('#skill-list tbody tr');
        $.each(rows, function (unused, row) {
            var skill = activeSkills[$(row).data('id')];
            $(row).toggleClass('invalid', !isActiveSkillValid(skill));
        });
    }

    $.ajax('/api/shadowrun5e/skills').done(processActiveSkills);
    $.ajax('/api/shadowrun5e/skill-groups').done(processSkillGroups);

    if (typeof character.skills === 'undefined') {
        character.skills = [];
    }
    if (typeof character.skillGroups === 'undefined') {
        character.skillGroups = [];
    }
    let points = new Points(character);

    $('[data-bs-toggle="tooltip"]').tooltip();

    $('#skill-groups-list').on('change', 'input', updateSkillGroupRating);
    $('#skill-groups-list').on('click', '.btn-danger', removeSkillGroup);
    $('#group-list').on('click', '.btn-success', addSkillGroup);
    $('#group-modal').on('show.bs.modal', filterGroupList);

    $('#skill-modal').on('show.bs.modal', updateSkillValidity);
    $('#skill-modal')
        .on('shown.bs.modal', function () { $('#search-skills').focus(); });
    $('#skill-list tbody').on('click', 'tr', handleSkillClick);
    $('#skill-info-panel').on('click', '.btn-primary', addActiveSkill);
    $('#active-skills-list').on('change', 'input', updateSkillRating);
    $('#active-skills-list').on('click', '.btn-danger.skill', removeSkill);

    $('#specialize-modal').on('shown.bs.modal', populateSpecializeModal);
    $('#specialize-modal .btn-primary').on('click', addSpecialization);
    $('#specialize-modal .btn-secondary').on('click', function () {
        $('#specialize-modal').modal('hide');
    });
    $('#specialization-entry').on('keyup', function () {
        $('#specialize-modal .btn-primary').prop(
            'disabled',
            '' == $('#specialization-entry').val()
        );
    });
    $('#active-skills-list')
        .on('click', '.btn-danger.specialize', removeSpecialization);
});
