/**
 * Take a character's name, and return it ready to be a unique
 * element id.
 * @param {string} string
 * @return {string}
 */
function makeSafeForId(string) {
    return string.replace(/^[0-9]*/, '')
        .replace(/([^A-Za-z0-9[\]{}_.:-])\s?/g, '')
        .toLowerCase();
}

/**
 * Add a new combatant to the initiative list.
 * @param {string} name Combatant's name
 * @param {Number} initiative
 */
function addInitiativeRow(name, initiative) {
    $('#combatants').append(
        '<li class="list-group-item" id="init-'
        + makeSafeForId(name) + '">' + name
        + '<span class="float-end"><span class="score">'
        + initiative + '</span>'
        + '<span class="dropdown">'
        + '<button aria-expanded="false" '
        + 'class="btn btn-link btn-sm" '
        + 'data-bs-toggle="dropdown" type="button">'
        + '<i class="bi bi-three-dots-vertical"></i>'
        + '</button>'
        + '<ul class="dropdown-menu">'
        + '<li><a class="dropdown-item" href="#">Change name</a></li>'
        + '<li><a class="dropdown-item" href="#">Change initiative</a></li>'
        + '<li><a class="dropdown-item remove" href="#">Remove from combat</a></li>'
        + '</ul>'
        + '</span>'
        + '</li>'
    );
}

/**
 * Clear the initiatives shown on the page.
 */
function clearInitiatives() {
    $('#combatants').html(
        '<li class="list-group-item" id="no-combatants">'
        + 'No combatants.</li>'
    );
}

/**
 * Update an existing combatant's initiative.
 * @param {Jquery} el Element containing the combatant's old init
 * @param {Number} initiative
 */
function updateInitiative(el, initiative) {
    el.find('.score').html(initiative);
}

/**
 * Compare two initiative HTML rows.
 * @param {Element} a
 * @param {Element} b
 * @return {Number}
 */
function compareInitiatives(a, b) {
    const aVal = parseInt($(a).find('.score').text(), 10);
    const bVal = parseInt($(b).find('.score').text(), 10);
    return bVal - aVal;
}

/**
 * Sort the initiative listing on the page.
 */
function sortInitiatives() {
    const combatantsEl = $('#combatants');
    let rows = combatantsEl.children()
        .toArray()
        .sort(compareInitiatives);
    combatantsEl.html('');
    $.each(rows, function (unused, row) {
        combatantsEl.append(row);
    });
}

Echo.private(`campaign.{{ $campaign->id }}`)
    .listen('InitiativeAdded', (e) => {
        $('#no-combatants').hide();
        const el = $('#init-' + makeSafeForId(e.name));
        if (0 === el.length) {
            addInitiativeRow(e.name, e.initiative.initiative);
        } else {
            updateInitiative(el, e.initiative.initiative);
        }
        sortInitiatives();
    });

$('.bi-stop-circle').on('click', function () {
    const url = '/api/campaigns/' + campaign + '/initiatives';
    $.ajax({
        data: {
            _token: csrfToken
        },
        success: clearInitiatives,
        type: 'DELETE',
        url: url
    });
});

$('input[name="initiative-type"]').on('change', function (e) {
    if ('roll' === $(e.target).val()) {
        $('#roll-form').show();
        $('#assign-form').hide();
        return;
    }
    $('#roll-form').hide();
    $('#assign-form').show();
});

$('#add-combatant .btn-primary').on('click', function () {
    const payload = {
        character_name: $('#name').val(),
        grunt_id: $('#grunt-id').val(),
        _token: csrfToken
    };

    if ('roll' === $('input[name="initiative-type"]:checked').val()) {
        payload.base_initiative = parseInt($('#base').val(), 10);
        payload.initiative_dice = parseInt($('#dice').val(), 10);
    } else {
        payload.initiative = parseInt($('#init').val(), 10);
    }

    const url = '/api/campaigns/' + campaign + '/initiatives';
    $.post(url, payload, function () { $('#name').focus(); });
});

$('.bi-play-circle').on('click', function () {
    const combatants = $('#combatants').children();
    let active = $('#combatants .active');
    if (0 === active.length) {
        // First turn.
        // If the first person has no score, don't highlight them.
        if (isNaN(parseInt($(combatants[0]).find('.score').html(), 10))) {
            return;
        }
        $(combatants[0]).addClass('active');
        return;
    }
    active.removeClass('active');
    active = active.next();
    if (0 === active.length) {
        // Everyone has already gone.
        nextRound();
        return;
    }
    const score = parseInt($(active).find('.score').html(), 10);
    if (isNaN(score)) {
        nextRound();
        return;
    }
    active.addClass('active');
});

function nextRound() {
    const combatants = $('#combatants').children();
    $.each(combatants, function (unused, row) {
        const scoreEl = $(row).find('.score');
        let score = parseInt(scoreEl.html(), 10);
        if (isNaN(score)) {
            return;
        }
        score -= 10;
        if (score <= 0) {
            score = '';
        }
        scoreEl.html(score);
    });
    // If the first person has no score, don't highlight them.
    if (isNaN(parseInt($(combatants[0]).find('.score').html(), 10))) {
        return;
    }
    $(combatants[0]).addClass('active');
}

function reloadInitiatives(data) {
    $('#combatants').html('');
    $.each(data.initiatives, function (index, initiative) {
        addInitiativeRow(initiative.character_name, initiative.initiative);
    });
    sortInitiatives();
}

$('.bi-arrow-clockwise').on('click', function () {
    const url = '/api/campaigns/' + campaign + '/initiatives';
    $.get(url, '', reloadInitiatives);
});

$('#combatants').on('click', '.remove', function (e) {
    const el = $(e.target).parents('.list-group-item');
    const id = el.data('id');
    const url = '/api/campaigns/' + campaign + '/initiatives/' + id;
    $.ajax({
        data: {
            _token: csrfToken
        },
        type: 'DELETE',
        url: url
    });
    el.remove();
});

$('#change-initiative').on('show.bs.modal', function (e) {
    const el = $(e.relatedTarget).parents('.list-group-item');
    const id = el.data('id')
    const initiative = parseInt(el.find('.score').html(), 10);
    $('#change-id').val(id);
    $('#new-initiative').val(initiative).focus();
});
$('#change-initiative form').on('submit', function (e) {
    e.preventDefault();
    const id = $('#change-id').val();
    const initiative = $('#new-initiative').val();
    $('li[data-id="' + id + '"]').find('.score').html(initiative);

    const url = '/api/campaigns/' + campaign + '/initiatives/' + id;
    $.ajax({
        data: {
            initiative: initiative,
            _token: csrfToken
        },
        type: 'PATCH',
        url: url
    });
    const modalEl = document.querySelector('#change-initiative');
    const modal = bootstrap.Modal.getInstance(modalEl);
    modal.hide();
});

$('#rename-combatant').on('show.bs.modal', function (e) {
    const el = $(e.relatedTarget).parents('.list-group-item');
    const id = el.data('id')
    const name = el[0].firstChild.data.trim();
    $('#rename-id').val(id);
    $('#rename-name').val(name).focus();
});
$('#rename-combatant form').on('submit', function (e) {
    e.preventDefault();
    const id = $('#rename-id').val();
    const name = $('#rename-name').val();
    const el = $('li[data-id="' + id + '"]');
    el[0].firstChild.data = name;

    const url = '/api/campaigns/' + campaign + '/initiatives/' + id;
    $.ajax({
        data: {
            character_name: name,
            _token: csrfToken
        },
        type: 'PATCH',
        url: url
    });
    const modalEl = document.querySelector('#rename-combatant');
    const modal = bootstrap.Modal.getInstance(modalEl);
    modal.hide();
});
