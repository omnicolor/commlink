function updateContactSubmitButton() {
    $('#contact-submit').prop(
        'disabled',
        !$('#contact-name').val().trim() || !$('#contact-archetype').val().trim()
    );
}

function resetContactForm() {
    $('#contact-form').removeClass('was-validated');
    $('#contact-character-id').val('');
    $('#contact-id').val('');
    $('#contact-name').val('');
    $('#contact-archetype').val('');
    $('#contact-connection').val('');
    $('#contact-loyalty').val('');
    $('#contact-notes').val('');
    $('#contact-gm-notes').val('');

    // TODO: Reset characters in #contact-characters
}

/**
 * Handle either saving a new contact or updating an existing one.
 * @param {Event} e
 */
function saveContact(e) {
    e.preventDefault();
    e.stopPropagation();
    if ($('#contact-id').val() && $('#contact-character-id').val()) {
        return updateContact();
    }

    let characters = [];
    $.each($('#contact-characters :checked'), function (unused, el) {
        characters.push($(el).val());
    });
    const contact = {
        name: $('#contact-name').val().trim(),
        archetype: $('#contact-archetype').val().trim(),
        connection: $('#contact-connection').val(),
        loyalty: $('#contact-loyalty').val(),
        notes: $('#contact-notes').val().trim(),
        gmNotes: $('#contact-gm-notes').val().trim()
    };

    if ('0' === contact.connection || '' === contact.connection) {
        contact.connection = null;
    } else {
        contact.connection = parseInt(contact.connection, 10);
    }
    if ('0' === contact.loyalty || '' === contact.loyalty) {
        contact.loyalty = null;
    } else {
        contact.loyalty = parseInt(contact.loyalty, 10);
    }

    if ('' === contact.notes) {
        contact.notes = null;
    }
    if ('' === contact.gmNotes) {
        contact.gmNotes = null;
    }

    $.each(characters, function (unused, characterId) {
        const settings = {
            data: {
                _token: csrfToken,
                contact: contact
            },
            method: 'POST',
            url: '/api/shadowrun5e/characters/' + characterId + '/contacts'
        };
        window.console.log(settings);
    });
}

function updateContact() {
    window.console.log('Updating an existing contact');
}

$('#contact-name').on('change', updateContactSubmitButton);
$('#contact-archetype').on('change', updateContactSubmitButton);
$('#contact-form').on('submit', saveContact);

// Handle carriage returns properly in textareas, cross-browser/cross-platform.
$.valHooks.textarea = {
    get: function (elem) {
        return elem.value.replace(/\r?\n/g, "\r\n");
    }
};
