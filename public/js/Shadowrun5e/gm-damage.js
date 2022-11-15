/**
 * Turn the submit button on and off as the form is changed.
 */
function updateDamageForm() {
    $('#damage-save').prop(
        'disabled',
        (!$('#damage-type').val().trim()
            || isNaN($('#damage-amount').val())
            || 0 == $('#damage-amount').val())
    );

    if (isNaN($('#damage-amount').val())
        || 0 == $('#damage-amount').val()
        || '' == $('#damage-amount').val()) {
        return;
    }

    if (parseInt($('#damage-amount').val(), 10) >= 0) {
        $('#damage-save')
            .addClass('btn-danger')
            .removeClass('btn-success')
            .text('Hurt \'em');
    } else {
        $('#damage-save')
            .addClass('btn-success')
            .removeClass('btn-danger')
            .text('Heal them');
    }
}

/**
 * Handle submitting the damage form to heal or hurt a character, or
 * take some edge.
 * @param {Event} e
 */
function handleDamageSubmit(e) {
    e.preventDefault();
    e.stopPropagation();

    const form = $('#damage-form');
    if (form[0].checkValidity() === false) {
        form.addClass('was-validated');
        return;
    }
    form.removeClass('was-validated');
    $('#damage-modal').modal('hide');
    const characterId = $('#damage-modal').data('id');
    const damageType = $('#damage-type').val().toLowerCase();
    let type;
    switch (damageType) {
        case 'physical':
            type = '/damagePhysical';
            break;
        case 'stun':
            type = '/damageStun';
            break;
        case 'edge':
            type = '/edgeCurrent';
            break;
    }

    // Figure out how much damage they should have.
    const damage = parseInt($('#damage-amount').val(), 10);
    let value;
    let current;
    // Edge and damage are backwards from each other.
    if ('edge' === damageType) {
        // Figure out their maximum edge.
        const max = $('#edge-' + characterId + ' .box').length;
        const used = $('#edge-' + characterId + ' .used').length;
        current = max - used;
        value = Math.min(Math.max(0, current - damage), max);
    } else {
        current = $('#' + damageType + '-' + characterId + ' .used').length;
        value = Math.max(0, current + damage);
    }

    const settings = {
        accept: 'application/json-patch+json',
        data: {
            _token: csrfToken,
            patch: [
                {
                    op: 'replace',
                    path: type,
                    value: value
                }
            ]
        },
        method: 'PATCH',
        url: '/api/shadowrun5e/characters/' + characterId
    };

    $.ajax(settings)
        .done(handleDamageResponse)
        .fail(function (data) { window.console.log(data); });
}

function healAll() {
    $.each($('.character-row'), function (unused, row) {
        const id = $(row).data('id');
        const edge = $('#edge-' + id + ' .box').length;
        const payload = {
            accept: 'application/json-patch+json',
            data: {
                _token: csrfToken,
                patch: [
                    {
                        op: 'replace',
                        path: '/edgeCurrent',
                        value: edge
                    },
                    {
                        op: 'replace',
                        path: '/damagePhysical',
                        value: 0
                    },
                    {
                        op: 'replace',
                        path: '/damageStun',
                        value: 0
                    },
                    {
                        op: 'replace',
                        path: '/damageOverflow',
                        value: 0
                    }
                ]
            },
            method: 'PATCH',
            url: '/api/shadowrun5e/characters/' + id
        };
        $.ajax(payload)
            .done(handleDamageResponse)
            .fail(function (data) { window.console.log(data); });
    });
}

/**
 * Handle the response from the server about damaging a character.
 * @param {Object} data
 */
function handleDamageResponse(data) {
    const character = data.data;
    const usedEdge = character.edge - character.edgeCurrent;
    $('#physical-' + character.id + ' .box').removeClass('used');
    $('#stun-' + character.id + ' .box').removeClass('used');
    $('#overflow-' + character.id + ' .box').removeClass('used');
    $('#edge-' + character.id + ' .box').removeClass('used');
    $('#physical-' + character.id + ' .box:lt(' +
        character.damagePhysical + ')').addClass('used');
    $('#stun-' + character.id + ' .box:lt(' +
        character.damageStun + ')').addClass('used');
    $('#overflow-' + character.id + ' .box:lt(' +
        character.damageOverflow + ')').addClass('used');
    $('#edge-' + character.id + ' .box:lt(' + usedEdge + ')')
        .addClass('used');
}

$('#damage-type').on('change', updateDamageForm);
$('#damage-amount')
    .on('keyup', updateDamageForm)
    .on('change', updateDamageForm);
$('#damage-modal').on('show.bs.modal', function (e) {
    const button = $(e.relatedTarget);
    $('#damage-handle').html(button.text().trim());
    $('#damage-modal').data('id', button.data('id'));
    $('#damage-type option').first().prop('selected', true);
    $('#damage-amount').val('');
    $('#damage-save')
        .prop('disabled', true)
        .addClass('btn-danger')
        .removeClass('btn-success')
        .text('Hurt \'em');
});
$('#damage-form').on('submit', handleDamageSubmit);
$('#heal-all').on('click', healAll);
