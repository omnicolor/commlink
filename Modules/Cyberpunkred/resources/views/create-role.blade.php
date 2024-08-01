<x-app>
    <x-slot name="title">Create character</x-slot>
    @include('cyberpunkred::create-navigation')

    <div class="row">
        <div class="col">
            <h1>Choose role</h1>
        </div>
    </div>
    <div class="row">
        <div class="col">
            <form action="{{ route('cyberpunkred.create-role') }}" method="POST">
            @csrf
            <div class="row">
                <div class="col">
                    <select class="form-control" id="role" name="role">
                        <option value="">Choose role</option>
                        @foreach ($roles as $role)
                            <option
                                data-ability="{{ $role->abilityDescription }}"
                                data-description="{{ $role->description }}"
                                @if ($chosenRole === (string)$role) selected @endif value="{{ $role }}"
                                >{{ $role }}</option>
                            @endforeach
                    </select>
                </div>
            </div>
            <div class="row mt-3">
                <div class="col">
                    <button class="btn btn-primary"
                        @if (!$chosenRole)
                        disabled
                        @endif
                        type="submit">
                        Set role
                    </button>
                </div>
            </div>
            </form>
        </div>
        <div class="col"><div id="role-info" style="display:none;">
            <div><strong>Description</strong></div>
            <div id="role-desc"></div>
            <div><strong>Role ability</strong></div>
            <div id="ability-desc"></div>
        </div></div>
    </div>

    <x-slot name="javascript">
    <script>
        $(function () {
            const info = $('#role-info');
            $('#role').on('change', function (e) {
                const btn = $('button');
                const el = $(e.target);
                if ('' === el.val()) {
                    info.hide();
                    btn.prop('disabled', true);
                    return;
                }
                const role = $(':selected');
                const description = '<p>' +
                    role.data('description').replace('||', '</p><p>')
                    + '</p>';
                $('#role-desc').html(description);
                $('#ability-desc').html(role.data('ability'));
                btn.prop('disabled', false);
                info.show();
            });
        });
    </script>
    </x-slot>
</x-app>
