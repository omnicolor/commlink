$(function () {
    'use strict';

    /**
     * Add a contact to the list.
     * @param {!Object} contact Contact to add
     */
    function addContactRow(contact) {
        $('#no-contacts').hide();
        const html = '<li class="list-group-item" data-id="' + contact.id +
            '">' + htmlEncode(contact.name) + ' (' +
            htmlEncode(contact.archetype) + ' C: ' +
            contact.connection + ' L: ' + contact.loyalty + ') ' +
            '<div class="float-end">' +
            '<button class="btn btn-danger btn-sm" " type="button">' +
            '<span aria-hidden="true" class="bi bi-dash"></span> Remove' +
            '</button></div>' +
            '<input name="contact-names[]" type="hidden" value="' +
            htmlEncode(contact.name) + '">' +
            '<input name="contact-archetypes[]" type="hidden" value="' +
            htmlEncode(contact.archetype) + '">' +
            '<input name="contact-connections[]" type="hidden" value="' +
            contact.connection + '">' +
            '<input name="contact-loyalties[]" type="hidden" value="' +
            contact.loyalty + '">' +
            '<input name="contact-notes[]" type="hidden" value="' +
            htmlEncode(contact.notes) + '">' +
            '</li>';
        $(html).insertBefore($('#no-contacts'));
    }

    function addContact(e) {
        e.preventDefault();
        e.stopPropagation();
        const form = $('#contacts-modal form')[0];
        form.classList.add('was-validated');
        if (form.checkValidity() === false) {
            return;
        }
        const contact = {
            name: $('#contact-name').val(),
            archetype: $('#contact-archetype').val(),
            connection: parseInt($('#contact-connection').val(), 10),
            loyalty: parseInt($('#contact-loyalty').val(), 10),
            notes: $('#contact-notes').val()
        };

        character.contacts.push(contact);
        addContactRow(contact);
        resetContactsModal();
        points = new Points(character);
        updatePointsToSpendDisplay(points);
    }

    function removeContact(e) {
        let el = $(e.target);
        if ('SPAN' === el[0].nodeName) {
            el = el.parent();
        }
        const id = el.parents('li').data('id');
        delete character.contacts[id];
        el.parents('li').remove();
        const tmpContacts = character.contacts.filter(function(v){return v;});
        if (!tmpContacts.length) {
            $('#no-contacts').show();
            character.contacts = [];
        }
        points = new Points(character);
        updatePointsToSpendDisplay(points);
    }

    function resetContactsModal() {
        $('#contacts-modal form').removeClass('was-validated');
        $('#contact-name').val('').focus();
        $('#contact-archetype').val('');
        $('#contact-connection').val('');
        $('#contact-loyalty').val('');
        $('#contact-notes').val('');
    }

    /**
     * Add an identity row.
     * @param {!Object} identity Identity to add
     * @param {?Object} replace
     */
    function addIdentityRow(identity, replace) {
        $('#no-identities').hide();

        let html = '<li class="list-group-item" ' +
            'data-id="' + identity.id + '">' +
            htmlEncode(identity.name) +
            '<div class="float-end">';
        if (!identity.sin) {
            html += '<button class="btn btn-success btn-sm mx-1" ' +
                'data-id="' + identity.id + '" data-bs-target="#sin-modal" ' +
                'data-bs-toggle="modal" type="button">' +
                '<span aria-hidden="true" class="bi bi-plus"></span> Add SIN' +
                '</button>';
        } else {
            html += '<div class="btn-group">' +
                '<button aria-haspopup="true" aria-expanded="false" ' +
                'class="btn btn-success btn-sm dropdown-toggle" ' +
                'data-bs-toggle="dropdown" ' +
                'id="identity-dropdown-' + identity.id + '" type="button">' +
                '<span aria-hidden="true" class="bi bi-plus"></span> Add' +
                '</button>' +
                '<div class="dropdown-menu" ' +
                'aria-labelledby="identity-dropdown-' + identity.id + '">' +
                '<button class="dropdown-item" ' +
                'data-bs-target="#licenses-modal" data-bs-toggle="modal" ' +
                'type="button">Add license</button>' +
                '<button class="dropdown-item" ' +
                'data-bs-target="#lifestyles-modal" data-bs-toggle="modal" ' +
                'type="button">' +
                'Add lifestyle</a>' +
                '<button class="dropdown-item" ' +
                'data-bs-target="#subscriptions-modal" data-bs-toggle="modal" ' +
                'type="button">' +
                'Add subscription</a>' +
                '</div></div>';
            if (identity.sinner) {
                html += '<button class="btn btn-primary btn-sm ml-1" ' +
                    'data-id="' + identity.id + '" data-bs-target="#sin-modal" ' +
                    'data-bs-toggle="modal" disabled type="button">' +
                    'Real SIN - ' + identity.sin + '</button>';
            } else {
                html += '<button class="btn btn-primary btn-sm ml-1" ' +
                    'data-id="' + identity.id + '" data-bs-target="#sin-modal" ' +
                    'data-bs-toggle="modal" type="button">' +
                    'Change SIN - ' + identity.sin + '</button>';
            }
        }
        html += '<button class="btn btn-danger btn-sm identity ml-1" ' +
            'type="button">' +
            '<span aria-hidden="true" class="bi bi-dash"></span> ' +
            'Remove</button>' +
            '</div><ul class="list-group">';
        $.each(identity.lifestyles, function (index, lifestyle) {
            html += '<li class="list-group-item">Lifestyle: ' + lifestyle.name +
                ' - ' + lifestyle.quantity + ' month';
            if (1 !== lifestyle.quantity) {
                html += 's';
            }
            html += '<div class="float-end">' +
                '<button class="btn btn-danger btn-sm lifestyle" ' +
                'data-lifestyle="' + index + '" ' +
                'data-identity="' + identity.id + '" type="button">' +
                '<span aria-hidden="true" class="bi bi-dash"></span> Remove' +
                '</button></div></li>';
        });
        $.each(identity.subscriptions, function (index, subscription) {
            html += '<li class="list-group-item">Subscription: ' +
                subscription.name + ' - ' +
                subscription.quantity + ' month';
            if (1 !== subscription.quantity) {
                html += 's';
            }
            html += '<div class="float-right">' +
                '<button class="btn btn-danger btn-sm subscription" ' +
                'data-subscription="' + index + '" ' +
                'data-identity="' + identity.id + '" type="button">' +
                '<span aria-hidden="true" class="bi bi-dash"></span> Remove' +
                '</button></div></li>';
        });
        $.each(identity.licenses, function (index, license) {
            html += '<li class="list-group-item">License: ' +
                htmlEncode(license.license) + ' - ' + license.rating +
                '<div class="float-right">' +
                '<button class="btn btn-danger btn-sm license" ' +
                'data-license-index="' + index + '" type="button">'+
                '<span aria-hidden="true" class="bi bi-dash"></span> ' +
                'Remove</button></div></li>';
        });
        html += '</ul></li>';

        if (replace) {
            replace.replaceWith(html);
            return;
        }
        $(html).insertBefore($('#no-identities'));
    }

    /**
     * Validate the identity form and add the identity if valid.
     * @param {!Event} e
     */
    function handleAddIdentity(e) {
        e.preventDefault();
        e.stopPropagation();
        const form = $('#identities-modal form')[0];
        form.classList.add('was-validated');
        if (form.checkValidity() === false) {
            return;
        }
        const identity = {
            id: character.identities.length,
            licenses: [],
            lifestyles: [],
            name: $('#identity-name').val(),
            notes: $('#identity-notes').val(),
            sin: null,
            subscriptions: []
        };
        character.identities.push(identity);
        addIdentityRow(identity);
        resetIdentityModal();
    }

    /**
     * Reset the identity modal to its original state.
     */
    function resetIdentityModal() {
        $('#identities-modal form').removeClass('was-validated');
        $('#identity-name').val('').focus();
        $('#identity-notes').val('');
    }

    /**
     * Handle the user attaching a SIN to an identity.
     * @param {!Event} e Event that fired this handler
     */
    function handleAttachSinClick(e) {
        let el = $(e.target);
        if ('SPAN' == el[0].nodeName) {
            el = el.parent();
        }
        const rating = el.data('rating');
        const modal = $('#sin-modal');
        const id = modal.data('identity');
        let identity;
        for (let i = 0, c = character.identities.length; i < c; i++) {
            if (character.identities[i] && character.identities[i].id == id) {
                identity = character.identities[i];
                break;
            }
        }
        identity.sin = rating;
        addIdentityRow(
            identity,
            $('#identities li[data-id="' + id + '"]')
        );
        bootstrap.Modal.getInstance(modal).hide();
    }

    let points = new Points(character);
    updatePointsToSpendDisplay(points);
    let names = [];
    loadNames();

    $('#contacts-modal form').on('submit', addContact);
    $('#contacts-list').on('click', '.btn-danger', removeContact);

    $('#identities-modal form').on('submit', handleAddIdentity);

    $('#sin-modal').on('show.bs.modal', function (e) {
        // Add the identity ID to the modal.
        $('#sin-modal').data(
            'identity',
            $(e.relatedTarget).parents('li').data('id')
        );
    });
    $('#sin-modal .btn-success').on('click', handleAttachSinClick);

    $('button.suggest-name').on('click', suggestName);
});
