<x-app>
    <x-slot name="title">Create character: Powers</x-slot>
    @include('capers.create-navigation')

    <form action="{{ route('capers.create-powers') }}" id="form" method="POST"
        @if ($errors->any()) class="was-validated" @endif
        novalidate>
    @csrf

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
            <div class="col-1"></div>
        </div>
    @endif

    <div class="my-4 row">
        <div class="col-1"></div>
        <div class="col">
            <h1>Powers</h1>

            <p>
                Many characters in CAPERS are gifted with superhuman abilities
                that allow them to do things others can only dream of. Your
                character might have great strength or superhuman speed, the
                ability to manipulate objects from afar, powers that damage or
                affect others’ minds, a special communion with a specific type
                of energy, or any of a wide variety of other capabilities. These
                abilities are called Powers.
            </p>
        </div>
        <div class="col-1"></div>
    </div>

    <div class="my-4 row">
        <div class="col-1"></div>
        <label class="col-1 col-form-label" for="options">
            Options
        </label>
        <div class="col">
            <select class="form-control" id="options" name="options" required>
                <option value="">Choose number of starting powers</option>
                <option @if ('one-minor' === $options) selected @endif
                    value="one-minor">One Minor Power at rank 2</option>
                <option @if ('two-minor' === $options) selected @endif
                    value="two-minor">Two Minor Powers each at rank 1</option>
                <option @if ('one-major' === $options) selected @endif
                    value="one-major">One Major Power at rank 1</option>
            </select>
            <div class="invalid-feedback">You must choose the number of powers.</div>
        </div>
        <div class="col-1"></div>
    </div>

    <div class="my-4 row minor"
        @if ('one-minor' !== $options && 'two-minor' !== $options)
            style="display:none"
        @endif >
        <div class="col-1"></div>
        <div class="col">
            <h2>Minor powers (Choose <span id="minor-powers">2</span>)</h2>
        </div>
        <div class="col-1"></div>
    </div>

    <div class="mb-4 minor row"
        @if ('one-minor' !== $options && 'two-minor' !== $options)
            style="display:none"
        @endif >
        <div class="col-1"></div>
        @foreach (collect($minor)->chunk((int)(count($minor) / 3)) as $chunk)
        <div class="col">
            @foreach ($chunk as $power)
            <div class="form-check">
                <input class="form-check-input"
                    @if (in_array($power->id, $chosenPowers, true)) checked @endif
                    id="power-{{ $power->id }}" name="powers[]" type="checkbox"
                    value="{{ $power->id }}">
                <label class="form-check-label" data-bs-toggle="tooltip"
                    for="power-{{ $power->id }}"
                    title="{{ $power->description }}">
                    {{ $power }}
                </label>
            </div>
            @endforeach
        </div>
        @endforeach
        <div class="col-1"></div>
    </div>

    <div class="my-4 major row"
        @if ('one-major' !== $options) style="display:none" @endif >
        <div class="col-1"></div>
        <div class="col">
            <h2>Major powers (Choose 1)</h2>
        </div>
        <div class="col-1"></div>
    </div>

    <div class="mb-4 major row"
        @if ('one-major' !== $options) style="display:none" @endif >
        <div class="col-1"></div>
        @foreach (collect($major)->chunk((int)(count($major) / 3)) as $chunk)
        <div class="col">
            @foreach ($chunk as $power)
            <div class="form-check">
                <input class="form-check-input"
                    @if (in_array($power->id, $chosenPowers, true)) checked @endif
                    id="power-{{ $power->id }}" name="powers[]" type="checkbox"
                    value="{{ $power->id }}">
                <label class="form-check-label" data-bs-toggle="tooltip"
                    for="power-{{ $power->id }}"
                    title="{{ $power->description }}">
                    {{ $power }}
                </label>
            </div>
            @endforeach
        </div>
        @endforeach
        <div class="col-1"></div>
    </div>

    <div class="my-4 row">
        <div class="col-1"></div>
        <div class="col text-end">
            <button class="btn btn-secondary" name="nav" type="submit"
                value="skills">
                Previous: Skills
            </button>
            <button class="btn btn-primary" name="nav" type="submit"
                value="boosts">
                Next: Boosts
            </button>
        </div>
        <div class="col-1"></div>
    </div>
    </form>

    <x-slot name="javascript">
        <script>
			(function () {
				'use strict';

                function uncheck(type) {
                    $('.' + type + ' input').prop('checked', false);
                }

                const tooltipTriggerList = [].slice.call(
                    document.querySelectorAll('[data-bs-toggle="tooltip"]')
                );
                tooltipTriggerList.map(function (tooltipTriggerEl) {
                    return new bootstrap.Tooltip(tooltipTriggerEl);
                });

                $('#options').on('change', function (event) {
                    switch ($(event.target).val()) {
                        case 'one-minor':
                            uncheck('major');
                            $('.minor').show();
                            $('.major').hide();
                            $('#minor-powers').html('1');
                            break;
                        case 'two-minor':
                            uncheck('major');
                            $('.minor').show();
                            $('.major').hide();
                            $('#minor-powers').html('2');
                            break;
                        case 'one-major':
                            uncheck('minor');
                            $('.major').show();
                            $('.minor').hide();
                            break;
                    }
                });

                $('#form').on('submit', function (event) {
                    form.classList.add('was-validated');
                    if (!form.checkValidity()) {
                        event.preventDefault();
                        event.stopPropagation();
                        return false;
                    }
                });
            })();
        </script>
    </x-slot>
</x-app>
