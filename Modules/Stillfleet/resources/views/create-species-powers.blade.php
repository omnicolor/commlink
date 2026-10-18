<x-app>
    <x-slot name="title">Create character: Species powers</x-slot>
    @include('stillfleet::create-navigation')

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
        <div class="col"><form action="{{ route('stillfleet.create-species-powers') }}" method="POST">
            @csrf
            <h1>{{ $character->species }} powers</h1>

            <br>

            @if (0 !== count($character->species->species_powers))
            <h2>Species powers:</h2>
            <ul>
                @foreach ($character->species->species_powers as $power)
                    <li>
                        {{ $power }}
                        @can ('view data')
                            &mdash; {{ $power->description }}
                        @endcan
                    </li>
                @endforeach
            </ul>
            @endif

            <h2>Choose {{ $character->species->powers_choose }}</h2>
            <ul class="list-group" id="choices">
                @foreach ($character->species->optional_powers as $power)
                    <li class="list-group-item">
                        <label class="fw-bold">
                            <input name="powers[]" type="checkbox" value="{{ $power->id }}"
                                   @if(in_array($power->id, $chosen_powers, true)) checked @endif>
                            {{ $power }}
                        </label>
                        @can ('view data')
                            &mdash; {{ $power->description }}
                        @endcan
                        <span class="text-muted">{{ $power->ruleset }}, p{{ $power->page }}</span>
                    </li>
                @endforeach
            </ul>

            <div class="mt-4">
                <button class="btn btn-primary" type="submit">
                    Set species powers
                </button>
            </div>
        </form></div>
        <div class="col-1"></div>
    </div>

    <x-slot name="javascript">
        <script>
            /**
             * Set the disabled property on powers if they've already chosen
             * enough.
             */
            function initializeCheckboxen(choices) {
                if ($('#choices input:checked').length >= choices) {
                    $.each($('#choices input:not(:checked)'), function (index, el) {
                        $(el).attr('disabled', true);
                    });
                }
            }

            $(function () {
                'use strict';
                const choices = {{ $character->species->powers_choose }};
                $('#choices input').on('change', function () {
                    const unchecked = $('#choices input:not(:checked)');
                    if ($('#choices input:checked').length >= choices) {
                        $.each(unchecked, function (index, el) {
                            $(el).attr('disabled', true);
                        });
                    } else {
                        $.each(unchecked, function (index, el) {
                            $(el).attr('disabled', false);
                        });
                    }
                });

                initializeCheckboxen(choices);
            });
        </script>
    </x-slot>
</x-app>
