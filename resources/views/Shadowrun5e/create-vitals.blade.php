<x-app>
    <x-slot name="title">Create character</x-slot>
    <x-slot name="head">
        <link href="/css/Shadowrun5e/character-generation.css" rel="stylesheet">
    </x-slot>
    @include('Shadowrun5e.create-navigation')

    @if ($errors->any())
        <div class="alert alert-danger mt-4">
            <ul>
                @foreach ($errors->all() as $error)
                <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <form action="{{ route('shadowrun5e.create-vitals') }}" method="POST">
    @csrf

    <div class="row">
        <div class="col-1"></div>
        <div class="col">
            <h1>Vitals</h1>
        </div>
    </div>

    <div class="row mb-2">
        <div class="col-1"></div>
        <label class="col-2 col-form-label" for="handle">
            Handle
        </label>
        <div class="col">
            <input aria-describedby="handle-help" class="form-control"
                id="handle" name="handle" required type="text"
                value="{{ $selected['handle'] }}">
            <small class="form-text text-muted" id="handle-help">
                Character's name to other runners (Fastjack, The
                Smiling Bandit, for example).
            </small>
        </div>
        <div class="col-3"></div>
    </div>
    <div class="row mb-2">
        <div class="col-1"></div>
        <label class="col-2 col-form-label" for="real-name">
            Real name
        </label>
        <div class="col">
            <input aria-describedby="real-name-help" class="col form-control"
                id="real-name" name="real-name" type="text"
                value="{{ $selected['real-name'] }}">
            <small class="form-text text-muted" id="real-name-help">
                What name did your parents give you?
            </small>
        </div>
        <div class="col-3"></div>
    </div>
    <div class="row mb-2">
        <div class="col-1"></div>
        <label class="col-2 col-form-label" for="height">Height</label>
        <div class="col">
            <div class="input-group">
                <input aria-describedby="height-help" class="form-control"
                    id="height" max="2.5" min="0.5" name="height" step="0.01"
                    type="number" value="{{ $selected['height'] }}">
                <div class="input-group-append">
                    <span class="input-group-text">
                        m &asymp;&nbsp;<span id="feet"></span>
                    </span>
                </div>
            </div>
            <small class="form-text text-muted" id="height-help">
                How tall is your character in meters?
            </small>
        </div>
        <div class="col-3"></div>
    </div>
    <div class="row mb-2">
        <div class="col-1"></div>
        <label class="col-2 col-form-label" for="weight">Weight</label>
        <div class="col">
            <div class="input-group">
                <input aria-describedby="weight-help" class="form-control"
                    id="weight" max="300" min="50" name="weight" step="1"
                    type="number" value="{{ $selected['weight'] }}">
                <div class="input-group-append">
                    <span class="input-group-text">
                        kg &asymp;&nbsp;
                        <span id="pounds"></span>&nbsp;lbs
                    </span>
                </div>
            </div>
            <small class="form-text text-muted" id="weight-help">
                How much does your character weigh in kilograms?
            </small>
        </div>
        <div class="col-3"></div>
    </div>
    <div class="row mb-2">
        <div class="col-1"></div>
        <div class="col-2 col-form-label">Gender</div>
        <div class="col">
            <div>
                <div class="form-check form-check-inline">
                    <input aria-describedby="gender-help"
                        class="form-check-input" id="gender-male" name="gender"
                        @if ('male' === $selected['gender'])
                            checked
                        @endif
                        type="radio" value="male">
                    <label class="form-check-label" for="gender-male">
                        Male
                    </label>
                </div>
                <div class="form-check form-check-inline">
                    <input aria-describedby="gender-help"
                        class="form-check-input" id="gender-female"
                        name="gender"
                        @if ('female' === $selected['gender'])
                            checked
                        @endif
                        type="radio" value="female">
                    <label class="form-check-label" for="gender-female">
                        Female
                    </label>
                </div>
                <div class="form-check form-check-inline">
                    <input aria-describedby="gender-help"
                        class="form-check-input" id="gender-other"
                        name="gender"
                        @if ('other' === $selected['gender'])
                            checked
                        @endif
                        type="radio" value="other">
                    <label class="form-check-label" for="gender-other">
                        Other
                    </label>
                </div>
            </div>
            <small class="form-text text-muted" id="gender-help">
                What gender does your character identify as?
            </small>
        </div>
        <div class="col-3"></div>
    </div>
    <div class="row mb-2">
        <div class="col-1"></div>
        <label class="col-2 col-form-label" for="eyes">Eyes</label>
        <div class="col">
            <input aria-describedby="eye-help" class="form-control" id="eyes"
                name="eyes" type="text" value="{{ $selected['eyes'] }}">
            <small class="form-text text-muted" id="eye-help">
                What color are your character's irises, assuming they
                have them?
            </small>
        </div>
        <div class="col-3"></div>
    </div>
    <div class="row mb-2">
        <div class="col-1"></div>
        <label class="col-2 col-form-label" for="hair">Hair</label>
        <div class="col">
            <input aria-describedby="hair-help" class="form-control" id="hair"
                name="hair" type="text" value="{{ $selected['hair'] }}">
            <small class="form-text text-muted" id="hair-help">
                What color and style is your character's hair, assuming
                they have hair?
            </small>
        </div>
        <div class="col-3"></div>
    </div>
    <div class="row mb-2">
        <div class="col-1"></div>
        <label class="col-2 col-form-label" for="birthdate">Birthdate</label>
        <div class="col">
            <div class="input-group">
                <input aria-describedby="birthdate-help" class="form-control"
                    id="birthdate" name="birthdate" type="date"
                    value="{{ $selected['birthdate'] }}">
                <div class="input-group-append" id="years-display"
                    style="display: none;">
                    <span class="input-group-text">
                        <span id="years"></span>&nbsp;years old
                    </span>
                </div>
            </div>
            <small class="form-text text-muted" id="birthdate-help">
                When was your character born? mm/dd/yyyy format.
            </small>
        </div>
        <div class="col-3"></div>
    </div>
    <div class="row mb-4">
        <div class="col-1"></div>
        <label class="col-2 col-form-label" for="birthplace">Birthplace</label>
        <div class="col">
            <input aria-describedby="birthplace-help" class="form-control"
                id="birthplace" name="birthplace" type="text"
                value="{{ $selected['birthplace'] }}">
            <small class="form-text text-muted" id="birthplace-help">
                Where was your character born?
            </small>
        </div>
        <div class="col-3"></div>
    </div>

    @include('Shadowrun5e.create-next')
    </form>

    @include('Shadowrun5e.create-points', ['hide' => true])

    <x-slot name="javascript">
        <script>
        @if ($startDate)
            const campaignStartDate = new Date('{{ $startDate }}');
        @endif
            let character = @json($character);
        </script>
        <script src="/js/Shadowrun5e/Points.js"></script>
        <script src="/js/Shadowrun5e/create-common.js"></script>
        <script src="/js/Shadowrun5e/create-vitals.js"></script>
    </x-slot>
</x-app>
