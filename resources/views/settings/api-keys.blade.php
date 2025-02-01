@php
use App\Features\ApiAccess;
use Laravel\Pennant\Feature;
@endphp
<x-app>
    <x-slot name="title">Settings - API keys</x-slot>

    <div class="row mt-4">
        <div class="col-lg-1"></div>
        <div class="col">
            <h1>Settings - API keys</h1>
        </div>
        <div class="col-lg-1"></div>
    </div>

    <div class="row mt-4">
        <div class="col-lg-1"></div>
        <div class="col">
            <div id="api-keys-table">
                <p>
                    An API token is used to interact directly with
                    <a href="/openapi/index.html">{{ config('app.name') }}'s API</a>.
                    If you're not a software developer or don't know what an
                    API is, this probably isn't something that you need to use.
                    In fact, unless you are building an integration with
                    {{ config('app.name') }}, this is really something you
                    should avoid playing with.
                </p>

                <table class="table">
                    <thead>
                        <tr>
                            <th scope="col">Name</th>
                            <th scope="col">Expires at</th>
                            <th scope="col">Last used</th>
                            <th scope="col">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                    @foreach ($user->tokens as $token)
                        <tr>
                            <td>{{ $token->name }}</td>
                            <td>{{ $token->expires_at?->format('Y-m-d') }}</td>
                            <td>{{ $token->last_used_at }}</td>
                            <td><button class="btn btn-outline-danger btn-sm"
                                data-id="{{ $token->id }}"
                                type="button">
                                <i class="bi bi-trash3"></i>
                            </button></td>
                        </tr>
                    @endforeach
                        <tr @class(['d-none' => 0 !== count($user->tokens)]) id="no-tokens">
                            <td colspan="4">You don't have any API keys!</td>
                        </tr>
                    </tbody>
                    <tfoot>
                        <tr>
                            <td colspan="4">
                                <button class="btn btn-primary"
                                    data-bs-target="#create-token"
                                    data-bs-toggle="modal" type="button">
                                    Create a token
                                </button>
                            </td>
                        </tr>
                    </tfoot>
                </table>
            </div>
        </div>
        <div class="col-lg-1"></div>
    </div>

    <div aria-hidden="true" aria-labelledby="create-token-title"
        class="modal fade" id="create-token" tabindex="-1">
        <div class="modal-dialog">
            <div class="modal-content">
                <form id="api-token-form">
                <div class="modal-header">
                    <h5 class="modal-title" id="create-token-title">
                        Create a new API token
                    </h5>
                    <button aria-label="Close" class="btn-close"
                        data-bs-dismiss="modal" type="button"></button>
                </div>
                <div class="modal-body">
                    <div class="row">
                        <div class="col">
                            <p>
                                API keys should be protected. Anyone that has
                                your API key can do everything you're allowed to
                                do. For example, they can delete any campaigns
                                or characters you've created.
                            </p>
                        </div>
                    </div>
                    <div class="form-row mt-1">
                        <label class="form-label" for="token-name">
                            Token name (required)
                        </label>
                        <div class="col">
                            <input aria-describedBy="token-name-help" autocomplete="off"
                                class="form-control" id="token-name" required
                                type="text">
                            <small id="server-help" class="form-text text-muted">
                                If you're building an app, this might be the
                                name of the app you're building.
                            </small>
                        </div>
                    </div>
                    <div class="form-row mt-1">
                        <label class="form-label" for="token-expiration">
                            Expiration
                        </label>
                        <div class="col">
                            <input aria-describedBy="token-expiration-help"
                                autocomplete="off" class="form-control"
                                id="token-expiration" min="{{ date('Y-m-d') }}"
                                type="date">
                            <small id="token-expiration-help" class="form-text text-muted">
                                Token expiration is not required, but can help
                                keep your data safer by automatically expiring
                                and requiring you to recreate them.
                            </small>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                    <button class="btn btn-primary" disabled id="token-submit"
                        type="submit">
                        Create token
                    </button>
                </div>
                </form>
            </div>
        </div>
    </div>

    <div aria-hidden="true" aria-labelledby="new-token-title" class="modal fade"
        id="token-created" tabindex="-1">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="new-token-title">
                        New token created!
                    </h5>
                    <button aria-label="Close" class="btn-close"
                        data-bs-dismiss="modal" type="button"></button>
                </div>
                <div class="modal-body">
                    <div class="row">
                        <div class="col">
                            <p>
                                <strong>Remember!</strong> API keys should be
                                protected. Anyone that has your API key can do
                                everything you're allowed to do. For example,
                                they can delete any campaigns or characters
                                you've created.
                            </p>
                            <div class="input-group input-group-sm">
                                <span class="input-group-text user-select-all">
                                </span>
                                <button class="btn btn-outline-secondary copy-btn" type="button">
                                    <i class="bi bi-clipboard"></i>
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Done</button>
                </div>
            </div>
        </div>
    </div>

<x-slot name="javascript">
    <script>
        const csrfToken = '{{ csrf_token() }}';

        $('.copy-btn').on('click', function (e) {
            if (!window.navigator.clipboard) {
                // Clipboard API not available
                return;
            }
            const text = $(e.target).parents('div')
                .first()
                .children('.user-select-all')
                .text()
                .trim();
            try {
                navigator.clipboard.writeText(text);
            } catch (err) {
                console.error('Failed to copy!', err);
            }
        });

        $('#token-name').on('keyup', function (e) {
            e = $(e.target);
            $('#token-submit').prop('disabled', '' === e.val().trim());
        });

        $('#api-token-form').on('submit', function (e) {
            e.preventDefault();
            $('#token-submit').prop('disabled', true);
            const settings = {
                accept: 'application/json',
                data: {
                    _token: csrfToken,
                    expires_at: $('#token-expiration').val(),
                    name: $('#token-name').val()
                },
                method: 'POST',
                url: '/api/users/' + {{ $user->id }} + '/token'
            };
            $.ajax(settings)
                .done(function (data) {
                    let expires = data.expires_at;
                    if (null !== expires) {
                        expires = expires.split('T')[0];
                    } else {
                        expires = '';
                    }
                    bootstrap.Modal.getInstance('#create-token').hide();
                    $('#token-created .user-select-all').text(data.plainText);
                    const created = new bootstrap.Modal('#token-created');
                    created.show();

                    $('#token-expiration').val('');
                    $('#token-name').val('');
                    $('#api-keys-table tbody').append(
                        '<tr><td>' + data.name + '</td>' +
                        '<td>' + expires + '</td>' +
                        '<td>&nbsp;</td>' +
                        '<td><button class="btn btn-outline-danger btn-sm" ' +
                        'data-id="' + data.id + '" type="button">' +
                        '<i class="bi bi-trash3"></i></button></td>' +
                        '</tr>'
                    );
                    $('#no-tokens').addClass('d-none');
                })
                .fail(function (data) { window.console.log(data); });
        });

        $('#api-keys-table').on('click', '.btn-outline-danger', function (e) {
            e = $(e.target);
            if ('I' === e[0].nodeName) {
                e = e.parent();
            }
            const token = e.data('id');
            const settings = {
                accept: 'application/json',
                data: { _token: csrfToken },
                method: 'DELETE',
                url: '/api/users/' + {{ $user->id }} + '/token/' + token
            };
            $.ajax(settings)
                .done(function () {
                    e.parents('TR').remove();
                    if (3 === $('tr').length) {
                        $('#no-tokens').removeClass('d-none');
                    }
                })
                .fail(function (data) { window.console.log(data); });
        });
    </script>
</x-slot>
</x-app>
