@php
use Modules\Capers\Models\Skill;
@endphp
<x-app>
    <x-slot name="title">Create character: Skills</x-slot>
    @include('capers::create-navigation')

    <form action="{{ route('capers.create-skills') }}" id="form" method="POST"
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
            <h1>Skills</h1>

            <p>
                Skills define things your character is particularly adept at.
                They might be things your character has studied in depth. They
                might be things your character has a natural affinity for.
                Select for your character a number of Skills equal to 2 plus
                their Expertise score. Following is a list of Skills available.
                More information on each can be found in the Chapter&nbsp;3.
            </p>
        </div>
        <div class="col-1"></div>
    </div>

    <div class="my-4 row">
        <div class="col-1"></div>
        <div class="col">
            You have <span id="remaining">
                {{ $character->expertise + 2 - count($character->skills) }}
            </span> skills remaining.
        </div>
        <div class="col-1"></div>
    </div>

    <div class="my-4 row">
        <div class="col-1"></div>
        @foreach (collect(Skill::all())->chunk(6) as $chunk)
        <div class="col">
            @foreach ($chunk as $skill)
            <div class="form-check">
                <input @if (in_array($skill->id, $skills)) checked @endif
                    class="form-check-input" id="skill-{{ $skill->id }}"
                    name="skills[]" type="checkbox" value="{{ $skill->id }}">
                <label class="form-check-label" data-bs-toggle="tooltip"
                    for="skill-{{ $skill->id }}"
                    title="{{ $skill->description }}">
                    {{ $skill }}
                </label>
            </div>
            @endforeach
        </div>
        @endforeach
        <div class="col-1"></div>
    </div>

    <div class="my-1 row">
        <div class="col-1"></div>
        <div class="col text-end">
            <button class="btn btn-secondary" name="nav" type="submit"
                value="traits">
                Previous: Traits
            </button>
            @if ($character->type === 'exceptional')
            <button class="btn btn-primary" name="nav" type="submit"
                value="perks">
                Next: Perks and trem-gear
            </button>
            @elseif ($character->type === 'caper')
            <button class="btn btn-primary" name="nav" type="submit"
                value="powers">
                Next: Powers
            </button>
            @else
            <button class="btn btn-primary" disabled>
                Next: ???
            </button>
            @endif
        </div>
        <div class="col-1"></div>
    </div>
    </form>

    <x-slot name="javascript">
        <script>
			(function () {
				'use strict';

                const expertise = {{ $character->expertise }};

                const tooltipTriggerList = [].slice.call(
                    document.querySelectorAll('[data-bs-toggle="tooltip"]')
                );
                tooltipTriggerList.map(function (tooltipTriggerEl) {
                    return new bootstrap.Tooltip(tooltipTriggerEl);
                });

                $('input').on('change', function (event) {
                    const checked = $('input:checked');
                    if (checked.length > expertise + 2) {
                        event.target.checked = false;
                        return false;
                    }
                    $('#remaining').html(expertise + 2 - checked.length);
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
