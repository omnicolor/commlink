@php
use Modules\Alien\Models\Armor;
use Modules\Alien\Models\Weapon;
@endphp
<x-app>
    <x-slot name="title">Create character: Gear</x-slot>
    @include('alien::create-navigation')

    <div class="my-4 row">
        <div class="col-1"></div>
        <div class="col">
            <h1>Gear</h1>
            @if ($errors->any())
                @php
                $errors = collect($errors->all())->unique();
                @endphp
                <div class="my-4 row">
                    <div class="col-1"></div>
                    <div class="col">
                        <div class="alert alert-danger">
                            <ul>
                                @foreach ($errors as $error)
                                <li>{{ $error }}</li>
                                @endforeach
                            </ul>
                        </div>
                    </div>
                    <div class="col-1"></div>
                </div>
            @endif

            <p>
                To survive the world of ALIEN, you need the right gear. In
                Campaign play, your career determines what gear you can choose
                from at the start of the game. If you get a weapon, you always
                get two full reloads to go with it. In addition to the items you
                choose, you are assumed to have a uniform or civilian clothing.
                You also get some cash—the career indicates how much. Read more
                about cash and how to spend it in Chapter 7.
            </p>

            <p>
                Choose two of the following:
            </p>

            <form action="{{ route('alien.save-gear') }}" method="POST">
                @csrf

                @foreach ($gear as $key => $choice)
                <div class="mb-3 row">
                    @foreach ($choice as $item)
                    <div class="col">
                        <input class="form-check-input" data-row="{{ $key }}"
                            @if (in_array($item->id, $chosenGear)) checked @endif
                            id="{{ $item->id }}" name="gear[]" type="checkbox"
                            value="{{ $item->id }}">
                        <label class="form-check-label" for="{{ $item->id }}">
                        @if ($item instanceof Weapon)
                        <strong>{{ $item }}</strong> (Weapon)<br>
                        Bonus: {{ $item->bonus }}<br>
                        Damage: {{ $item->damage }}<br>
                        Range: {{ $item->range }}<br>
                        Weight: {{ $item->weight }}<br>
                        Cost: ${{ number_format($item->cost) }}
                        @elseif ($item instanceof Armor)
                        <strong>{{ $item }}</strong> (Armor)<br>
                        Rating: {{ $item->rating }}<br>
                        Air supply: {{ $item->air_supply }}<br>
                        Weight: {{ $item->weight }}<br>
                        Cost: ${{ number_format($item->cost) }}
                        @else
                        <strong>{{ $item }}</strong><br>
                        Effect: {{ $item->effects_text }}<br>
                        Weight: {{ $item->weight }}<br>
                        Cost: ${{ number_format($item->cost) }}
                        @endif
                        </label>
                    </div>
                    @endforeach
                </div>
                @endforeach
                <button class="btn btn-primary mt-4" id="submit" type="submit">
                    Next: Finishing touches
                </button>
            </form>
        </div>
        <div class="col-1"></div>
    </div>

    <x-slot name="javascript">
        <script>
            (function () {
                'use strict';
                function updateForm() {
                    const checked = $('input[type="checkbox"]:checked');
                    // Only two choices are allowed.
                    if (2 === checked.length) {
                        // Lock all checkboxen.
                        $('input[type="checkbox"]').prop('disabled', true);
                        // Except for the ones that are already checked.
                        $('input[type="checkbox"]:checked').prop('disabled', false);

                        // Enable the submit button.
                        $('#submit').prop('disabled', false);
                        return;
                    }

                    // Fewer than two are checked, unlock all.
                    $('input[type="checkbox"]').prop('disabled', false);

                    // Only one choice per row is allowed.
                    const row = $(checked[0]).data('row');
                    // Lock both in the row.
                    $('input[data-row="' + row + '"]').prop('disabled', true);
                    // Unlock the checked one.
                    $('input[data-row="' + row + '"]:checked')
                        .prop('disabled', false);
                    $('#submit').prop('disabled', true);
                }

                $('input[type="checkbox"]').on('change', updateForm);
                updateForm();
            })();
        </script>
    </x-slot>
</x-app>
