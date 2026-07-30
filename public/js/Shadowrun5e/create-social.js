$(function () {
    'use strict';

    function findIdentity(id) {
        for (let i = 0, c = character.identities.length; i < c; i++) {
            if (character.identities[i] && character.identities[i].id == id) {
                return character.identities[i];
            }
        }
        return null;
    }

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

    function suggestName(e) {
        $(e.target).parent().children('input').val(names.shift());
        if (0 === names.length) {
            loadNames();
        }
    }

    function loadNames() {
        $.ajax({
            dataType: 'json',
            success: function (response) { names = response.data; },
            url: '/api/fakes/names'
        });
    }

    function addIdentityRow(identity, replace) {
        $('#no-identities').hide();

        let html = '<li class="list-group-item" ' +
            'data-id="' + identity.id + '"><strong>' +
            htmlEncode(identity.name) +
            '</strong><div class="float-end">';
        if (identity.sin) {
            html += '<div class="btn-group">' +
                '<button aria-haspopup="true" aria-expanded="false" ' +
                'class="btn btn-success btn-sm dropdown-toggle" ' +
                'data-bs-toggle="dropdown" ' +
                'id="identity-dropdown-' + identity.id + '" type="button">' +
                '<span aria-hidden="true" class="bi bi-plus"></span> ' +
                'Add to SIN ' +
                '</button>' +
                '<div class="dropdown-menu" ' +
                'aria-labelledby="identity-dropdown-' + identity.id + '">' +
                '<button class="dropdown-item" ' +
                'data-bs-target="#licenses-modal" data-bs-toggle="modal" ' +
                'type="button">Add license</button>' +
                '<button class="dropdown-item" ' +
                'data-bs-target="#lifestyles-modal" data-bs-toggle="modal" ' +
                'type="button">' +
                'Add lifestyle</button>' +
                '<button class="dropdown-item" ' +
                'data-bs-target="#subscriptions-modal" data-bs-toggle="modal" ' +
                'type="button">' +
                'Add subscription</button>' +
                '</div></div> ';
            if (identity.sinner) {
                html += '<button class="btn btn-primary btn-sm mx-1" ' +
                    'data-id="' + identity.id + '" data-bs-target="#sin-modal" ' +
                    'data-bs-toggle="modal" disabled type="button">' +
                    'Real SIN - ' + identity.sin + '</button>';
            } else {
                html += '<button class="btn btn-primary btn-sm mx-1" ' +
                    'data-id="' + identity.id + '" data-bs-target="#sin-modal" ' +
                    'data-bs-toggle="modal" type="button">' +
                    '<span aria-hidden="true" class="bi bi-pencil"></span> ' +
                    'Change SIN - ' + identity.sin + '</button> ';
            }
        } else {
            html += '<button class="btn btn-success btn-sm mx-1" ' +
                'data-id="' + identity.id + '" data-bs-target="#sin-modal" ' +
                'data-bs-toggle="modal" type="button">' +
                '<span aria-hidden="true" class="bi bi-plus"></span> Add SIN' +
                '</button> ';
        }
        html += '<button class="btn btn-danger btn-sm identity ml-1" ' +
            'type="button">' +
            '<span aria-hidden="true" class="bi bi-dash"></span> ' +
            'Remove</button>' +
            '</div><ul class="list-group list-group-flush mt-3 ms-3 me-0">';
        $.each(identity.licenses, function (index, license) {
            if (!license) {
                return;
            }
            html += '<li class="list-group-item pe-0" data-identity-index="' +
                identity.id + '" data-license-index="' + index + '">License: ' +
                htmlEncode(license.license) + ' (' + license.rating +
                ')<div class="float-end">' +
                '<button class="btn btn-danger btn-sm license" ' +
                'data-license-index="' + index + '" type="button">'+
                '<span aria-hidden="true" class="bi bi-dash"></span> ' +
                'Remove</button></div></li>';
        });
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
        html += '</ul></li>';

        if (replace) {
            replace.replaceWith(html);
            return;
        }
        $(html).insertBefore($('#no-identities'));
    }

    function addIdentity(e) {
        e.preventDefault();
        e.stopPropagation();
        const form = $('#identities-modal form')[0];
        form.classList.add('was-validated');
        if (form.checkValidity() === false) {
            return;
        }
        const identity = {
            id: character.identities.length + 1,
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

    function removeIdentity(e) {
        let el = $(e.target);
        if ('SPAN' === el[0].nodeName) {
            el = el.parent();
        }
        const id = el.parents('li').data('id');
        for (let i = 0, c = character.identities.length; i < c; i++) {
            if (!character.identities[i]) {
                continue;
            }
            if (character.identities[i].id === id) {
                delete character.identities[i];
                break;
            }
        }
        el.parents('li').remove();

        const tmpIdentities = character.identities.filter(function(v){return v;});
        if (!tmpIdentities.length) {
            $('#no-identities').show();
            character.identities = [];
        }
    }

    function resetIdentityModal() {
        $('#identities-modal form').removeClass('was-validated');
        $('#identity-name').val('').focus();
        $('#identity-notes').val('');
    }

    function addSinClick(e) {
        let el = $(e.target);
        if ('SPAN' == el[0].nodeName) {
            el = el.parent();
        }
        const rating = el.data('rating');
        const modal = $('#sin-modal');
        const id = modal.data('identity');
        let identity = findIdentity(id);

        identity.sin = rating;
        addIdentityRow(
            identity,
            $('#identities-list li[data-id="' + id + '"]')
        );
        bootstrap.Modal.getInstance(modal).hide();
    }

    function addLicense(e) {
        e.preventDefault();
        e.stopPropagation();

        const modal = $('#licenses-modal');
        const id = modal.data('identity');
        let identity = findIdentity(id);
        identity.licenses = identity.licenses || [];
        identity.licenses.push({
            rating: parseInt($('#license-rating').val()),
            license: $('#license-name').val()
        });
        addIdentityRow(
            identity,
            $('#identities-list li[data-id="' + id + '"]')
        );

        $('#license-name').val('');
    }

    function removeLicense(e) {
        let el = $(e.target);
        if ('SPAN' === el[0].nodeName) {
            el = el.parent();
        }
        const row = el.parents('li');
        const identityId = row.data('identity-index');
        const licenseIndex = row.data('license-index');
        const identity = findIdentity(identityId);
        delete identity.licenses[licenseIndex];

        addIdentityRow(
            identity,
            $('#identities-list li[data-id="' + identityId + '"]')
        );
    }

    function updateLifestyleCost(e) {
        const comforts = parseInt($('#comforts').val());
        const comfortsBase = parseInt($('#comforts').prop('min'));
        const neighborhood = parseInt($('#neighborhood').val());
        const neighborhoodBase = parseInt($('#neighborhood').prop('min'));
        const security = parseInt($('#security').val());
        const securityBase = parseInt($('#security').prop('min'));
        let cost = parseInt($('#lifestyle-cost').html().replace('¥', '').replace(',', ''));
        const allowedPoints = parseInt($('#lifestyle-points').val());
        let spentPoints = 0 + (comforts - comfortsBase)
            + (neighborhood - neighborhoodBase) + (security - securityBase);

        if (0 !== spentPoints && 0 === cost) {
            // Points spent on street level lifestyles cost 50¥ per point.
            cost = 50 * spentPoints;
        } else {
            cost = cost + (spentPoints * 0.1) * cost;
        }

        cost = cost * parseInt($('#lifestyle-months').val());
        $('#lifestyle-total').html(nuyen.format(cost));

        const remainingPoints = allowedPoints - spentPoints;

        $('#remaining-points').html(remainingPoints);
        $('#customize-lifestyle button').prop('disabled', 0 > remainingPoints);
        $('#lifestyle-points-warning').toggleClass('d-none', 0 <= remainingPoints);
    }

    function showLifestyle(e) {
        $('#click-panel').addClass('d-none');
        $('#info-panel').removeClass('d-none');

        let el = $(e.target).parent('tr');
        $('#lifestyle-id').val(el.data('id'));
        $('#lifestyle-name').html(el.data('name'));
        if (el.data('description')) {
            $('#lifestyle-description').html(el.data('description'));
        }

        const comforts = el.data('comforts');
        const comfortsMax = el.data('comforts-max');
        $('#lifestyle-comforts').html(comforts + ' [' + comfortsMax + ']');
        $('#comforts').prop('min', comforts)
            .prop('max', comfortsMax)
            .val(comforts);

        const security = el.data('security');
        const securityMax = el.data('security-max');
        $('#lifestyle-security').html(security + ' [' + securityMax + ']');
        $('#security').prop('min', security)
            .prop('max', securityMax)
            .val(security);

        const neighborhood = el.data('neighborhood');
        const neighborhoodMax = el.data('neighborhood-max');
        $('#lifestyle-neighborhood')
            .html(neighborhood + ' [' + neighborhoodMax + ']');
        $('#neighborhood').prop('min', neighborhood)
            .prop('max', neighborhoodMax)
            .val(neighborhood);

        const lifestyleCost = el.data('cost');
        $('#lifestyle-cost').html(nuyen.format(lifestyleCost));
        $('#lifestyle-total').html(nuyen.format(lifestyleCost));

        const lifestylePoints = el.data('points');
        $('#lifestyle-points').val(lifestylePoints);
        $('#remaining-points').html(lifestylePoints);
    }

    function addLifestyle(e) {
        e.preventDefault();
        e.stopPropagation();
        const modal = $('#lifestyles-modal');
        const id = modal.data('identity');
        let identity = findIdentity(id);
        identity.lifestyles = identity.lifestyles || [];
        let lifestyle = {
            id: $('#lifestyle-id').val(),
            attributes: {},
        };
        window.console.log(lifestyle);
    }

    let points = new Points(character);
    updatePointsToSpendDisplay(points);
    let names = [];
    loadNames();

    $('#contacts-modal form').on('submit', addContact);
    $('#contacts-list').on('click', '.btn-danger', removeContact);

    $('#identities-modal form').on('submit', addIdentity);
    $('#identities-list').on('click', '.btn-danger identity', removeIdentity);

    $('#sin-modal').on('show.bs.modal', function (e) {
        // Add the identity ID to the modal.
        $('#sin-modal').data(
            'identity',
            $(e.relatedTarget).parents('li').data('id')
        );
    });
    $('#sin-modal .btn-success').on('click', addSinClick);

    $('#licenses-modal form').on('submit', addLicense);
    $('#licenses-modal').on('show.bs.modal', function (e) {
        // Add the identity ID to the modal.
        $('#licenses-modal').data(
            'identity',
            $(e.relatedTarget).parents('li').data('id')
        );
    });
    $('#identities-list').on('click', '.btn-danger.license', removeLicense);

    $('#lifestyles-modal tbody td').on('click', showLifestyle);
    $('#lifestyles-modal').on('show.bs.modal', function (e) {
        // Add the identity ID to the modal.
        $('#lifestyles-modal').data(
            'identity',
            $(e.relatedTarget).parents('li').data('id')
        );
    });
    $('#customize-lifestyle input').on('change', updateLifestyleCost);
    $('#lifestyles-modal').on('submit', addLifestyle);

    $('button.suggest-name').on('click', suggestName);
});
