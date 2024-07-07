<x-app>
    <x-slot name="title">Create character: Values</x-slot>
    @include('subversion::create-navigation')

    @if ($errors->any())
        <div class="my-4 row">
            <div class="col-1"></div>
            <div class="col">
                <div class="alert alert-danger">
                    <ul>
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            </div>
            <div class="col-4"></div>
        </div>
    @endif

    <form action="{{ route('subversion.create-values') }}" id="form"
        method="POST" @if ($errors->any()) class="was-validated" @endif
        novalidate>
    @csrf

    <div class="my-4 row">
        <div class="col-1"></div>
        <div class="col">
            <h1>Values</h1>
            <p>
                In addition to the beliefs that line up with their ideology,
                each character has three additional personal beliefs.
            </p>

            <p>
                PCs may choose Values from the examples below or create their
                own.
            </p>

            <p>
                Values should include “I will” statements. They can be whatever
                you want, but they should have an actionable quality to them so
                it is clear when they are being followed or broken. There is
                usually a belief behind the action-oriented Value, and a player
                is encouraged to explore that! But all that is necessary for
                character creation is the “I will” value itself.
            </p>

            <h2>Corrupted starting value</h2>

            <p>
                Players may pick one corrupted Value (see page 29, core) instead
                of a normal Value at character creation— doing so gives them 5
                bonus Fortune.
            </p>
        </div>
        <div class="col-4"></div>
    </div>

    <div class="my-1 row">
        <div class="col-1"></div>
        <div class="col-2">
            <label class="form-label" for="value1">Value 1</label>
        </div>
        <div class="col">
            <div class="input-group">
                <span class="input-group-text">I will</span>
                <input class="form-control" id="value1" name="value1" required
                    type="text"
                    value="{{ old('value1') ?? $character->values[0] ?? '' }}">
            </div>
            <div class="invalid-feedback">Values are required.</div>
        </div>
        <div class="col-4"></div>
    </div>

    <div class="row">
        <div class="col-1"></div>
        <div class="col-2">
            <label class="form-label" for="value2">Value 2</label>
        </div>
        <div class="col">
            <div class="input-group">
                <span class="input-group-text">I will</span>
                <input class="form-control" id="value2" name="value2" required
                    type="text"
                    value="{{ old('value2') ?? $character->values[1] ?? '' }}">
            </div>
            <div class="invalid-feedback">Values are required.</div>
        </div>
        <div class="col-4"></div>
    </div>

    <div class="my-1 row">
        <div class="col-1"></div>
        <div class="col-2">
            <label class="form-label" for="value3" id="value3-label">Value 3</label>
        </div>
        <div class="col">
            <div class="input-group">
                <span class="input-group-text">I will</span>
                <input class="form-control" id="value3" name="value3" required
                    type="text"
                    value="{{ old('value3') ?? $character->values[2] ?? '' }}">
            </div>
            <div class="invalid-feedback">Values are required.</div>
            <label class="form-label">
                <input autocomplete="off"
                    @if ($character->corrupted_value ?? false) checked @endif
                    class="form-check-input" id="corrupted" name="corrupted"
                    type="checkbox" value="1">
                Corrupted value (+5 fortune)
            </label>
        </div>
        <div class="col-4"></div>
    </div>

    <div class="my-1 row">
        <div class="col-1"></div>
        <div class="col text-end">
            <button class="btn btn-primary" type="submit">
                Next: Impulse
            </button>
        </div>
        <div class="col-4"></div>
    </div>
    </form>

    @include('subversion::create-fortune')

    <x-slot name="javascript">
        <script>
			(function () {
				'use strict';
                const remainingFortune = parseInt($('#fortune-remaining').text());

                $('#form').on('submit', function (event) {
                    form.classList.add('was-validated');
                    if (!form.checkValidity()) {
                        event.preventDefault();
                        event.stopPropagation();
                        return false;
                    }
                });

                $('#corrupted').on('change', function (event) {
                    if ($(event.target).prop('checked')) {
                        $('#fortune-corrupted').html('+5');
                        $('#fortune-remaining').html(remainingFortune + 5);
                        $('#value3-label').html('Corrupted value');
                    } else {
                        $('#fortune-corrupted').html('+0');
                        $('#fortune-remaining').html(remainingFortune);
                        $('#value3-label').html('Value 3');
                    }
                });
            })();
        </script>
    </x-slot>
</x-app>
