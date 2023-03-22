/**
 * Given a string description, replace double pipes with paragraph breaks.
 * @param {string} description Description to clean
 * @return {!string}
 */
function cleanDescription(description) {
    return description.replace(/\|\|/g, '</p><p>');
}

/**
 * Encode a string for display in HTML.
 * @param {!string} value String to encode
 * @return {!string}
 */
function htmlEncode(value) {
    return $('<div/>').text(value).html();
}

/**
 * Return whether a given object is empty (has no properties).
 * @param {Object} object
 * @return {bool}
 */
function isEmpty(object) {
    for (let key in object) {
        if (object.hasOwnProperty(key)) {
            return false;
        }
    }
    return true;
}

/**
 * Uppercase the first letter of a string.
 * @param {string} string String to uppercase
 * @return {string}
 */
function ucfirst(string) {
    if (!string) {
        return string;
    }
    return string[0].toUpperCase() + string.slice(1);
}

/**
 * Change the appearance of the points display depending on the amount
 * remaining.
 * @param {!Number} points Number of whatever is left
 * @param {Element} el HTML element to update
 * @param {?Function} formatter Formatted to use
 */
function setPointsDisplay(points, el, formatter) {
    if (0 === points || (el[0].id == 'karma' && points <= 7) ||
        'None' == points || (0 === parseInt(points, 10) && 'string' === typeof points)) {
        el.parents('tr')
            .removeClass('alert-warning')
            .removeClass('alert-danger');
    } else if ('string' === typeof points && 0 < parseInt(points, 10)) {
        el.parents('tr')
            .addClass('alert-warning')
            .removeClass('alert-danger')
            .removeClass('alert-success');
    } else if (0 < points) {
        el.parents('tr')
            .addClass('alert-warning')
            .removeClass('alert-danger')
            .removeClass('alert-success');
    } else {
        el.parents('tr')
            .addClass('alert-danger')
            .removeClass('alert-success')
            .removeClass('alert-warning');
    }
    if (formatter) {
        el.html(formatter(points));
    } else {
        el.html(points);
    }
}

/**
 * Calculate the karma cost for having too much skill.
 * @param {array} skills Skills ordered by level ascending
 * @param {array} specializations Skills with specializations
 * @param {Number} skillCost Cost per skill rank
 * @param {Number} deficit Number of skill points to charge karma for
 * @return {Object} Map of message and karmaSpent
 */
function calculateKarmaForTooManySkills(skills, specializations, skillCost, deficit) {
    window.console.log(skills);
    let karmaSpent = 0;
    let message = [];
    let skill;
    for (let i = 0, c = skills.length; i < c; i++) {
        skill = skills[i];
        if (skill.level * 2 > 7 && specializations.length) {
            // The next highest skill level costs more than adding a
            // specialization to a skill, so charge the specialization cost
            // next.
            karmaSpent += 7;
            skill = specializations.pop();
            deficit++;
            message.push(
                '+7₭ for ' + skill.specialization + ' to ' + skill.name
            );
            // But don't advance past the current skill since it's still the
            // cheapest non-specialization option.
            i--;
            continue;
        }
        karmaSpent += skill.level * skillCost;
        message.push('+' + (skill.level * skillCost) + '₭ for ' + skill.name +
            ' to rank ' + skill.level);
        deficit++;
        if (0 === deficit) {
            // We've already charged enough karma, bail out.
            break;
        }
        skill.level--;
        if (!skill.level) {
            delete skills[i];
            continue;
        }
        // We haven't exhausted the current skill of its points, and it is
        // even cheaper now than it was before, so don't move past it.
        i--;
    }
    return {
        message: message,
        karmaSpent: karmaSpent
    };
}

/**
 * Update the points to spend display.
 * @param {Object} pointsToSpend Map of points that can be spent
 */
function updatePointsToSpendDisplay(pointsToSpend) {
    const compare = function (a, b) {
        if (a.level > b.level) {
            return 1;
        }
        if (a.level < b.level) {
            return -1;
        }
        return 0;
    };
    let karmaSpent;
    let results;
    let skills;
    let skill;
    let specializations;

    setPointsDisplay(pointsToSpend.attributes, $('#attribute-points'));
    setPointsDisplay(pointsToSpend.special, $('#special-points'));

    if (pointsToSpend.activeSkills < 0) {
        // The user has spent too many points on active skills. Charge them
        // karma to make up the difference.
        skills = character.skills;
        specializations = skills.filter(skill => skill.specialization);
        skills.sort(compare);
        results = calculateKarmaForTooManySkills(
            skills,
            specializations,
            2,
            pointsToSpend.activeSkills
        );
        pointsToSpend.activeSkills = 0;

        setPointsDisplay(
            '<span class="tooltip-anchor" data-bs-placement="left" ' +
            'data-bs-toggle="tooltip" title="' + results.message.join(', ') +
            '">0</span>',
            $('#active-skills')
        );
        pointsToSpend.karma -= results.karmaSpent;
    } else {
        setPointsDisplay(pointsToSpend.activeSkills, $('#active-skills'));
    }

    if (pointsToSpend.knowledgeSkills < 0) {
        // The user has spent too many points on knowledge skills. Charge them
        // karma to make up the difference.
        skills = character.knowledgeSkills;
        specializations = skills.filter(skill => skill.specialization);
        skills.sort(compare);
        results = calculateKarmaForTooManySkills(
            skills,
            specializations,
            1,
            pointsToSpend.knowledgeSkills
        );
        pointsToSpend.knowledgeSkills = 0;

        setPointsDisplay(
            '<span class="tooltip-anchor" data-bs-placement="left" ' +
            'data-bs-toggle="tooltip" title="' + results.message.join(', ') +
            '">0</span>',
            $('#knowledge-skills')
        );
        pointsToSpend.karma -= results.karmaSpent;
    } else {
        setPointsDisplay(pointsToSpend.knowledgeSkills, $('#knowledge-skills'));
    }

    if (pointsToSpend.skillGroups < 0) {
        karmaSpent = 0;
        for (let i = pointsToSpend.skillGroups; i; i++) {
            karmaSpent += i * 5;
        }
        setPointsDisplay(
            '<span class="tooltip-anchor" data-bs-placement="left" ' +
            'data-bs-toggle="tooltip" title="' + (-1 * karmaSpent) +
            '₭ spent">0</span>',
            $('#skill-groups')
        );
    } else {
        setPointsDisplay(pointsToSpend.skillGroups, $('#skill-groups'));
    }

    window.console.log(pointsToSpend.resources);
    if (pointsToSpend.resources < 0) {
        const resources = pointsToSpend.resources +
            Math.floor(pointsToSpend.resources / 2000) * -2000;
        setPointsDisplay(
            '<span class="tooltip-anchor" data-bs-placement="left" ' +
            'data-bs-toggle="tooltip" title="' +
            Math.floor(pointsToSpend.resources / 2000) * -1 +
            '₭ spent">' + nuyen.format(resources) + '</span>',
            $('#resources')
        );
    } else {
        setPointsDisplay(
            pointsToSpend.resources,
            $('#resources'),
            nuyen.format
        );
    }

    if (pointsToSpend.contacts < 0) {
        setPointsDisplay(
            '<span class="tooltip-anchor" data-bs-placement="left" ' +
            'data-bs-toggle="tooltip" title="' + (pointsToSpend.contacts * -1) +
            '₭ spent">0</span>',
            $('#contacts')
        );
    } else {
        setPointsDisplay(pointsToSpend.contacts, $('#contacts'));
    }
    if (pointsToSpend.magics < 0) {
        var magic = sr.loadMagic(sr.store);
        if (magic.spells && magic.spells.length) {
            pointsToSpend.karma += pointsToSpend.magics * 5;
            setPointsDisplay(
                '<span class="tooltip-anchor" data-bs-placement="left" ' +
                'data-bs-toggle="tooltip" title="' + (pointsToSpend.magics * -1) +
                ' extra spells for ' + (pointsToSpend.magics * -5) +
                '₭ spent">0</span>',
                $('#magics')
            );
        }
    } else {
        setPointsDisplay(pointsToSpend.magics, $('#magics'));
    }
    setPointsDisplay(pointsToSpend.magicSkills, $('#magic-skills'));
    if (pointsToSpend.forms < 0) {
        pointsToSpend.karma += pointsToSpend.forms * 4;
        setPointsDisplay(
            '<span class="tooltip-anchor" data-bs-placement="left" ' +
            'data-bs-toggle="tooltip" title="' + (pointsToSpend.forms * -1) +
            ' extra complex forms for ' + (pointsToSpend.forms * -4) +
            '₭ spent">0</span>',
            $('#forms')
        );
    } else {
        setPointsDisplay(pointsToSpend.forms, $('#forms'));
    }
    setPointsDisplay(pointsToSpend.resonanceSkills, $('#resonance-skills'));
    setPointsDisplay(pointsToSpend.karma, $('#karma'), karma.format);
}

/**
 * Formatter to format a number with commas and the Yen symbol.
 */
const nuyen = new Intl.NumberFormat('en-US', {
    style: 'currency',
    currency: 'JPY',
    minimumFractionDigits: 0
});

let karma = {};
karma.format = function (a) {
    return '₭' + a;
};

$(function () {
    $('#points').on('hide.bs.offcanvas', function (event) {
        event.preventDefault();
        return false;
    });
    $('#points button').on('click', function (event) {
        $('#points').hide();
    });
    $('#points-button').on('click', function (event) {
        event.preventDefault();
        $('#points').show();
    });

    let points = new Points(character);
    window.console.log('Running update');
    updatePointsToSpendDisplay(points);
});
