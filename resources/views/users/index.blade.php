@php
use Laravel\Pennant\Feature;
@endphp
<x-app>
    <x-slot name="title">Commlink: Users</x-slot>

    <x-slot name="navbar">
        <li class="nav-item">
            <a class="nav-link" href="/dashboard">Home</a>
        </li>
        <li class="nav-item">
            <span class="nav-link active">User administration</span>
        </li>
    </x-slot>

    <h1>User administration</h1>

    <table class="table table-striped table-header-rotated">
        <thead>
            <tr>
                <th scope="col">Name</th>
                <th scope="col">Email</th>
                <th scope="col">Systems</th>
                <th scope="col">Characters</th>
                <th scope="col">Campaigns</th>
                <th scope="col">Features</th>
                <th scope="col">Roles</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($users as $item)
                @php
                    $characters = $item->characters()->get();
                    $systems = $characters->pluck('system')->unique();
                @endphp
            <tr data-id="{{ $item->id }}">
                <td>{{ $item->name }}</td>
                <td>{{ $item->email }}</td>
                <td>
                    @foreach ($systems as $system)
                        <img alt="{{ $system }}" height="32"
                            src="/images/logos/{{ $system }}.ico">
                    @endforeach
                </td>
                <td>{{ count($characters) }}</td>
                <td>{{ count($item->campaigns) }}</td>
                <td>
                    @foreach ($features as $feature)
                        <div class="form-check">
                            <label class="form-check-label">
                                <input class="form-check-input"
                                    @if (Feature::for($item)->active(get_class($feature)))
                                        checked
                                    @endif
                                    name="features[]" type="checkbox"
                                    value="{{ substr($feature::class, strrpos($feature::class, '\\') + 1) }}">
                                {{ $feature }}
                            </label>
                        </div>
                    @endforeach
                </td>
                <td>
                    @foreach ($roles as $role)
                        <div class="form-check">
                            <label class="form-check-label">
                                <input class="form-check-input"
                                    @if ($item->hasRole($role)) checked @endif
                                    name="roles[]" type="checkbox"
                                    value="{{ $role->id }}">
                                {{ $role->name }}
                            </label>
                        </div>
                    @endforeach
                </td>
            </tr>
        @endforeach
        </tbody>
    </table>

    <x-slot name="javascript">
        <script>
            function patchUser(path, user, value) {
                const settings = {
                    accept: 'application/json-patch+json',
                    data: {
                        _token: csrfToken,
                        patch: [
                            {
                                op: 'replace',
                                path: path,
                                value: value
                            }
                        ]
                    },
                    method: 'PATCH',
                    url: '/api/users/' + user
                };
                $.ajax(settings)
                    .fail(function (data) { window.console.log(data); });
            }

            const csrfToken = '{{ csrf_token() }}';
            $('input[name="features[]"]').on('change', function (e) {
                const el = $(e.target);
                const id = el.parents('tr').data('id');
                patchUser('/features/' + el.val(), id, !!el.prop('checked'));
            });
            $('input[name="roles[]"]').on('change', function (e) {
                const el = $(e.target);
                const id = el.parents('tr').data('id');
                patchUser('/roles/' + el.val(), id, !!el.prop('checked'));
            });
        </script>
    </x-slot>
</x-app>
