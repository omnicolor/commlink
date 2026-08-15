<x-app>
    <x-slot name="title">Create character: Class powers</x-slot>
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
        <div class="col"><form action="{{ route('stillfleet.create-class-powers') }}" method="POST">
            @csrf
            <h1>Class powers</h1>

            <br>

            <h2>Your marquee power is:</h2>

            <ul>
                <li>
                    <strong>{{ $role->marquee_power }}</strong>
                    @can ('view data')
                        &mdash; {{ $role->marquee_power->description }}
                    @endcan
                    <span class="text-muted">
                        {{ $role->marquee_power->ruleset }}, p{{ $role->marquee_power->page }}
                    </span>
                </li>
            </ul>

            @if (0 !== count($role->other_powers))
            <h2>Your other class {{ Str::plural('power', count($role->other_powers)) }}:</h2>
            <ul>
                @foreach ($role->other_powers as $power)
                <li>
                    <strong>{{ $power }}</strong>
                    @can ('view data')
                        &mdash; {{ $power->description }}
                    @endcan
                </li>
                @endforeach
            </ul>
            @endif

            <h2>Choose {{ $choices }}</h2>
            <ul class="list-group" id="choices">
                @foreach ($list as $power)
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

            @if (null !== $choices2)
            <h2 class="mt-4">Choose {{ $choices2 }}</h2>
            <ul class="list-group" id="choices2">
                @foreach ($list2 as $power)
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
            @endif

            <div class="mt-4">
                <button class="btn btn-primary" type="submit">
                    Set class powers
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
            function initializeCheckboxen(choices, choices2) {
                if ($('#choices input:checked').length >= choices) {
                    $.each($('#choices input:not(:checked)'), function (index, el) {
                        $(el).attr('disabled', true);
                    });
                }
                if ($('#choices2 input:checked').length >= choices2) {
                    $.each($('#choices2 input:not(:checked)'), function (index, el) {
                        $(el).attr('disabled', true);
                    });
                }
            }

            $(function () {
                'use strict';

                const choices = {{ $choices }};
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

                const choices2 = {{ $choices2 ?? 0 }};
                $('#choices2 input').on('change', function () {
                    const unchecked = $('#choices2 input:not(:checked)');
                    if ($('#choices2 input:checked').length >= choices2) {
                        $.each(unchecked, function (index, el) {
                            $(el).attr('disabled', true);
                        });
                    } else {
                        $.each(unchecked, function (index, el) {
                            $(el).attr('disabled', false);
                        });
                    }
                });

                initializeCheckboxen(choices, choices2);
            });
        </script>
    </x-slot>
</x-app>
