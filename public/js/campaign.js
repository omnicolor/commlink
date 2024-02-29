const discordWebhookFailed = function (xhr, status, errorThrown) {
    let message;
    switch (errorThrown) {
        case 'Forbidden':
            message = 'You don\'t have permission to change that channel!';
            break;
        case 'Not Found':
            message = 'That channel no longer seems to exist.';
            break;
        case 'Unprocessable Entity':
            message = [];
            $.each(xhr.responseJSON.errors, function (field, errorBag) {
                $.each(errorBag, function (key, error) {
                    message.push(error);
                });
            });
            message = message.join('<br>');
            break;
        default:
            message = 'An unknown error has occurred: ' + errorThrown;
            break;
    }
    $('#add-webhook-discord .modal-body').prepend(
        '<div class="alert alert-danger alert-dismissible fade show" role="alert">'
        + message
        + '<button type="button" class="btn-close" '
        + 'data-bs-dismiss="alert" aria-label="Close"></button>'
        + '</div>'
    );
};
const discordWebhookSucceeded = function (data, status, xhr) {
    const id = $('#add-webhook-discord').data('bs-channel-id');
    $('#add-webhook-discord .modal-body .alert').remove();
    $('button[data-bs-channel-id="' + id + '"]').replaceWith(
        '<button class="btn btn-link float-end">'
        + '<i class="bi bi-check-square-fill text-success"></i>'
        + '</button>'
    );
    $('#add-webhook-discord').hide();
    $('.modal-backdrop').remove();
};
$('#add-webhook-discord').on('show.bs.modal', function (event) {
    let button = $(event.relatedTarget);
    $('#webhook-discord-channel-name').html(button.data('bs-channel-name'));
    $('#add-webhook-discord').data('bs-channel-id', button.data('bs-channel-id'));
    $('#add-webhook-discord-footer-initial').removeClass('d-none');
    $('.discord-manual').addClass('d-none');
});
$('#add-webhook-discord').on('hidden.bs.modal', function () {
    $('#add-webhook-discord-footer-initial').removeClass('d-none');
    $('.discord-manual').addClass('d-none');
    $('#add-webhook-discord .modal-dialog').removeClass('modal-xl');
});
$('#add-webhook-discord-auto').on('click', function (event) {
    $.ajax({
        data: {
            auto: 1
        },
        error: discordWebhookFailed,
        headers: {
            'X-CSRF-TOKEN': csrfToken
        },
        method: 'PATCH',
        success: discordWebhookSucceeded,
        url: '/api/channels/' + $('#add-webhook-discord').data('bs-channel-id')
    });
});
$('.discord-manual .btn-primary').on('click', function (event) {
    $.ajax({
        data: {
            webhook: $('#discord-webhook').val()
        },
        error: discordWebhookFailed,
        headers: {
            'X-CSRF-TOKEN': csrfToken
        },
        method: 'PATCH',
        success: discordWebhookSucceeded,
        url: '/api/channels/' + $('#add-webhook-discord').data('bs-channel-id')
    });
});
$('#add-webhook-discord-manual').on('click', function (event) {
    $('#add-webhook-discord-footer-initial').addClass('d-none');
    $('.discord-manual').removeClass('d-none');
    $('#add-webhook-discord .modal-dialog').addClass('modal-xl');
});

const createEventFailed = function (xhr, status, errorThrown) {
    let message;
    switch (errorThrown) {
        case 'Forbidden':
            message = 'You don\'t have permission to add events!';
            break;
        case 'Not Found':
            message = 'The campaign doesn\'t seem to exist...';
            break;
        case 'Unprocessable Entity':
            message = [];
            $.each(xhr.responseJSON.errors, function (field, errorBag) {
                $.each(errorBag, function (key, error) {
                    message.push(error);
                });
            });
            message = message.join('<br>');
            break;
        default:
            message = 'An unknown error has occurred: ' + errorThrown;
            break;
    }
    $('#add-event .modal-body').prepend(
        '<div class="alert alert-danger alert-dismissible fade show" role="alert">'
        + message
        + '<button type="button" class="btn-close" '
        + 'data-bs-dismiss="alert" aria-label="Close"></button>'
        + '</div>'
    );
};
const createEventSucceeded = function (response, status, xhr) {
    $('#add-event .modal-body .alert').remove();
    $('#event-description').val('');
    $('#event-name').val('');
    $('#event-start').val('');
    $('#event-end').val('');
    $('#no-events').remove();
    const event = response.data;
    let real_start = new Date(event.real_start);
    real_start = real_start.toUTCString();

    let html = '<li class="list-group-item">'
        + '<button class="btn btn-outline-danger btn-sm float-end" '
        + 'data-id="' + event.id + '" type="button">'
        + '<i class="bi bi-trash3"></i>'
        + '</button>'
        + '<div class="fs-4">' + event.name + '</div>'
        + '<div class="fs-6 text-muted">' + real_start + '</div>';
    if (null !== event.description) {
        html += '<div>' + event.description + '</div>';
    }
    html += '<ul>';
    if ($('#event-attending').prop('checked')) {
        html += '<li>' + userName + ': Accepted</li>';
        $.ajax({
            data: {
                response: 'accepted'
            },
            headers: {
                'X-CSRF-TOKEN': csrfToken
            },
            method: 'PUT',
            url: '/api/events/' + event.id + '/rsvp'
        });
    } else {
        html += '<li>No responses</li>';
    }
    html += '</ul></li>';
    $('#new-event-row').before(html);
    $('#event-attending').prop('checked', true);
};
$('#event-form').on('submit', function (event) {
    event.preventDefault();
    event.stopPropagation();
    const form = $('#event-form');
    form.addClass('was-validated');
    if (!form[0].checkValidity()) {
        return;
    }
    form.removeClass('was-validated');
    let name = $('#event-name').val().trim();
    const start = $('#event-start').val();
    if ('' === name) {
        name = new Date(start);
        name = name.toUTCString();
    }
    $.ajax({
        data: {
            description: $('#event-description').val(),
            name: name,
            real_start: start,
            real_end: $('#event-end').val()
        },
        error: createEventFailed,
        headers: {
            'X-CSRF-TOKEN': csrfToken
        },
        method: 'POST',
        success: createEventSucceeded,
        url: '/api/campaigns/' + campaignId + '/events'
    });
});
const deleteEventFailed = function (xhr, status, errorThrown) {
    let message;
    switch (errorThrown) {
        case 'Forbidden':
            message = 'You don\'t have permission to delete events!';
            break;
        case 'Not Found':
            message = 'The event doesn\'t seem to exist...';
            break;
        default:
            message = 'An unknown error has occurred: ' + errorThrown;
            break;
    }
    $('#add-event .modal-body').prepend(
        '<div class="alert alert-danger alert-dismissible fade show" role="alert">'
        + message
        + '<button type="button" class="btn-close" '
        + 'data-bs-dismiss="alert" aria-label="Close"></button>'
        + '</div>'
    );
};
const deleteEventSucceeded = function (response, status, xhr) {
    let id = this.url.replace('/api/events/', '');
    $('#upcoming-events .btn-outline-danger[data-id="' + id + '"]')
        .parent('li')
        .remove();
    const list = $('#upcoming-events > li');
    if (1 === list.length) {
        $('#upcoming-events').prepend('<li class="list-group-item" id="no-events">No upcoming events</li>');
    }
};
$('#upcoming-events').on('click', '.btn-outline-danger', function (event) {
    let el = $(event.target);
    if ('I' === el[0].nodeName) {
        el = el.parent('button');
    }
    $.ajax({
        error: deleteEventFailed,
        headers: {
            'X-CSRF-TOKEN': csrfToken
        },
        method: 'DELETE',
        success: deleteEventSucceeded,
        url: '/api/events/' + el.data('id')
    });
});

const createInvitationSucceeded = function (response, status, xhr) {
    $('#no-players').remove();
    $('#invite-player-row').before(
        '<li class="list-group-item text-muted">'
            + '<i class="bi bi-person-exclamation"></i> '
            + response.data.invitee.name
            + ' (invited)</li>'
    );
    $('#invitee-name').val('');
    $('#invitee-email').val('');
};
const createInvitationFailed = function (response, status, xhr) {
    $('#invite-player-form .modal-body').prepend(
        '<div class="alert alert-danger" role="alert" id="invite-error">'
            + response.responseJSON.message
            + '</div>'
    );
};
$('#invite-player-form').on('submit', function (event) {
    event.preventDefault();
    event.stopPropagation();

    const form = $('#invite-player-form');
    form.addClass('was-validated');
    if (!form[0].checkValidity()) {
        return;
    }
    form.removeClass('was-validated');
    $('#invite-error').remove();

    const name = $('#invitee-name').val().trim();
    const email = $('#invitee-email').val().trim();

    $.ajax({
        data: {
            email: email,
            name: name,
        },
        error: createInvitationFailed,
        headers: {
            'X-CSRF-TOKEN': csrfToken
        },
        method: 'POST',
        success: createInvitationSucceeded,
        url: '/api/campaigns/' + campaignId + '/invite'
    });
});

const updateDateSucceeded = function (response, status, xhr) {
    window.console.log(response.data.formatted_date);
    const currentDate = response.data.formatted_date;
    $('#current-date').html(currentDate);
    $('#set-current-date .modal-body .alert').remove();
};

$('#current-date-form').on('submit', function (event) {
    event.preventDefault();
    event.stopPropagation();
    const date = $('#update-current-date').val();

    $.ajax({
        contentType: 'application/json-patch+json',
        data: JSON.stringify([
            {
                op: 'replace',
                path: '/options/currentDate',
                value: date
            }
        ]),
        dataType: 'json',
        headers: {
            'X-CSRF-TOKEN': csrfToken
        },
        method: 'PATCH',
        processData: false,
        url: '/api/campaigns/' + campaignId
    })
        .done(updateDateSucceeded)
        .fail(function (data) { window.console.log(data); });
});
