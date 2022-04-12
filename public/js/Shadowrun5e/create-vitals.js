$(function () {
    'use strict';

    /**
     * Calculate the character's age based on the game's start date and their
     * birthdate.
     */
    function calculateAge() {
        let birthDate = $('#birthdate').val();
        if (!birthDate || typeof campaignStartDate === 'undefined') {
            $('#years-display').hide();
            return;
        }
        birthDate = new Date(birthDate);
        let age = campaignStartDate.getFullYear() - birthDate.getFullYear();
        const m = campaignStartDate.getMonth() - birthDate.getMonth();
        if (m < 0 || (m === 0 && campaignStartDate.getDate() < birthDate.getDate())) {
            age--;
        }
        $('#years').html(age);
        $('#years-display').show();
    }

    /**
     * Calculate the character's height in feet.
     */
    function calculateHeight() {
        const height = parseFloat($('#height').val());
        if (!height) {
            $('#feet').html('0\'0&quot;');
            return;
        }
        let inches = height * 39.3700787402;
        const feet = Math.floor(inches / 12);
        inches = Math.round(inches % 12);
        $('#feet').html(feet + '\'' + inches + '&quot;');
    }

    /**
     * Calculate the character's weight in pounds.
     */
    function calculateWeight() {
        let weight = parseFloat($('#weight').val());
        if (!weight) {
            weight = 0;
        }
        const pounds = Math.floor(weight * 2.2046226218);
        $('#pounds').html(pounds);
    };

    $('#height').on('keyup', calculateHeight);
    $('#weight').on('keyup', calculateWeight);
    $('#birthdate').on('change', calculateAge);
    calculateAge();
    calculateHeight();
    calculateWeight();
    $('[data-bs-toggle="tooltip"]').tooltip();
});
