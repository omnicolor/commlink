<x-app>
    <x-slot name="title">Create character: Talent</x-slot>
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
            <h1>Talent</h1>

            <p>
                Talents are tricks, moves, and minor abilities that give you a
                small edge. They are more specialized than skills and make your
                character unique. Talents are further explained in Chapter 4.
            </p>

            <p>
                When creating a character for Campaign play, you get one talent
                at the start of the game. Your career offers you three talents
                to choose from. You can learn more talents during the course of
                the game, at which point you will have many more talents to
                choose from.
            </p>

            <form action="{{ route('alien.save-talent') }}" method="POST">
                @csrf

                @foreach ($talents as $talent)
                <div class="form-check">
                    <input @if ($talent->id === $chosenTalent) checked @endif
                        class="form-check-input" id="{{ $talent->id }}"
                        name="talent" required type="radio"
                        value="{{ $talent->id }}">
                    @can('view data')
                    <label class="form-check-label fw-bold" for="{{ $talent->id }}">
                        {{ $talent }}
                    </label>
                        ({{ $talent->ruleset }}, p{{ $talent->page }}) &mdash;
                        {{ $talent->description }}
                    @else
                    <label class="form-check-label" for="{{ $talent->id }}">
                        {{ $talent }} ({{ $talent->ruleset }}, p{{ $talent->page }})
                    </label>
                    @endcan
                </div>
                @endforeach
                <button class="btn btn-primary mt-4" id="submit" type="submit">
                    Next: Choose gear
                </button>
            </form>
        </div>
        <div class="col-1"></div>
    </div>

    <x-slot name="javascript">
        <script>
            (function () {
                'use strict';
            })();
        </script>
    </x-slot>
</x-app>
