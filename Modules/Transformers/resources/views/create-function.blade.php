<x-app>
    <x-slot name="title">Create character</x-slot>
    @include('transformers::create-navigation')

    <div class="row">
        <div class="col">
            <h1>Create a new transformer</h1>
        </div>
    </div>

    @if ($errors->any())
        <div class="alert alert-danger">
            <ul>
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <div class="row">
        <div class="col">
            <h2>03. Function</h2>
            <p>
                Function is the programming of the Robot, dictating its
                available Actions. This works similarly to the structure of
                “Class”. You must choose either Warrior, Gunner, Engineer, or
                Scout. Out of the 283 Robots in the Generation 1 (G1)
                Continuity, all of them conform to these four basic Functions,
                although sometimes they will take on a different title, such as
                “Medic” instead of “Engineer”. These titles are arbitrary, and
                are usually just a deviation from the basic name, or drawing
                from a favorite Action such as “Espionage” or “Interceptor”.
                Feel free to give your Robot an appropriate title, but they
                still must conform to the structure of Warrior, Gunner,
                Engineer, or Scout,
            </p>
        </div>
    </div>

    <form action="{{ route('transformers.create-programming') }}" method="post">
    @csrf

    <div class="row">
        <label class="col-1 col-form-label" for="function">Function</label>
        <div class="col">
            <select class="form-control" id="programming" name="programming"
                required>
                <option>Choose function</option>
                @foreach (\Modules\Transformers\Models\Programming::all() as $key => $value)
                    <option @if (null !== $programming && $key === $programming->value) selected @endif
                        value="{{ $key }}">{{ $value->name }}</option>
                @endforeach
            </select>
        </div>
        <div class="col" id="about-function">
            @foreach (\Modules\Transformers\Models\Programming::all() as $key => $value)
                <div class="d-none" id="about-{{ $key }}">
                    {{ $value->description() }}
                </div>
            @endforeach
        </div>
    </div>

    <div class="row mt-1">
        <div class="col">
            <button type="submit" class="btn btn-primary"
                @if ('' === $programming) disabled @endif id="save">
                Save
            </button>
        </div>
    </div>
    </form>

    <x-slot name="javascript">
        <script>
            $(function () {
                $('#programming').on('change', function (e) {
                    $('#about-function div').addClass('d-none');
                    $('#about-' + $(e.target).val()).removeClass('d-none');
                });
                $('#about-' + $('#programming').val()).removeClass('d-none');
            });
        </script>
    </x-slot>
</x-app>
