<x-app>
    <x-slot name="title">Create character: Traits</x-slot>
    @include('capers.create-navigation')

    <form action="{{ route('capers.create-traits') }}" id="form" method="POST"
        @if ($errors->any()) class="was-validated" @endif
        novalidate>
    @csrf
    <div class="my-4 row">
        <div class="col-1"></div>
        <div class="col">
            <h1>Traits</h1>

            <p>
                Six Traits define your character’s core capabilities. They are:
            </p>

            <ul>
                <li>Charisma</li>
                <li>Agility</li>
                <li>Perception</li>
                <li>Expertise</li>
                <li>Resilience</li>
                <li>Strength</li>
            </ul>

            <p>
                You use these Traits to have your character attempt to do things
                in the game. More information on each can be found in Chapter 3.
            </p>

            <p>
                Traits are rated between 1 and 3. The maximum for a non-Caper is
                3. Capers can gain a Trait rating of 4 or 5, but only by taking
                the appropriate Power.
            </p>

            <p>
                Assign scores to your Traits as follows. Higher is better.
            </p>

            <ul>
                <li>One Trait with a score of 1.</li>
                <li>One Trait with a score of 3.</li>
                <li>The remaining four Traits will have a score of 2.</li>
            </ul>
        </div>
        <div class="col-1"></div>
    </div>

    <div class="my-4 row"></div>

    <div class="my-4 row">
        <div class="col-1"></div>
        <div class="col">
            <div class="my-2 row">
                <label for="trait-high" class="col col-form-label">
                    Score 3 trait
                </label>
                <div class="col">
                    <select class="form-control" id="trait-high"
                        name="trait-high" required>
                        <option value="">Choose high trait</option>
                        <option @if ($traitHigh === 'charisma') selected @endif value="charisma">Charisma</option>
                        <option @if ($traitHigh === 'agility') selected @endif value="agility">Agility</option>
                        <option @if ($traitHigh === 'preception') selected @endif value="perception">Perception</option>
                        <option @if ($traitHigh === 'expertise') selected @endif value="expertise">Expertise</option>
                        <option @if ($traitHigh === 'resilience') selected @endif value="resilience">Resilience</option>
                        <option @if ($traitHigh === 'strength') selected @endif value="strength">Strength</option>
                    </select>
                </div>
            </div>
            <div class="my-2 row">
                <label for="trait-low" class="col col-form-label">
                    Score 1 trait
                </label>
                <div class="col">
                    <select class="form-control" id="trait-low" name="trait-low"
                        required>
                        <option value="">Choose low trait</option>
                        <option @if ($traitLow === 'charisma') selected @endif value="charisma">Charisma</option>
                        <option @if ($traitLow === 'agility') selected @endif value="agility">Agility</option>
                        <option @if ($traitLow === 'perception') selected @endif value="perception">Perception</option>
                        <option @if ($traitLow === 'expertise') selected @endif value="expertise">Expertise</option>
                        <option @if ($traitLow === 'resilience') selected @endif value="resilience">Resilience</option>
                        <option @if ($traitLow === 'strenght') selected @endif value="strength">Strength</option>
                    </select>
                </div>
            </div>
        </div>
        <div class="col">
            <ul id="attributes">
                <li>Charisma: <span id="charisma">
                    {{ 2 + (int)($traitHigh === 'charisma') - (int)($traitLow === 'charisma') }}
                </span></li>
                <li>Agility: <span id="agility">
                    {{ 2 + (int)($traitHigh === 'agility') - (int)($traitLow === 'agility') }}
                </span></li>
                <li>Perception: <span id="perception">
                    {{ 2 + (int)($traitHigh === 'perception') - (int)($traitLow === 'perception') }}
                </span></li>
                <li>Expertise: <span id="expertise">
                    {{ 2 + (int)($traitHigh === 'expertise') - (int)($traitLow === 'expertise') }}
                </span></li>
                <li>Resilience: <span id="resilience">
                    {{ 2 + (int)($traitHigh === 'resilience') - (int)($traitLow === 'resilience') }}
                </span></li>
                <li>Strength: <span id="strength">
                    {{ 2 + (int)($traitHigh === 'strength') - (int)($traitLow === 'strength') }}
                </span></li>
            </ul>
        </div>
        <div class="col-1"></div>
    </div>

    <div class="my-1 row">
        <div class="col-1"></div>
        <div class="col text-end">
            <button class="btn btn-secondary" name="nav" type="submit"
                value="anchors">
                Previous: Anchors
            </button>
            <button class="btn btn-primary" name="nav" type="submit"
                value="skills">
                Next: Skills
            </button>
        </div>
        <div class="col-1"></div>
    </div>
    </form>

    <x-slot name="javascript">
        <script>
			(function () {
				'use strict';

                function traitsAreEqual() {
                    return $('#trait-high').val() === $('#trait-low').val();
                }

                function updateAttributes() {
                    const high = $('#trait-high').val();
                    const low = $('#trait-low').val();
                    $('#attributes span').html('2');
                    if (high) {
                        $('#' + high).html('3');
                    }
                    if (low) {
                        $('#' + low).html('1');
                    }
                }

                const tooltipTriggerList = [].slice.call(
                    document.querySelectorAll('[data-bs-toggle="tooltip"]')
                );
                tooltipTriggerList.map(function (tooltipTriggerEl) {
                    return new bootstrap.Tooltip(tooltipTriggerEl);
                });

                $('#trait-high').on('change', function (event) {
                    if (traitsAreEqual()) {
                        $('#trait-low')[0].selectedIndex = 0;
                    }
                    updateAttributes();
                });

                $('#trait-low').on('change', function (event) {
                    if (traitsAreEqual()) {
                        $('#trait-high')[0].selectedIndex = 0;
                    }
                    updateAttributes();
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
