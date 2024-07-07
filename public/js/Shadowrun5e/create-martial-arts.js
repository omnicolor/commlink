$(function () {
    'use strict';

    /**
     * List of martial arts styles.
     */
    let styles = {};

    /**
     * List of martial arts techniques.
     */
    let techniques = {};

    /**
     * User clicked the button to add a style to their character.
     * @param {!Event} e
     */
    function addStyle(e) {
        let el = $(e.target);
        if ('SPAN' === el[0].nodeName) {
            el = el.parent();
        }
        const id = el.data('id');
        character.martialArts.styles.push(id);
        const style = styles[id];
        let html = '<li class="list-group-item style">';
        if (trusted) {
            html += '<span data-bs-placement="right" data-bs-toggle="tooltip" '
                + 'title="' + style.description.replace(/\|\|/g, '\n\n') + '">';
        } else {
            html += '<span>';
        }
        html += style.name +
            '</span><div class="float-end">' +
            '<button class="btn btn-danger btn-sm" role="button">' +
            '<span aria-hidden="true" class="bi bi-dash"></span> ' +
            'Remove</button>' +
            '</div>' +
            '<input name="style" type="hidden" value="' + id + '">' +
            '</li>';
        $(html).insertBefore($('.no-styles').first());
        $('.no-styles').hide();
        $('#martial-art-technique-div').show();
        resetModals();
        filterTechniques();
        points = new Points(character);
        updatePointsToSpendDisplay(points);
    }

    /**
     * User clicked the button to add a technique to their character.
     * @param {!Event} e
     */
    function addTechnique(e) {
        const id = $(e.target).data('id');
        if (!id) {
            return;
        }
        const technique = techniques[id];
        character.martialArts.techniques.push(id);
        let html = '<li class="technique list-group-item">';
        if (trusted) {
            html += '<span class="tooltip-anchor" data-html="true" ' +
                'data-placement="right" data-toggle="tooltip" ' +
                'title="' + technique.description.replace(/\|\|/g, '\n\n') + '">';
        } else {
            html += '<span>';
        }
        html += technique.name;
        if (technique.subname) {
            html += ' (' + technique.subname + ')';
        }
        html += '</span><div class="float-end">' +
            '<button class="btn btn-danger btn-sm" data-id="' + id + '" ' +
            'type="button">' +
            '<span aria-hidden="true" class="bi bi-dash"></span> ' +
            'Remove</button></div>' +
            '<input name="techniques[]" type="hidden" value="' + id + '">' +
            '</li>';
        $(html).insertBefore($('#no-techniques'));
        $('#no-techniques').hide();
        $('#techniques-click-panel').show();
        $('#techniques-info-panel').hide();
        filterTechniques();
        points = new Points(character);
        updatePointsToSpendDisplay(points);
    }

    /**
     * Filter the techniques list.
     */
    function filterTechniques() {
        const style = styles[character.martialArts.styles[0]];
        if (!style) {
            return;
        }
        const rows = $('#techniques-list td');
        let ownedTechniques = [];
        $.each(character.martialArts.techniques, function (unused, technique) {
            ownedTechniques.push(technique);
        });
        $.each(rows, function (unused, row) {
            row = $(row);
            const id = row.data('id');
            const invalid = (-1 === style.techniques.indexOf(id)
                || -1 !== ownedTechniques.indexOf(id));
            row.parent().toggleClass('invalid', invalid);
        });
    }

    /**
     * Handle the response for martial arts styles from the API.
     * @param {!Object} data
     */
    function processStyles(data) {
        let html = '';
        $.each(data.data, function (unused, style) {
            styles[style.id] = style;
            html += '<tr>' +
                '<td data-id="' + style.id + '">' + style.name + '</td>' +
                '</tr>';
        });
        $('#styles-list tbody').append(html);
        const table = $('#styles-list').DataTable({
            columns: [
                {} // name
            ],
            info: false
        });
        $('#search-styles').on('keyup', function () {
            table.search(this.value).draw();
        });
        $('#styles-list_length').remove();
        $('#styles-list_filter').remove();
    }

    /**
     * Handle the response for martial arts techniques from the API.
     * @param {!Object} data
     */
    function processTechniques(data) {
        let html = '';
        $.each(data.data, function (unused, technique) {
            techniques[technique.id] = technique;
            html += '<tr>' +
                '<td data-id="' + technique.id + '">' + technique.name;
            if (technique.subname) {
                html += ' (' + technique.subname + ')';
            }
            html += '</td>' +
                '</tr>';
        });
        $('#techniques-list tbody').append(html);
        const table = $('#techniques-list').DataTable({
            columns: [
                {} // name
            ],
            info: false
        });
        $('#search-techniques').on('keyup', function () {
            table.search(this.value).draw();
        });
        $('#techniques-list_length').remove();
        $('#techniques-list_filter').remove();
        table.on('draw', filterTechniques);
        filterTechniques();
    }

    /**
     * Remove a martial arts style from the character.
     * @param {!Event} e
     */
    function removeStyle(e) {
        e.preventDefault();

        // Remove the row from the page.
        $('#martial-arts-styles .style').remove();

        // Since there can only be one style at character generation, empty the
        // array.
        character.martialArts.styles = [];

        // Techniques depend on knowing a style, so empty the techniques display
        // on the page as well.
        $('li.technique').remove();

        // Erase the techniques the user chose since they may not be available
        // when/if they choose a new style.
        character.martialArts.techniques = [];

        // Hide the techniques list and show the no styles message.
        $('#martial-art-technique-div').hide();
        $('.no-styles').show();
        $('#no-techniques').show();
        points = new Points(character);
        updatePointsToSpendDisplay(points);
    }

    /**
     * User clicked to remove a technique from the character.
     * @param {!Event} e
     */
    function removeTechnique(e) {
        let el = $(e.target);
        if ('SPAN' === el[0].nodeName) {
            el = el.parent();
        }
        const id = el.data('id');
        el.parents('li').remove();

        for (let i = 0, c = character.martialArts.techniques.length; i < c; i++) {
            if (character.martialArts.techniques[i] === id) {
                character.martialArts.techniques.splice(i, 1);
                break;
            }
        }

        // If that was the last technique, put the no techniques row back.
        if (0 === character.martialArts.techniques.length) {
            $('#no-techniques').show();
        }
        points = new Points(character);
        updatePointsToSpendDisplay(points);
    }

    /**
     * Reset the modals.
     */
    function resetModals() {
        $('#styles-click-panel').show();
        $('#styles-info-panel').hide();
        $('#styles-modal').modal('hide');
        $('#techniques-click-panel').show();
        $('#techniques-info-panel').hide();
        $('#techniques-modal').modal('hide');
    }

    /**
     * User clicked on a martial art style, show them more information about it.
     * @param {!Event} e
     */
    function showStyleInformation(e) {
        const id = $(e.target).data('id');
        const style = styles[id];
        let html = '';

        $('#style-name').html(style.name);
        $('#style-description').html(style.description);
        if (style.page) {
            $('#style-ruleset').html(
                rulebooks[style.ruleset].name + ', p' + style.page
            );
        }
        $('#styles-info-panel .btn-success').data('id', id);
        $.each(style.techniques, function (unused, techniqueId) {
            const technique = techniques[techniqueId];
            if (typeof technique === 'undefined') {
                return;
            }
            html += '<li>' + technique.name;
            if (technique.subname) {
                html += ' (' + technique.subname + ')';
            }
            html += '</li>';
        });
        $('#style-techniques').html(html);
        $('#styles-click-panel').hide();
        $('#styles-info-panel').show();
    }

    /**
     * User clicked on a technique to potentially add to their character.
     * @param {!Event} e
     */
    function showTechniqueInformation(e) {
        const el = $(e.target);
        const invalid = el.parents('tr').hasClass('invalid');
        const id = el.data('id');
        const technique = techniques[id];
        $('#technique-name').html(technique.name);
        $('#technique-description').html(
            cleanDescription(technique.description)
        );
        $('#technique-ruleset').html(
            rulebooks[technique.ruleset].name + ', p' + technique.page
        );
        $('#techniques-click-panel').hide();
        $('#techniques-info-panel').show();
        $('#techniques-info-panel .btn-success')
            .prop('disabled', invalid)
            .data('id', id);
    }

    $.ajax('/api/shadowrun5e/martial-arts-styles').done(processStyles);
    $.ajax('/api/shadowrun5e/martial-arts-techniques').done(processTechniques);

    let points = new Points(character);

    $('#styles-list').on('click', 'td', showStyleInformation);
    $('#styles-info-panel').on('click', '.btn-success', addStyle);
    $('#martial-arts-styles').on('click', '.btn-danger', removeStyle);
    $('#techniques-list').on('click', 'td', showTechniqueInformation);
    $('#techniques-info-panel').on('click', '.btn-success', addTechnique);
    $('#martial-arts-techniques').on('click', '.btn-danger', removeTechnique);

    // Reset the modals when escape is pressed.
    $(document).on('keydown', function (e) {
        if (27 === e.which) {
            resetModals();
        }
    });
    // Autofocus on the search box when the modal is shown.
    $('#styles-modal').on('shown.bs.modal', function () {
        $('#search-styles')
            .val('') // Remove previous search
            .focus() // Put the cursor there so if the user types it searches
            .trigger('keyup'); // Refilter if anything changed.
    });
    $('#techniques-modal').on('shown.bs.modal', function () {
        $('#search-techniques').val('').focus().trigger('keyup');
    });
});
