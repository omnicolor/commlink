$(function () {
    'use strict';

    /**
     * Collection of priority select dropdowns.
     * @type {Object}
     */
    let $priorityElements = $('select.priority');

    /**
     * Array of possible priority values.
     * @type {Array}
     */
    const priorityValues = [
        'metatype',
        'attributes',
        'magic',
        'skills',
        'resources'
    ];

    /**
     * Array of available races at each priority level.
     * @type {Object}
     */
    const races = {
        a: {
            human: 'Human (9)',
            elf: 'Elf (8)',
            dwarf: 'Dwarf (7)',
            ork: 'Ork (7)',
            troll: 'Troll (5)'
        },
        b: {
            human: 'Human (7)',
            elf: 'Elf (6)',
            dwarf: 'Dwarf (4)',
            ork: 'Ork (4)',
            troll: 'Troll (0)'
        },
        c: {
            human: 'Human (5)',
            elf: 'Elf (3)',
            dwarf: 'Dwarf (1)',
            ork: 'Ork (0)'
        },
        d: {
            human: 'Human (3)',
            elf: 'Elf (0)'
        },
        e: {
            human: 'Human (1)'
        }
    };

    /**
     * Collection of magics at each level.
     * @type {Object}
     */
    const magics = {
        a: {
            magician: {
                name: 'Magician',
                description: 'Magic 6, two rating 5 magical skills, ten spells'
            },
            mystic: {
                name: 'Mystic adept',
                description: 'Magic 6, two rating 5 magical skills, ten spells'
            },
            technomancer: {
                name: 'Technomancer',
                description: 'Resonance 6, two rating 5 resonance skills, ' +
                    'five complex forms'
            }
        },
        b: {
            magician: {
                name: 'Magician',
                description: 'Magic 4, two rating 4 magical skills, seven ' +
                    'spells'
            },
            mystic: {
                name: 'Mystic adept',
                description: 'Magic 4, two rating 4 magical skills, seven ' +
                    'spells'
            },
            technomancer: {
                name: 'Technomancer',
                description: 'Resonance 4, two rating 4 resonance skills, ' +
                    'two complex forms'
            },
            adept: {
                name: 'Adept',
                description: 'Magic 6, one rating 4 active skill'
            },
            aspected: {
                name: 'Aspected magician',
                description: 'Magic 5, one rating 4 magical skill group'
            }
        },
        c: {
            magician: {
                name: 'Magician',
                description: 'Magic 3, five spells'
            },
            mystic: {
                name: 'Mystic adept',
                description: 'Magic 3, five spells'
            },
            technomancer: {
                name: 'Technomancer',
                description: 'Resonance 3, one complex form'
            },
            adept: {
                name: 'Adept',
                description: 'Magic 4, one rating 2 active skill'
            },
            aspected: {
                name: 'Aspected magician',
                description: 'Magic 3, one rating 2 magical skill group'
            }
        },
        d: {
            adept: {
                name: 'Adept',
                description: 'Magic 2'
            },
            aspected: {
                name: 'Aspected magician',
                description: 'Magic 2'
            }
        }
    };

    /**
     * The user chose a priority.
     * @param {Event} e
     */
    var changePriority = function (e) {
        /**
         * Array of priorities used at a higher level.
         * @type {Array}
         */
        let used = [];

        /**
         * Priority level the user changed.
         * @type {string}
         */
        const chosenPriority = e.target.id.slice(-1);

        /**
         * Element with the extra information for a priority level.
         * @type {Object}
         */
        const $extraInfoElement = $('#extra-' + chosenPriority);

        if ('metatype' === e.target.value) {
            /**
             * HTML for the race selector.
             * @type {string}
             */
            let raceSelect = '<select class="form-control" id="metatype" ' +
                'name="metatype">' + '<option>&hellip;</option>';
            $.each(races[chosenPriority], function (index, race) {
                raceSelect += '<option value="' + index + '">' + race +
                    '</option>';
            });
            $extraInfoElement.html(raceSelect + '</select>');
            $('#metatype').on('change', updatePoints);
        } else if ('magic' === e.target.value && 'e' !== chosenPriority) {
            /**
             * HTML for the magic selector.
             * @type {string}
             */
            let magicSelect = '<select class="form-control" id="magic" ' +
                'name="magic">' + '<option>&hellip;</option>';
            $.each(magics[chosenPriority], function (index, magic) {
                magicSelect += '<option value="' + index + '" title="' +
                    magic.description + '">' + magic.name + '</option>';
            });
            $extraInfoElement.html(magicSelect + '</select>');
            $('#magic').on('change', updatePoints);
        } else {
            $extraInfoElement.html('');
        }

        $.each($priorityElements, function (unused, prioritySelectElement) {
            if (prioritySelectElement.value) {
                // User has selected a priority at this level
                if (-1 !== used.indexOf(prioritySelectElement.value)) {
                    // But a higher priority has already claimed it. Unselect
                    // the dropdown and remove the extra info field.
                    prioritySelectElement.selectedIndex = 0;
                    $('#extra-' + prioritySelectElement.id.slice(-1)).html('');
                }
                // Mark the priority as already used
                used.push(prioritySelectElement.value);
                return;
            }

            if (e.target !== prioritySelectElement) {
                // We're looking at a different (and lower) priority than was
                // changed. Mark already used priority levels as disabled and
                // unused as not disabled.
                $.each(priorityValues, function (index, priorityValue) {
                    if (-1 !== used.indexOf(priorityValue)) {
                        prioritySelectElement[index + 1].disabled = true;
                    } else {
                        prioritySelectElement[index + 1].disabled = false;
                    }
                });
            }
        });

        updatePoints();
    };

    /**
     * Update the points display.
     */
    var updatePoints = function () {
        character.priorities.a = $('#priority-a')[0].value;
        character.priorities.b = $('#priority-b')[0].value;
        character.priorities.c = $('#priority-c')[0].value;
        character.priorities.d = $('#priority-d')[0].value;
        character.priorities.e = $('#priority-e')[0].value;
        if ($('#magic').length) {
            character.priorities.magic = $('#magic').val();
        } else {
            character.priorities.magic = null;
        }

        if ($('#metatype').length) {
            character.priorities.metatype = $('#metatype').val();
        } else {
            character.priorities.metatype = 'human';
        }

        points = new Points(character);
        updatePointsToSpendDisplay(points);

        if (
            'magician' === character.priorities.magic
            || 'adept' === character.priorities.magic
        ) {
            $('.magic-noshow').hide();
            $('.magic-show').show();
        } else {
            $('.adept-show').hide();
            $('.magic-show').hide();
            $('.magic-noshow').show();
        }
        if ('technomancer' === character.priorities.magic) {
            $('.resonance-show').show();
            $('.resonance-noshow').hide();
        } else {
            $('.resonance-show').hide();
            $('.resonance-noshow').show();
        }
    };

    let points = new Points(character);
    updatePointsToSpendDisplay(points);

    $('[data-bs-toggle="tooltip"]').tooltip();
    $priorityElements.on('change', changePriority);
});
