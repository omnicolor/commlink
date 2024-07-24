<x-app>
    <x-slot name="title">Create character: Attributes</x-slot>
    @include('alien::create-navigation')

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
            <h1>Attributes</h1>

            <p>
                When you create your player character for Campaign play, you may
                distribute a total of 14 points across your attributes. You may
                assign no less than 2 and no more than 4 points to any
                attribute. However, you may assign 5 points to the attribute
                listed as the “key attribute” for your career.
            </p>

            @if (null === $character->career)
                <div class="alert alert-danger">
                    You haven't chosen a career, so none of your attributes can
                    be greater than 4.
                </div>
            @else
            <p>
                The key attribute for your career ({{ $career }}) is
                <strong>{{ $career->keyAttribute }}</strong>.
            </p>
            @endif

            <p>
                You have spent <strong id="points"></strong> out of 14 that you
                are required to spend.
            </p>

            <form action="{{ route('alien.save-attributes') }}" method="POST">
                @csrf

                <div class="row">
                    <label class="col-2 col-form-label" for="strength">
                        Strength
                    </label>
                    <div class="col-4">
                        <input aria-describedby="strength-help"
                            class="form-control" id="strength"
                            @if ('strength' === $career?->keyAttribute)
                            max="5"
                            @else
                            max="4"
                            @endif
                            min="2" name="strength" required type="number"
                            value="{{ $strength }}">
                        <div class="invalid-feedback">
                            @if ('strength' === $career?->keyAttribute)
                                Must be between 2 and 5.
                            @else
                                Must be between 2 and 4.
                            @endif
                        </div>
                        <div class="form-text" id="strength-help">
                            Raw muscle power and brawn.
                        </div>
                    </div>
                </div>
                <div class="row">
                    <label class="col-2 col-form-label" for="agility">
                        Agility
                    </label>
                    <div class="col-4">
                        <input aria-describedby="agility-help"
                            class="form-control" id="agility"
                            @if ('agility' === $career?->keyAttribute)
                            max="5"
                            @else
                            max="4"
                            @endif
                            min="2" name="agility" required type="number"
                            value="{{ $agility }}">
                        <div class="form-text" id="agility-help">
                            Body control, speed, and motor skills.
                        </div>
                    </div>
                </div>
                <div class="row">
                    <label class="col-2 col-form-label" for="wits">
                        Wits
                    </label>
                    <div class="col-4">
                        <input aria-describedby="wits-help"
                            class="form-control" id="wits"
                            @if ('wits' === $career?->keyAttribute)
                            max="5"
                            @else
                            max="4"
                            @endif
                            min="2" name="wits" required type="number"
                            value="{{ $wits }}">
                        <div class="form-text" id="wits-help">
                            Sensory perception, intelligence, and sanity.
                        </div>
                    </div>
                </div>
                <div class="row">
                    <label class="col-2 col-form-label" for="empathy">
                        Empathy
                    </label>
                    <div class="col-4">
                        <input aria-describedby="empathy-help"
                            class="form-control" id="empathy"
                            @if ('empathy' === $career?->keyAttribute)
                            max="5"
                            @else
                            max="4"
                            @endif
                            min="2" name="empathy" required type="number"
                            value="{{ $empathy }}">
                        <div class="form-text" id="empathy-help">
                            Personal charisma, empathy, and ability to
                            manipulate others.
                        </div>
                    </div>
                </div>

                <button class="btn btn-primary" id="submit" type="submit">
                    Next: Choose skills
                </button>
            </form>
        </div>
        <div class="col-1"></div>
    </div>

    <x-slot name="javascript">
        <script>
            (function () {
                'use strict';

                function updatePoints() {
                    let points = 0;
                    const attributes = ['agility', 'empathy', 'strength', 'wits'];
                    $.each(attributes, function (index, attribute) {
                        const value = parseInt($('#' + attribute).val());
                        if (isNaN(value)) {
                            return;
                        }
                        points += value;
                    });
                    $('#points').html(points);
                    $('#submit').prop('disabled', points !== 14);
                }

                $('#agility').on('change', updatePoints);
                $('#empathy').on('change', updatePoints);
                $('#strength').on('change', updatePoints);
                $('#wits').on('change', updatePoints);
                updatePoints();
            })();
        </script>
    </x-slot>
</x-app>
