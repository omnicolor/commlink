<x-app>
    <x-slot name="title">Create character: Skills</x-slot>
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
            <h1>Skills</h1>

            @if (null === $character->career)
                <div class="alert alert-danger">
                    You haven't chosen a career, so none of your skills can be
                    greater than one.
                </div>
            @endif

            <p>
                Your skills are the knowledge and abilities you have acquired
                during your life. They are important as they determine, along
                with your attributes, how effectively you can perform certain
                actions in the game. There are twelve skills in the game and
                they are all described in detail in Chapter 3. They are measured
                by skill level on a scale from 0 to 5. The higher the number,
                the better.
            </p>

            <p>
                When you create your player character for Campaign play, you
                distribute a total of 10 points amongst your skills. You can
                assign up to three points to each of the skills listed for your
                @if (null === $character->career)
                career.
                @else
                career ({{ implode(', ', $character->career->keySkills) }}).
                @endif
                You can assign a single point each to any other skills
                you choose. You can increase your skill levels during the game
                (see page 36).
            </p>

            <p>
                You have spent <strong id="points"></strong> out of 10 that you
                are required to spend on skills.
            </p>

            <form action="{{ route('alien.save-skills') }}" method="POST">
                @csrf

                @foreach ($skills as $skill)
                <div class="mb-1 row">
                    <label class="col-2 col-form-label" for="{{ $skill->id }}">
                        {{ $skill }}
                    </label>
                    <div class="col-4">
                        <div class="input-group row">
                            <input class="form-control col"
                                data-attribute="{{ $character->{$skill->attribute} }}"
                                id="{{ $skill->id }}" max="1" min="0"
                                name="{{ $skill->id }}" required type="number"
                                value="{{ old($skill->id) ?? $character->skills[$skill->id]->rank ?? 0 }}">
                                <span class="input-group-text col-6">
                                    + {{ $skill->attribute }}
                                    ({{ $character->{$skill->attribute} }})
                                    =&nbsp;<span id="total-{{ $skill->id }}"></span>
                                </span>
                        </div>
                    </div>
                </div>
                @endforeach

                <button class="btn btn-primary" id="submit" type="submit">
                    Next: Choose talent
                </button>
            </form>
        </div>
        <div class="col-1"></div>
    </div>

    <x-slot name="javascript">
        <script>
            (function () {
                'use strict';
                const skills = [
                    'close-combat',
                    'command',
                    'comtech',
                    'heavy-machinery',
                    'manipulation',
                    'medical-aid',
                    'mobility',
                    'observation',
                    'piloting',
                    'ranged-combat',
                    'stamina',
                    'survival',
                ];

                function updateSkillTotal(e) {
                    const el = $(e.target);
                    const rank = parseInt(el.val(), 10);
                    const span = $('#total-' + e.target.id);
                    if (isNaN(rank)) {
                        span.html('');
                        return;
                    }
                    const attribute = parseInt(el.data('attribute'));
                    span.html(rank + attribute);
                }

                function updatePoints() {
                    let points = 0;
                    $.each(skills, function (index, skill) {
                        const value = parseInt($('#' + skill).val());
                        if (isNaN(value)) {
                            return;
                        }
                        points += value;
                    });
                    $('#points').html(points);
                    $('#submit').prop('disabled', points !== 10);
                }

                $.each(skills, function (index, skill) {
                    $('#' + skill).on('change', updateSkillTotal);
                    $('#' + skill).change();
                    $('#' + skill).on('change', updatePoints);
                });
                updatePoints();

                @if (null !== $character->career)
                const careerSkills = [
                    @foreach ($character->career->keySkills as $skill)
                    '{{ $skill->id }}'@if (!$loop->last), @endif
                    @endforeach
                ];
                $.each(careerSkills, function (index, skill) {
                    $('label[for="' + skill + '"]').addClass('fw-bold');
                    $('#' + skill).prop('max', 3);
                });
                @endif
            })();
        </script>
    </x-slot>
</x-app>
