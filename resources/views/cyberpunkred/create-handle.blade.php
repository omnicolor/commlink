<x-app>
    <x-slot name="title">Create character</x-slot>
    @include('cyberpunkred.create-navigation')

    <div class="row">
        <div class="col">
            <h1>Name your character</h1>
        </div>
    </div>

    <form action="{{ route('cyberpunkred-create-handle') }}" method="POST">
    @csrf
    <div class="row">
        <div class="col">
            <dl>
                <dt><label for="handle">Handle</label></dt>
                <dd>A nickname; a working name you are known by on The Street.</dd>
            </dl>
        </div>
        <div class="col">
            <input autofocus class="form-control" id="handle" name="handle"
                required type="text" value="{{ $character->handle }}">
        </div>
    </div>
    <div class="row">
        <div class="col"></div>
        <div class="col">
            <button class="btn btn-primary"
                @if (!$character->handle)
                disabled
                @endif
                type="submit">
                Set handle
            </button>
        </div>
    </form>

    <x-slot name="javascript">
    <script>
        $(function () {
            const handle = $('#handle');
            $('#handle').on('keyup', function (e) {
                const btn = $('button');
                const el = $(e.target);
                btn.prop('disabled', '' === el.val());
            });
        });
    </script>
    </x-slot>
</x-app>
