$(function () {
    'use strict';

    /**
     * List of standard attributes.
     * @type {Array}
     */
    const attributes = [
        'body',
        'agility',
        'reaction',
        'strength',
        'willpower',
        'logic',
        'intuition',
        'charisma'
    ];

    /**
     * List of special attributes.
     * @type {Array}
     */
    const specialAttributes = ['edge', 'magic', 'resonance'];

    /**
     * Hash map of races and their modified minimum and maximum attributes.
     * @type {Object}
     */
    const raceLimits = {
        dwarf: {
            body: { max: 8, min: 3 },
            reaction: { max: 5, min: 1 },
            strength: { max: 8, min: 3 },
            willpower: { max: 7, min: 2 }
        },
        elf: {
            agility: { max: 7, min: 2 },
            charisma: { max: 8, min: 3 }
        },
        human: {
            edge: { max: 7, min: 2 }
        },
        ork: {
            body: { max: 9, min: 4 },
            strength: { max: 8, min: 3 },
            logic: { max: 5, min: 1 },
            charisma: { max: 5, min: 1 }
        },
        troll: {
            body: { max: 10, min: 5 },
            agility: { max: 5, min: 1 },
            strength: { max: 10, min: 5 },
            logic: { max: 5, min: 1 },
            intuition: { max: 5, min: 1 },
            charisma: { max: 4, min: 1 }
        }
    };

    /**
     * Check to make sure that the character doesn't have too many attributes at
     * their maximum value.
     */
     function checkTooManyMaximums() {
        let maxes = 0;
        $.each(attributes, function (unused, attribute) {
            const attributeEl = $('#' + attribute);
            const value = parseInt(attributeEl.val(), 10);
            if (value >= attributeEl.attr('max')) {
                maxes++;
                attributeEl.addClass('is-invalid');
            } else {
                attributeEl.removeClass('is-invalid');
            }
        });
        if (1 >= maxes) {
            $('.is-invalid').removeClass('is-invalid');
        }
    }

    /**
     * Handle the user changing an attribute's value.
     * @param {Event} e Event that fired this handler
     */
    function handleAttributeChange(e) {
        const attribute = e.target.id;
        const el = $('#' + attribute);
        let value = parseInt(el.val(), 10);

        // Make sure the value changed to is within bounds.
        if (value >= el.attr('max')) {
            value = el.attr('max');
        } else if (value < el.attr('min')) {
            value = el.attr('min');
        }
        el.val(value);
        character[attribute] = value;
        points = new Points(character);
        updatePointsToSpendDisplay(points);
    }

    /**
     * Handle the user changing a special attribute's value.
     * @param {Event} e Event that fired this handler
     */
    function handleSpecialAttributeChange(e) {
        const attribute = e.target.id;
        const el = $('#' + attribute);
        let value = parseInt(el.val(), 10);

        if (isNaN(value)) {
            value = el.attr('min');
        } else if (value > el.attr('max')) {
            value = el.attr('max');
        } else if (value < el.attr('min')) {
            value = el.attr('min');
        }
        el.val(value);
        character[attribute] = value;
        points = new Points(character);
        updatePointsToSpendDisplay(points);
    }

    /**
     * Set the minimum and maximums for attributes based on metatype.
     * @param {Object} limits Racial limit modifiers
     */
    function setAttributeLimits(limits) {
        let $attributeEl;
        let combined = attributes.concat(specialAttributes);

        $.each(combined, function (unused, attribute) {
            let min = 1;
            let max = 6;
            if (!limits) {
                return;
            }
            $attributeEl = $('#' + attribute);
            if (limits[attribute]) {
                // Metatype has a different minimum and/or maximum for the
                // given attribute.
                min = limits[attribute].min;
                max = limits[attribute].max;
            }

            $.each(character.qualitites, function (unused, quality) {
                if (!quality.effects) {
                    // Quality has no effects.
                    return;
                }

                if (!quality.effects['maximum-' + attribute]) {
                    // Quality has effects, but doesn't affect this
                    // attribute.
                    return;
                }

                max += quality.effects['maximum-' + attribute];
            });

            $attributeEl.parent().next().html(min + '/' + max);
            $attributeEl
                .attr('min', min)
                .attr('max', max);
        });
    }

    /**
     * Set up change handlers for all of the attribute inputs.
     * @param {number} unused
     * @param {string} attribute Name of the attribute
     */
    function setupAttributeHandler(unused, attribute) {
        $('#' + attribute)
            .on('change', handleAttributeChange)
            .on('keyup', handleAttributeChange)
            .on('change', checkTooManyMaximums)
            .on('keyup', checkTooManyMaximums);
    };

    /**
     * Set up change handlers for all of the special attribute inputs.
     * @param {number} unused
     * @param {string} attribute Name of the attribute
     */
    function setupSpecialHandler(unused, attribute) {
        const el = $('#' + attribute);
        if (!el) {
            return;
        }
        el.on('change', handleSpecialAttributeChange);
    }

    let points = new Points(character);
    setAttributeLimits(raceLimits[character.priorities.metatype]);
    $('[data-bs-toggle="tooltip"]').tooltip();
    $.each(attributes, setupAttributeHandler);
    $.each(specialAttributes, setupSpecialHandler);
});
