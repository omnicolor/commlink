<x-app>
    <x-slot name="title">Create character</x-slot>
    @include('Transformers.create-navigation')

    <div class="row">
        <div class="col">
            <h1>Create a new transformer</h1>
        </div>
    </div>

    <form action="{{ route('transformers.create-statistics') }}" method="post">
    @csrf

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
            <h2>02. Statistics</h2>
            <p>
                Statistics are a number between 1 and 10 that determine your
                character’s capability in different areas. No Statistic can be
                higher than 10. Roll one ten-sided deice (1d10) for each
                Statistic (or “Stat”) listed below, and record the result on
                your Character Sheet. New rolls are made for the character’s
                Alt.Mode (Alternative Mode), generating different Statistics
                that are similarly between 1 and 10. Transforming between these
                two sets of Statistics (Robot.Mode and Alt.Mode) alters the
                probability of different Actions, which at best have a 90%
                chance of success, and at worst have a 10% chance of success.
            </p>
        </div>
    </div>

    <div class="row">
        <div class="col-1"><strong>Statistic</strong></div>
        <div class="col"><strong>Robot.Mode</strong></div>
        <div class="col-2"><strong>Example uses</strong></div>
    </div>

    <div class="row mt-1">
        <div class="col-1">
            <div class="col-form-label" aria-hidden="true">Strength</div>
        </div>
        <div class="col">
            <label class="visually-hidden">Strength robot mode</label>
            <input class="form-control" id="strength-robot"
                name="strength_robot" readonly value="{{ $strength_robot }}">
        </div>
        <div class="col-2">Lift, Grapple, Throw</div>
    </div>

    <div class="row mt-1">
        <div class="col-1">
            <div class="col-form-label" aria-hidden="true">Intelligence</div>
        </div>
        <div class="col">
            <label class="visually-hidden">Intelligence robot mode</label>
            <input class="form-control" id="intelligence-robot"
                name="intelligence_robot" readonly
                value="{{ $intelligence_robot }}">
        </div>
        <div class="col-2">Processor Speed</div>
    </div>

    <div class="row mt-1">
        <div class="col-1">
            <div class="col-form-label" aria-hidden="true">Speed</div>
        </div>
        <div class="col">
            <label class="visually-hidden">Speed robot mode</label>
            <input class="form-control" id="speed-robot" name="speed_robot"
                readonly value="{{ $speed_robot  }}">
        </div>
        <div class="col-2">Move, React</div>
    </div>

    <div class="row mt-1">
        <div class="col-1">
            <div class="col-form-label" aria-hidden="true">Endurance</div>
        </div>
        <div class="col">
            <label class="visually-hidden">Endurance robot mode</label>
            <input class="form-control" id="endurance-robot"
                name="endurance_robot" readonly
                value="{{ $endurance_robot }}">
        </div>
        <div class="col-2">Hardness</div>
    </div>

    <div class="row mt-1">
        <div class="col-1">
            <div class="col-form-label" aria-hidden="true">Rank</div>
        </div>
        <div class="col">
            <label class="visually-hidden">Rank robot mode</label>
            <input class="form-control" id="rank-robot" name="rank_robot"
                readonly value="{{ $rank_robot }}">
        </div>
        <div class="col-2">Transform</div>
    </div>

    <div class="row mt-1">
        <div class="col-1">
            <div class="col-form-label" aria-hidden="true">Courage</div>
        </div>
        <div class="col">
            <label class="visually-hidden">Courage robot mode</label>
            <input class="form-control" id="courage-robot" name="courage_robot"
                readonly value="{{ $courage_robot }}">
        </div>
        <div class="col-2">Internal Functions</div>
    </div>

    <div class="row mt-1">
        <div class="col-1">
            <div class="col-form-label" aria-hidden="true">Firepower</div>
        </div>
        <div class="col">
            <label class="visually-hidden">Firepower robot mode</label>
            <input class="form-control" id="firepower-robot"
                name="firepower_robot" readonly
                value="{{ $firepower_robot }}">
        </div>
        <div class="col-2">Ranged Attack</div>
    </div>

    <div class="row mt-1">
        <div class="col-1">
            <div class="col-form-label" aria-hidden="true">Skill</div>
        </div>
        <div class="col">
            <label class="visually-hidden">Skill robot mode</label>
            <input class="form-control" id="skill-robot" name="skill_robot"
                readonly value="{{ $skill_robot }}">
        </div>
        <div class="col-2">External Functions</div>
    </div>

    <div class="row mt-1">
        <div class="col">
            <button type="button" class="btn btn-primary" id="roll">
                <i class="bi bi-dice-6-fill"></i>
                Roll statistics
            </button>
            <button type="submit" class="btn btn-primary" disabled id="save">
                Save
            </button>
        </div>
    </div>
    </form>

    <x-slot name="javascript">
        <script>
            function getRandomInt(max) {
                return Math.floor(Math.random() * 10) + 1;
            }

            function isValid() {
                return '' !== $('#strength-robot').val()
                    && '' !== $('#strength-alt').val()
                    && '' !== $('#intelligence-robot').val()
                    && '' !== $('#intelligence-alt').val()
                    && '' !== $('#speed-robot').val()
                    && '' !== $('#speed-alt').val()
                    && '' !== $('#endurance-robot').val()
                    && '' !== $('#endurance-alt').val()
                    && '' !== $('#rank-robot').val()
                    && '' !== $('#rank-alt').val()
                    && '' !== $('#courage-robot').val()
                    && '' !== $('#courage-alt').val()
                    && '' !== $('#firepower-robot').val()
                    && '' !== $('#firepower-alt').val()
                    && '' !== $('#skill-robot').val()
                    && '' !== $('#skill-alt').val();
            }

            $(function () {
                $('#save').prop('disabled', !isValid());
                $('#roll').on('click', function () {
                    $('#strength-robot').val(getRandomInt());
                    $('#strength-alt').val(getRandomInt());
                    $('#intelligence-robot').val(getRandomInt());
                    $('#intelligence-alt').val(getRandomInt());
                    $('#speed-robot').val(getRandomInt());
                    $('#speed-alt').val(getRandomInt());
                    $('#endurance-robot').val(getRandomInt());
                    $('#endurance-alt').val(getRandomInt());
                    $('#rank-robot').val(getRandomInt());
                    $('#rank-alt').val(getRandomInt());
                    $('#courage-robot').val(getRandomInt());
                    $('#courage-alt').val(getRandomInt());
                    $('#firepower-robot').val(getRandomInt());
                    $('#firepower-alt').val(getRandomInt());
                    $('#skill-robot').val(getRandomInt());
                    $('#skill-alt').val(getRandomInt());
                    $('#save').prop('disabled', false);
                });
            });
        </script>
    </x-slot>
</x-app>
