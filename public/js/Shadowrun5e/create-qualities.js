$(function () {
    'use strict';

    /**
     * List of qualities a character can choose.
     * @type {Object}
     */
    let qualities = {};

    /**
     * List of magic types for magic-only qualities.
     * @type {Array}
     */
    const mageTypes = ['adept', 'aspected', 'magician', 'mystic'];

    /**
     * Add an addiction quality to the character.
     * @param {!Event} e Event that fired this handler
     */
    function addAddiction(e) {
        const severity = $('#severity').val();
        const addiction = $('#addiction').val();
        if (!severity || !addiction) {
            return;
        }
        let el = $(e.target);
        if ('SPAN' === el[0].nodeName) {
            el = el.parent();
        }
        const id = el.data('name').replace(' ', '-').toLowerCase();
        let quality = findQualityById(id + '-' + severity);
        quality.addiction = addiction;
        finishAddQuality(quality);
    }

    /**
     * Allergy qualities are special snowflages and require some extra care.
     * @param {!Event} e Event that fired this handler
     */
    function addAllergy(e) {
        const rarity = $('#rarity').val();
        const severity = $('#severity').val();
        const allergy = $('#allergy').val();

        if (!rarity || !severity || !allergy) {
            return;
        }

        let quality = findQualityById('allergy-' + rarity + '-' + severity);
        quality.allergy = allergy;
        finishAddQuality(quality);
    }

    /**
     * Add the Indomitable quality.
     * @param {!Event} e Event that fired this handler
     */
    function addIndomitable(e) {
        const level = parseInt($('#indomitable-level').val(), 10);
        if (!level) {
            return;
        }

        const quality = findQualityById('indomitable-' + level);
        if (!quality) {
            return;
        }
        quality.limits = [];

        let limit = '';
        if (1 <= level) {
            limit = $('#limit-0').val();
            if (!limit) {
                return;
            }
            quality.limits.push(limit);
        }

        if (2 <= level) {
            limit = $('#limit-1').val();
            if (!limit) {
                return;
            }
            quality.limits.push(limit);
        }

        if (3 === level) {
            limit = $('#limit-2').val();
            if (!limit) {
                return;
            }
            quality.limits.push(limit);
        }

        quality.effects = {
            'mental-limit': 0,
            'physical-limit': 0,
            'social-limit': 0
        };
        $.each(quality.limits, function (unused, limit) {
            quality.effects[limit + '-limit']++;
        });

        finishAddQuality(quality);
    }

    /**
     * Add a quality to the character.
     * @param {Event} e Event that fired this handler
     */
    function addQuality(e) {
        let el = $(e.target);
        if ('SPAN' === el[0].nodeName) {
            el = el.parent();
        }
        const quality = findQualityById(el.data('id'));
        if (!quality) {
            return;
        }
        finishAddQuality(quality);
    }

    /**
     * Some qualities require choosing what they apply to.
     * @param {!Event} e Event that fired this handler
     */
    function addQualityWithDropdown(e) {
        const quality = $('#choose-quality').val();
        $(e.target).data('id', quality);
        addQuality(e);
    }

    /**
     * Checks the validity for an addiction quality.
     */
    function checkAddictionValidity() {
        const severity = $('#severity').val();
        const addiction = $('#addiction').val();
        let valid = true;

        if (!severity) {
            valid = false;
            $('#severity').removeClass('is-valid');
        } else {
            $('#severity').addClass('is-valid');
        }

        if (!addiction) {
            valid = false;
            $('#addiction').removeClass('is-valid');
        } else {
            $('#addiction').addClass('is-valid');
        }

        $('#quality-add .btn-success').prop('disabled', !valid);
    }

    /**
     * Checks validity for the allergy entries and enable or disable the submit
     * button.
     */
    function checkAllergyValidity() {
        const rarity = $('#rarity').val();
        const severity = $('#severity').val();
        const allergy = $('#allergy').val();
        let valid = true;

        if (!rarity) {
            valid = false;
            $('#rarity').removeClass('is-valid');
        } else {
            $('#rarity').addClass('is-valid');
        }
        if (!severity) {
            valid = false;
            $('#severity').removeClass('is-valid');
        } else {
            $('#severity').addClass('is-valid');
        }
        if (!allergy) {
            valid = false;
            $('#allergy').removeClass('is-valid');
        } else {
            $('#allergy').addClass('is-valid');
        }

        $('#add-allergy').prop('disabled', !valid);
    }

    /**
     * Checks a quality that has a dropdown for validity, setting classes on the
     * dropdown and enabling or disabling the button.
     */
    function checkDropdownValidity() {
        const quality = $('#choose-quality');
        if (!quality.val()) {
            quality.removeClass('is-valid');
            $('#quality-add .btn-success').prop('disabled', true);
        } else {
            quality.addClass('is-valid');
            $('#quality-add .btn-success').prop('disabled', false);
        }
    }

    /**
     * Add a quality to the list of qualities.
     * @param {!Object} quality Quality to add to the list
     */
    function displayQuality(quality) {
        $('#no-qualities').hide();

        let html = '<li class="list-group-item">';
        if (trusted) {
            html += '<span data-bs-html="true" data-bs-toggle="tooltip" ' +
                'title="<p>' + cleanDescription(quality.description) + '</p>">';
        } else {
            html += '<span>';
        }
        html += quality.name;
        let extra = '';
        if (quality.skill) {
            html += ' - ' + quality.skill;
            extra = quality.skill;
        } else if ('Allergy' === quality.name) {
            html += ' - ' + quality.severity.replace('-', ', ') + ', ' +
                htmlEncode(quality.allergy);
            extra = quality.allergy;
        } else if ('Addiction' === quality.name ||
                'Dry Addict' === quality.name) {
            html += ' - ' + quality.severity + ', ' +
                htmlEncode(quality.addiction);
            extra = quality.addiction;
        } else if (quality.severity) {
            html += ' - ' + quality.severity.replace('-', ', ');
        } else if (quality.level) {
            html += ' ' + quality.level;
        } else if (quality.attribute) {
            html += ' - ' + quality.attribute;
        }
        if (quality.limits) {
            html += ' (' + quality.limits.join(', ') + ')';
        }
        if ('' !== extra) {
            extra = '_' + encodeURIComponent(extra);
        }
        html += '<input name="quality[]" type="hidden" value="' +
            quality.id + extra + '">' +
            '</span><div class="float-end">' +
            '<button class="btn btn-danger btn-sm" data-id="' + quality.id +
            '" ' + 'role="button"><span class="bi bi-dash"></span> ' +
            'Remove</button>' +
            '</div></li>';
        $(html).insertBefore($('#no-qualities'));
    }

    /**
     * Find a quality in the qualities list by ID.
     * @param {string} id ID of the quality to find
     * @return {?Object}
     */
    function findQualityById(id) {
        let foundQuality = null;
        $.each(qualities, function (unused, qualityArray) {
            if (foundQuality) {
                return false;
            }
            $.each(qualityArray, function (unused, quality) {
                if (id === quality.id) {
                    foundQuality = quality;
                    return false;
                }
            });
        });
        return foundQuality;
    }

    /**
     * Add quality to the character, update their stats, add it to the page, and
     * update/close the modal.
     * @param {Quality} quality
     */
    function finishAddQuality(quality) {
        if (typeof character.qualities === 'undefined') {
            character.qualities = [];
        }
        character.qualities.push(quality);
        displayQuality(quality);
        resetAddQualityModal();
        updateListValidity();
        points = new Points(character);
        updatePointsToSpendDisplay(points);
    }

    /**
     * Given a list of qualities, return the karma cost(s) for them.
     * @param {Array} qualities
     * @return {string}
     */
    function getKarmaCost(qualities) {
        if (1 === qualities.length) {
            return qualities[0].karma;
        }
        let costs = [];
        $.each(qualities, function (index, level) {
            costs.push(level.karma);
        });
        costs = $.unique(costs);
        return costs.join(', ');
    }

    /**
     * Given a name and boolean for whether validation passed, return the HTML
     * for a badge to show if it's valid.
     * @param {string} name Name of the validation rule
     * @param {boolean} passed Whether the validation passed
     * @return {string} HTML for a badge
     */
    function getValidationBadge(name, passed) {
        if (passed) {
            return '<span class="badge badge-pill bg-success">' + name +
                '</span>';
        }
        return '<span class="badge badge-pill bg-danger">' + name + '</span>';
    }

    /**
     * Get a list of validation rules for a given quality.
     * @param {Object} quality Quality to test
     * @return {Array} List of validation criteria
     */
    function getValidationRules(quality) {
        let validation = [];
        if (quality.adeptOnly) {
            validation.push({
                name: 'Adept',
                test: -1 !== ['adept', 'mystic'].indexOf(character.priorities.magic)
            });
        }
        if (quality.magicOnly) {
            validation.push(
                {name: 'Magic', test: -1 !== mageTypes.indexOf(character.priorities.magic)}
            );
        }
        if (quality.technomancerOnly) {
            validation.push(
                {name: 'Technomancer', test: 'technomancer' === character.priorities.magic}
            );
        }
        if ('elf-poser' === quality.id) {
            validation.push({name: 'Human', test: 'human' === character.priorities.metatype});
        }
        if ('ork-poser' === quality.id) {
            validation.push({
                name: 'Human/Elf',
                test: -1 !== ['elf', 'human'].indexOf(character.priorities.metatype)
            });
        }
        if ('human-looking' === quality.id) {
            validation.push({
                name: 'Dwarf/Elf/Ork',
                test: -1 !== ['elf', 'dwarf', 'ork'].indexOf(character.priorities.metatype)
            });
        }
        if (quality['incompatible-with'] &&
                -1 !== quality['incompatible-with'].indexOf('magic')) {
            validation.push({
                name: 'Mundane',
                test: -1 !== mageTypes.indexOf(character.priorities.magic)
            });
        }
        $.each(character.qualities, function (unused, currentQuality) {
            $.each(
                currentQuality['incompatible-with'],
                function (unused, incompatibility) {
                    if (incompatibility !== quality.id) {
                        return;
                    }
                    if (currentQuality.name === quality.name) {
                        validation.push({name: 'Allowed once', test: false});
                        return;
                    }
                    validation.push({
                        name: 'Incompatible: ' + currentQuality.name,
                        test: false
                    });
                }
            );
        });
        if (!quality.requires) {
            return validation;
        }

        let requires = quality.requires;
        $.each(requires, function (unused, requirement) {
            let valid = false;
            switch (requirement.type) {
                case 'active-skill':
                    for (let i = 0, c = activeSkills.length; i < c; i++) {
                        if (activeSkills[i].id != requirement.id) {
                            continue;
                        }
                        if (activeSkills[i].level >= requirement.min) {
                            valid = true;
                            break;
                        }
                    }
                    validation.push({name: requirement.name, test: valid});
                    break;
                case 'knowledge-skill':
                    for (let i = 0, c = knowledgeSkills.length; i < c; i++) {
                        if (knowledgeSkills[i].id.toLowerCase() != requirement.id) {
                            continue;
                        }
                        if (knowledgeSkills[i].level >= requirement.min) {
                            valid = true;
                            break;
                        }
                    }
                    validation.push({name: requirement.name, test: valid});
                    break;
                case 'quality':
                    for (let i = 0, c = chosenQualities.length; i < c; i++) {
                        if (chosenQualities[i].id !== requirement.id) {
                            continue;
                        }
                        valid = true;
                        break;
                    }
                    validation.push({name: requirement.name, test: valid});
                    break;
                case 'qualities':
                    for (let i = 0, c = chosenQualities.length; i < c; i++) {
                        if (-1 === requirement.ids.indexOf(chosenQualities[i].id)) {
                            continue;
                        }
                        valid = true;
                        break;
                    }
                    validation.push({name: requirement.name, test: valid});
                    break;
                default:
                    // No validation rules written yet.
                    break;
            };
        });

        return validation;
    }

    /**
     * Handle the user changing the indomitable level.
     */
    function handleIndomitableLevelChange() {
        const level = $('#indomitable-level').val();

        switch (level) {
            case '1':
                $('#limit-0').parent().show();
                $('#limit-1').parent().hide();
                $('#limit-2').parent().hide();
                break;
            case '2':
                $('#limit-0').parent().show();
                $('#limit-1').parent().show();
                $('#limit-2').parent().hide();
                break;
            case '3':
                $('#limit-0').parent().show();
                $('#limit-1').parent().show();
                $('#limit-2').parent().show();
                break;
        }
        validateIndomitable();
    }

    /**
     * Handle a click on a quality's name.
     * @param {Event} e Event that fired the handler
     */
    function handleQualityClick(e) {
        const name = $(e.target).parents('tr').data('id');
        const quality = qualities[name];
        let html = '';

        $('#click-panel').hide();
        $('#info-panel').show();

        $('#quality-name').html(name);
        if (0 < quality[0].karma) {
            $('#quality-type').html('Negative quality');
        } else {
            $('#quality-type').html('Positive quality');
        }
        $('#quality-karma').html(getKarmaCost(quality));

        $('#quality-description').html(
            cleanDescription(quality[0].description)
        );
        if (quality[0].page) {
            $('#quality-ruleset').html(
                rulebooks[quality[0].ruleset].name + ', p' + quality[0].page
            );
        } else {
            $('#quality-ruleset').html(rulebooks[quality[0].ruleset].name);
        }

        const validation = getValidationRules(quality[0]);
        let valid = true;
        if (!validation.length) {
            $('#quality-validation').html('None');
        } else {
            html = '';
            for (let i = 0, c = validation.length; i < c; i++) {
                html += getValidationBadge(
                    validation[i].name, validation[i].test
                ) + '&nbsp;';
                valid = valid && validation[i].test;
            }
            $('#quality-validation').html(html);
        }

        if (1 === quality.length) {
            $('#quality-add').html('<div class="row col">' +
                '<button class="btn btn-success" ' +
                'data-id="' + quality[0].id + '" type="button">' +
                '<span aria-hidden="true" class="bi bi-plus"></span> ' +
                'Add quality</button>&nbsp;' +
                '<button class="btn btn-secondary" type="button">Cancel' +
                '</button>' +
                '</div>');
            $('#quality-add .btn-success').on('click', addQuality);
        } else if ('Allergy' === quality[0].name) {
            $('#quality-add').html(
                '<div class="row col col-form-label row">Choose quality:' +
                '</div>' +
                '<div class="row col mb-1">' +
                '<select class="form-control" id="rarity">' +
                '<option value="">Choose rarity' +
                '<option value="uncommon">Uncommon (+2 karma)' +
                '<option value="common">Common (+7 karma)' +
                '</select>' +
                '</div><div class="row col mb-1">' +
                '<select class="form-control" id="severity">' +
                '<option value="">Choose severity' +
                '<option value="mild">Mild (+3 karma)' +
                '<option value="moderate">Moderate (+8 karma)' +
                '<option value="severe">Severe (+13 karma)' +
                '<option value="extreme">Extreme (+18 karma)' +
                '</select>' +
                '</div><div class="row col mb-1">' +
                '<input class="form-control" id="allergy" ' +
                'placeholder="Allergy" type="text">' +
                '</div>' +
                '<div class="row col">' +
                '<button class="btn btn-success" disabled id="add-allergy" ' +
                'type="button">' +
                '<span class="bi bi-plus"></span> Add allergy</button>&nbsp;' +
                '<button class="btn btn-secondary" type="button">Cancel' +
                '</button>' +
                '</div>'
            );
            $('#rarity')
                .on('change', updateAllergyExamples)
                .on('change', checkAllergyValidity);
            $('#severity').on('change', checkAllergyValidity);
            $('#allergy').on('change', checkAllergyValidity);
            $('#add-allergy').on('click', addAllergy);
        } else if ('Addiction' === quality[0].name
                || 'Dry Addict' === quality[0].name) {
            $('#quality-add').html(
                '<div class="row col col-form-label">Choose addiction:</div>' +
                '<div class="row col mb-1">' +
                '<select class="form-control" id="severity">' +
                '<option value="">Choose severity</option>' +
                '<option value="mild">Mild  (+4 karma)</option>' +
                '<option value="moderate">Moderate (+9 karma)</option>' +
                '<option value="severe">Severe (+20 karma)</option>' +
                '<option value="burnout">Burnout (+25 karma)</option>' +
                '</select></div>' +
                '<div class="row col mb-1">' +
                '<input class="form-control" id="addiction" ' +
                'placeholder="Addiction" list="addictions" type="text"></div>' +
                '<div class="row col">' +
                '<button class="btn btn-success" disabled data-name="' +
                quality[0].name + '" ' +
                'type="button"><span class="bi bi-plus"></span> ' +
                'Add addiction</button>&nbsp;' +
                '<button class="btn btn-secondary" type="button">Cancel' +
                '</button>' +
                '</div>'
            );
            $('#severity').on('change', checkAddictionValidity);
            $('#addiction').on('change', checkAddictionValidity);
            $('#quality-add .btn-success').on('click', addAddiction);
        } else if (quality[0].skill) {
            html = '<div class="row col col-form-label">Choose skill:' +
                '</div>' +
                '<div class="row col mb-1">' +
                '<select class="form-control" id="choose-quality">' +
                '<option value="">Choose skill';
            $.each(quality, function (unused, skill) {
                html += '<option value="' + skill.id + '">' + skill.skill;
            });
            html += '</select></div>' +
                '<div class="row col">' +
                '<button class="btn btn-success" disabled type="button">' +
                '<span class="bi bi-plus"></span> Add ' + quality[0].name +
                '</button>&nbsp;' +
                '<button class="btn btn-secondary" type="buttoN">Cancel' +
                '</button></div>';
            $('#quality-add').html(html);
            $('#choose-quality').on('change', checkDropdownValidity);
            $('#quality-add .btn-success').on('click', addQualityWithDropdown);
        } else if ('Indomitable' === quality[0].name) {
            $('#quality-add').html(
                '<div class="row col col-form-label">Configure quality:</div>' +
                '<div class="row col mb-1">' +
                '<select class="form-control" id="indomitable-level">' +
                '<option value="">Choose level</option>' +
                '<option value="1">1 (-8 karma)</option>' +
                '<option value="2">2 (-16 karma)</option>' +
                '<option value="3">3 (-24 karma)</option>' +
                '</select></div>' +
                '<div class="row col mb-1" style="display: none">' +
                '<select class="form-control limit" id="limit-0">' +
                '<option value="">Choose limit</option>' +
                '<option value="physical">Physical</option>' +
                '<option value="mental">Mental</option>' +
                '<option value="social">Social</option>' +
                '</select></div>' +
                '<div class="row col mb-1" style="display: none">' +
                '<select class="form-control limit" id="limit-1">' +
                '<option value="">Choose limit</option>' +
                '<option value="physical">Physical</option>' +
                '<option value="mental">Mental</option>' +
                '<option value="social">Social</option>' +
                '</select></div>' +
                '<div class="row col mb-1" style="display: none">' +
                '<select class="form-control limit" id="limit-2">' +
                '<option value="">Choose limit</option>' +
                '<option value="physical">Physical</option>' +
                '<option value="mental">Mental</option>' +
                '<option value="social">Social</option>' +
                '</select></div>' +
                '<div class="row col">' +
                '<button class="btn btn-success" disabled ' +
                'id="add-indomitable" type="button">' +
                '<span class="bi bi-plus"></span> Add quality</button>&nbsp;' +
                '<button class="btn btn-secondary" type="button">Cancel' +
                '</button>' +
                '</div>'
            );
            $('#indomitable-level').on('change', handleIndomitableLevelChange);
            $('#quality-add .btn-success').on('click', addIndomitable);
            $('#quality-add .limit').on('change', validateIndomitable);
        } else if (quality[0].attribute) {
            html = '<label class="row col col-label-form" ' +
                'for="choose-quality">Choose quality:</label>' +
                '<div class="row col mb-1">' +
                '<select class="form-control" id="choose-quality">' +
                '<option value="">Choose attribute';
            $.each(quality, function (unused, qualityAttribute) {
                html += '<option value="' + qualityAttribute.id + '">' +
                    qualityAttribute.attribute;
            });
            html += '</select></div>' +
                '<div class="row col">' +
                '<button class="btn btn-success" type="button">' +
                '<span class="bi bi-plus"></span> Add quality</button>&nbsp;'
                '<button class="btn btn-secondary" type="button>Cancel'
                '</button></div>';
            $('#quality-add').html(html);
            $('#quality-add .btn-success').on('click', addQualityWithDropdown);
        } else if (quality[0].level) {
            html = '<label class="row col col-form-label"' +
                'for="choose-quality">Choose quality:</label>' +
                '<div class="row col mb-1">' +
                '<select class="form-control" id="choose-quality">' +
                '<option value="">Choose level';
            $.each(quality, function (unused, level) {
                html += '<option value="' + level.id + '">Level ' +
                    level.level + ' (' + level.karma + ' karma)';
            });
            html += '</select></div>' +
                '<div class="row col">' +
                '<button class="btn btn-success" type="button">' +
                '<span class="bi bi-plus"></span> Add quality</button>&nbsp;'
                '<button class="btn btn-secondary" type="button>Cancel'
                '</button></div>';
            $('#quality-add').html(html);
            $('#quality-add .btn-success').on('click', addQualityWithDropdown);
        } else if (quality[0].severity) {
            html = '<label class="row col col-form-label" ' +
                'for="choose-quality">Choose quality:</label>' +
                '<div class="row col mb-1">' +
                '<select class="form-control" id="choose-quality">' +
                '<option value="">Choose';
            $.each(quality, function (unused, severity) {
                html += '<option value="' + severity.id + '">' +
                    severity.severity[0].toUpperCase() +
                    severity.severity.slice(1) + ' (' +
                    severity.karma + ' karma)';
            });
            html += '</select></div>' +
                '<div class="row col">' +
                '<button class="btn btn-success" type="button">' +
                '<span class="bi bi-plus"></span> Add quality</button>&nbsp;'
                '<button class="btn btn-secondary" type="button>Cancel'
                '</button></div>';
            $('#quality-add').html(html);
            $('#quality-add .btn-success').on('click', addQualityWithDropdown);
        } else if ('Adept Way' === quality[0].name) {
            $('#quality-description').html('Choose a path from the dropdown');
            html = '<label class="row col col-form" for="choose-quality">' +
                'Choose quality:</label>' +
                '<div class="row col">' +
                '<select class="form-control" id="choose-quality">' +
                '<option data-description="Choose a path from the dropdown." ' +
                'value="">Choose path';
            $.each(quality, function (unused, path) {
                html += '<option data-description="' + path.description + '" ' +
                    'value="' + path.id + '">' + path.path + ' (' +
                    path.karma + ' karma)';
            });
            html += '</select></div>';
            $('#quality-add').html(html);
            $('#choose-quality').on('change', updateAdeptWay);
        } else {
            html = '<p>Add quality:</p>' +
                '<ul>';
            $.each(quality, function (unused, level) {
                html += '<li><a href="#" data-id="' + level.id + '">Add ' +
                    level.name + '</a> (' + level.karma + ' karma)</li>';
            });
            html += '</ul>';
            $('#quality-add').html(html);
        }
        valid = valid && !$('#quality-add .btn-success').prop('disabled');
        $('#quality-add .btn-success').prop('disabled', !valid);
    }

    /**
     * Determine whether a given quality is valid for the character.
     * @param {Object} quality Quality to test for appropriateness
     * @return {boolean} True if the quality is valid for the character
     */
    function isQualityValid(quality) {
        const validations = getValidationRules(quality);
        if (!validations.length) {
            return true;
        }
        for (let i = 0, c = validations.length; i < c; i++) {
            if (validations[i].test == false) {
                return false;
            }
        }
        return true;
    }

    /**
     * Handle the response from the server for qualities.
     * @param {Object} data Data from the server
     */
    function processQualities(data) {
        data = data.data;
        $.each(data, function (unused, value) {
            if (qualities[value.name]) {
                qualities[value.name].push(value);
            } else {
                qualities[value.name] = [value];
            }
        });
        let html = '';
        $.each(qualities, function (index, value) {
            if (!value[0].ruleset) {
                value[0].ruleset = 'core';
            }
            // Filter out rulebooks that aren't enabled for the character.
            if (typeof rulebooks[value[0].ruleset] === 'undefined') {
                return;
            }
            let costs = [];
            $.each(value, function (unused, quality) {
                costs.push(quality.karma);
            });
            costs = $.unique(costs);
            costs = costs.join(', ');
            let qualityClass = 'negative';
            if (value[0].karma < 0) {
                qualityClass = 'positive';
            }
            html += '<tr data-id="' + index + '">' +
                '<td>' + index + '</td>' +
                '<td>' + costs + '</td>' +
                '<td>' + value[0].ruleset + '</td>' +
                '<td>' + qualityClass + '</td>' +
                '</tr>';
        });
        $('#quality-list tbody').append(html);
        const table = $('#quality-list').DataTable({
            columns: [
                {}, // name
                {orderable: false}, // karma
                {visible: false}, // ruleset
                {visible: false} // class
            ],
            info: false
        });
        $('#filter-class').on('change', function () {
            table.columns(3).search(this.value).draw();
        });
        $('#filter-ruleset').on('change', function () {
            table.columns(2).search(this.value).draw();
        });
        $('#search-qualities').on('keyup', function () {
            table.search(this.value).draw();
        });
        $('#quality-list_length').remove();
        $('#quality-list_filter').remove();
        updateListValidity();
    }

    /**
     * User wants to remove a quality from a character.
     * @param {!Event} e Event that fired this handler
     */
    function removeQuality(e) {
        let el = $(e.target);
        if ('SPAN' === el[0].nodeName) {
            el = el.parent();
        }

        const id = el.data('id');
        for (let i = 0, c = character.qualities.length; i < c; i++) {
            if (character.qualities[i].id == id) {
                character.qualities.splice(i, 1);
                break;
            }
        }
        $(e.target).parents('li').remove();

        points = new Points(character);
        updatePointsToSpendDisplay(points);

        if (0 === character.qualities.length) {
            $('#no-qualities').show();
        }
    }

    /**
     * User either clicked a cancel button in the add quality flow, or added a
     * quality.
     */
    function resetAddQualityModal() {
        $('#click-panel').show();
        $('#info-panel').hide();
        $('#search-qualities').focus();
    }

    /**
     * Update the description for an Adept's Way quality.
     * @param {!Event} e Event that fired the handler
     */
    function updateAdeptWay(e) {
        let description = $(e.target.selectedOptions[0]).data('description');
        description = cleanDescription(description);
        $('#quality-description').html(description);
    }

    /**
     * Update the example allergies when the rarity is changed.
     */
    function updateAllergyExamples() {
        const rarity = $('#rarity').val();

        if ('uncommon' !== rarity && 'common' !== rarity) {
            $('#allergy').removeAttr('list');
        } else {
            $('#allergy').attr('list', 'allergy-' + rarity);
        }
    }

    /**
     * Update the currently displayed quality list for validity.
     */
    function updateListValidity() {
        const list = $('#quality-list tbody tr');
        $.each(list, function (unused, row) {
            let rowEl = $(row);
            if (rowEl.children().hasClass('dataTables_empty')) {
                return;
            }
            let quality = qualities[rowEl.data('id')];
            rowEl.toggleClass('invalid', !isQualityValid(quality[0]));
        });
    }

    /**
     * Validate whether the add quality button should be enabled.
     */
    function validateIndomitable() {
        const level = $('#indomitable-level').val();
        const limit0 = $('#limit-0').val();
        const limit1 = $('#limit-1').val();
        const limit2 = $('#limit-2').val();
        let valid = true;

        // Intentionally fall through on each.
        switch (level) {
            case '3':
                if ('' === limit2) {
                    valid = false;
                    break;
                }
            case '2':
                if ('' === limit1) {
                    valid = false;
                    break;
                }
            case '1':
                if ('' === limit0) {
                    valid = false;
                }
                break;
            case '':
                valid = false;
                break;
        }

        $('#quality-add .btn-success').prop('disabled', !valid);
    }

    $.ajax('/api/shadowrun5e/qualities').done(processQualities);
    $('#quality-list tbody').on('click', 'tr', handleQualityClick);
    $('#qualities').on('click', '.btn-danger', removeQuality);
    $('body').tooltip({selector: '[data-bs-toggle="tooltip"]'});

    let points = new Points(character);

    let modal = $('#quality-modal');
    // Close the modal when escape is pressed.
    $(document).on('keydown', function (e) {
        if (27 === e.which) {
            modal.modal('hide');
        }
    });
    // Autofocus on the search box when the modal is shown.
    modal.on('shown.bs.modal', function () {
        $('#search-qualities')
            .val('') // Remove previous search
            .focus() // Put the cursor there so if the user types it searches
            .trigger('keyup'); // Refilter if anything changed.
    });
});
