$(function () {
    'use strict';

    let points = new Points(character);

    $('[name="gameplay"]').on('change', function (event) {
        character.priorities.gameplay = $(event.target).val();
        points = new Points(character);
        updatePointsToSpendDisplay(points);
    });
    $('#rulebook-run-faster').on('change', function (event) {
        $('#system-ten').prop('disabled', false === $(event.target).prop('checked'));
        /*
        points = new Points(character);
        updatePointsToSpendDisplay(points);
        */
    });
    $('[data-bs-toggle="tooltip"]').tooltip();
});
