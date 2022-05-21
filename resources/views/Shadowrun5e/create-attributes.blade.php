<x-app>
    <x-slot name="title">Create character</x-slot>
    <x-slot name="head">
        <style>
            .points {
                position: fixed;
                right: 0;
                top: 5em;
            }
            .tooltip-inner {
                max-width: 600px;
                text-align: left;
            }
            tr.invalid {
                opacity: .5;
            }
            #points-button {
                position: fixed;
                right: 0;
                top: 5rem;
            }
            .offcanvas {
                border-bottom: 1px solid rgba(0, 0, 0, .2);
                border-top: 1px solid rgba(0, 0, 0, .2);
                bottom: 5rem;
                top: 4.5rem;
                width: 300px;
            }
        </style>
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

    <form action="{{ route('shadowrun5e.create-attributes') }}" method="POST">
    @csrf

    <div class="row">
        <div class="col-1"></div>
        <div class="col">
            <h1>Attributes</h1>
        </div>
    </div>

    <div class="row my-4">
        <div class="col-1"></div>
        <div class="col">
            <p>
                The next step is to raise the character’s attributes. Characters
                begin at their metatype’s starting levels at no cost; so humans
                begin with a Body rating of 1, dwarfs have a starting Body
                rating of 3, orks have an initial rating of 4, and trolls start
                at 5. Characters then apply their attribute points to these
                starting values. It takes 1 attribute point to raise an
                attribute rating by 1.
            </p>

            <p>
                A character must spend all attribute points during character
                creation. They may not spend attribute points from the
                Attributes column to raise special attributes or for any other
                purpose. Characters at character creation may only have 1 Mental
                or Physical attribute at their natural maximum limit; the
                special attributes of Magic, Edge, and Resonance are not
                included in this limitation.
            </p>
        </div>
        <div class="col-3"></div>
    </div>

    <div class="row mb-1">
        <div class="col-1"></div>
        <label class="col col-form-label" data-bs-placement="right"
            data-bs-toggle="tooltip" for="body"
            title="Body measures your physical health and resiliency. It affects how much damage you can take and stay on your feet, how well you resist damage coming your way, your ability to recover from poisons and diseases, and things of that nature.">
            Body
        </label>
        <div class="col">
            <input autofocus class="form-control text-center" id="body" max="6"
                min="1" name="body" required step="1" type="number"
                value="{{ $selected['body'] }}">
            <div class="invalid-feedback">
                You may only have one attribute at your race's natural maximum.
            </div>
        </div>
        <div class="col col-form-label limit">1/6</div>
    </div>
    <div class="row mb-1">
        <div class="col-1"></div>
        <label class="col col-form-label" data-bs-placement="right"
            data-bs-toggle="tooltip" for="agility"
            title="Agility measures things like hand-eye coordination, flexibility, nimbleness, and balance. Agility is the most important attribute when it comes to scoring hits during combat, as you need to be coordinated to land your blows, whether you’re swinging a sword or carefully aiming a rifle. It also is critical in non-combat situations, such as sneaking quietly past security guards or smoothly lifting a keycard from its secured position.">
            Agility
        </label>
        <div class="col">
            <input class="form-control text-center" id="agility" max="6" min="1"
                name="agility" required step="1" type="number"
                value="{{ $selected['agility'] }}">
            <div class="invalid-feedback">
                You may only have one attribute at your race's natural maximum.
            </div>
        </div>
        <div class="col col-form-label limit">1/6</div>
    </div>
    <div class="row mb-1">
        <div class="col-1"></div>
        <label class="col col-form-label" data-bs-placement="right"
            data-bs-toggle="tooltip" for="reaction"
            title="Reaction is about reflexes, awareness, and your character’s ability to respond to events happening around them. Reaction plays an important role in deciding how soon characters act in combat and how skilled they are in avoiding attacks from others. It also helps you make that quick turn down a narrow alley on your cycle to avoid the howling gangers on your tail.">
            Reaction
        </label>
        <div class="col">
            <input class="col form-control text-center" id="reaction" max="6"
                min="1" name="reaction" required step="1" type="number"
                value="{{ $selected['reaction'] }}">
            <div class="invalid-feedback">
                You may only have one attribute at your race's natural maximum.
            </div>
        </div>
        <div class="col col-form-label limit">1/6</div>
    </div>
    <div class="row mb-1">
        <div class="col-1"></div>
        <label class="col col-form-label" data-bs-placement="right"
            data-bs-toggle="tooltip" for="strength"
            title="Strength is an indicator of, well, how strong your character is. The higher your strength, the more damage you’ll do when you’re raining blows down on an opponent, and the more you’ll be able to move or carry when there’s stuff that needs to be moved. Or carried. Strength is also important with athletic tasks such as climbing, running, and swimming.">
            Strength
        </label>
        <div class="col">
            <input class="form-control text-center" id="strength" max="6"
                min="1" name="strength" required step="1" type="number"
                value="{{ $selected['strength'] }}">
            <div class="invalid-feedback">
                You may only have one attribute at your race's natural maximum.
            </div>
        </div>
        <div class="col col-form-label limit">1/6</div>
    </div>
    <div class="row mb-1">
        <div class="col-1"></div>
        <label class="col col-form-label" data-bs-placement="right"
            data-bs-toggle="tooltip" for="willpower"
            title="Willpower is your character’s desire to push through adversity, to resist the weariness of spellcasting, and to stay upright after being nailed in the head with a sap. Whether you’re testing yourself against a toxic wilderness or a pack of leather-clad orks with crowbars, Willpower will help you make it through.">
            Willpower
        </label>
        <div class="col">
            <input class="form-control text-center" id="willpower" max="6"
                min="1" name="willpower" required step="1" type="number"
                value="{{ $selected['willpower'] ?? null }}">
            <div class="invalid-feedback">
                You may only have one attribute at your race's natural maximum.
            </div>
        </div>
        <div class="col col-form-label limit">1/6</div>
    </div>
    <div class="row mb-1">
        <div class="col-1"></div>
        <label class="col col-form-label" data-bs-placement="right"
            data-bs-toggle="tooltip" for="logic"
            title="The Logic attribute measures the cold, calculating power of your rational mind. Whether you are attempting to repair complicated machinery or patch up an injured teammate, Logic helps you get things right. Logic is also the attribute hermetic mages use to resist Drain from the spells they rain down on their hapless foes. Deckers also find Logic extremely useful, as it helps them develop the attacks and counterattacks that are part of their online battles.">
            Logic
        </label>
        <div class="col">
            <input class="form-control text-center" id="logic" max="6" min="1"
                name="logic" required step="1" type="number"
                value="{{ $selected['logic'] ?? null }}">
            <div class="invalid-feedback">
                You may only have one attribute at your race's natural maximum.
            </div>
        </div>
        <div class="col col-form-label limit">1/6</div>
    </div>
    <div class="row mb-1">
        <div class="col-1"></div>
        <label class="col col-form-label" data-bs-placement="right"
            data-bs-toggle="tooltip" for="intuition"
            title="Intuition is the voice of your gut, the instinct that tells you things before your logical brain can figure them out. Intuition helps you anticipate ambushes, notice that something is amiss or out of place, and stay on the trail of someone you’re pursuing.">
            Intuition
        </label>
        <div class="col">
            <input class="form-control text-center" id="intuition" max="6"
                min="1" name="intuition" required step="1" type="number"
                value="{{ $selected['intuition'] ?? null }}">
            <div class="invalid-feedback">
                You may only have one attribute at your race's natural maximum.
            </div>
        </div>
        <div class="col col-form-label limit">1/6</div>
    </div>
    <div class="row mb-1">
        <div class="col-1"></div>
        <label class="col col-form-label" data-bs-placement="right"
            data-bs-toggle="tooltip" for="charisma"
            title="Charisma is your force of personality, the persuasiveness and charm you can call on to get people to do what you want without having to go to the trouble of pulling a gun on them. It’s not entirely about your appearance, but it’s also not entirely not about your appearance. What it’s mostly about is how you use what you have—your voice, your face, your words, and all the tools at your disposal—to charm and/or intimidate the people you encounter. Additionally, Charisma is an important attribute for shamanic mages, as it helps them resist the damaging Drain from spells they cast.">
            Charisma
        </label>
        <div class="col">
            <input class="col form-control text-center" id="charisma" max="6"
                min="1" name="charisma" required step="1" type="number"
                value="{{ $selected['charisma'] }}">
            <div class="invalid-feedback">
                You may only have one attribute at your race's natural maximum.
            </div>
        </div>
        <div class="col col-form-label limit">1/6</div>
    </div>
    <div class="row mb-1">
        <div class="col-1"></div>
        <label class="col col-form-label" data-bs-placement="right"
            data-bs-toggle="tooltip" for="edge"
            title="Edge is the ultimate intangible, that certain something that provides a boost when you need it, that gets you out of a tough spot when the chips are down. It’s not used to calculate dice pools; instead, you spend a point of Edge to acquire a certain effect. Every character has at least one point of Edge, more if they want to take more frequent advantage of the boosts it offers. The possible effects of and more details about Edge are on p. 56.">
            Edge
        </label>
        <div class="col">
            <input class="form-control text-center" id="edge" max="6" min="1"
                name="edge" required step="1" type="number"
                value="{{ $selected['edge'] }}">
        </div>
        <div class="col col-form-label limit">1/6</div>
    </div>
    @if (in_array($character->priorities['magic'] ?? '', ['adept', 'aspected', 'magician', 'mystic'], true))
    <div class="row mb-1">
        <div class="col-1"></div>
        <label class="col col-form-label" data-bs-placement="right"
            data-bs-toggle="tooltip" for="magic"
            title="If you intend to cast spells or use magic in any way, your character needs to have the Magic attribute. Most individuals do not have this attribute, meaning their rating is zero. Mages, who cast spells, and adepts, who channel magic into enhanced physical and mental abilities, need this quality. Their Magic rating measures how capable they are in the arcane arts and how much power they can draw down to help them in their efforts.">
            Magic
        </label>
        <div class="col">
            <input class="form-control text-center" id="magic" max="6" min="1"
                name="magic" required step="1" type="number"
                value="{{ $selected['magic'] }}">
        </div>
        <div class="col col-form-label limit">0/6</div>
    </div>
    @endif
    @if (($character->priorities['magic'] ?? '') === 'technomancer')
    <div class="row mb-1">
        <div class="col-1"></div>
        <label class="col col-form-label" data-bs-placement="right"
            data-bs-toggle="tooltip" for="resonance"
            title="Similar to Magic for mages and adepts, Resonance is the special attribute for technomancers. Technomancers interface with the Matrix using the power of their mind, and Resonance measures the strength of their ability to interact with and shape that environment (see Technomancers, p. 249). Non-technomancers have a zero rating for Resonance.">
            Resonance
        </label>
        <div class="col">
            <input class="form-control text-center" id="resonance" max="6"
                min="1" name="resonance" required step="1" type="number"
                value="{{ $selected['resonance'] }}">
        </div>
        <div class="col col-form-label limit">0/6</div>
    </div>
    @endif

    @include('Shadowrun5e.create-next')
    </form>

    @include('Shadowrun5e.create-points')

    <x-slot name="javascript">
        <script>
            let character = @json($character);
        </script>
        <script src="/js/Shadowrun5e/Points.js"></script>
        <script src="/js/Shadowrun5e/create-common.js"></script>
        <script src="/js/Shadowrun5e/create-attributes.js"></script>
    </x-slot>
</x-app>
