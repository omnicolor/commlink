<div class="card" id="attributes">
    <div class="card-header">attributes</div>
    <ul class="list-group list-group-flush">
        <li class="list-group-item">
            <span data-bs-toggle="tooltip" title="Body measures your physical health and resiliency. It affects how much damage you can take and stay on your feet, how well you resist damage coming your way, your ability to recover from poisons and diseases, and things of that nature.">
                Body
            </span>
            <div class="value">
                <span id="body-natural">{{ $character->body }}</span>
                <span id="body-augment">
                    {{ $character->getModifiedAttribute('body') - $character->body }}
                </span>
                <span id="body-total">
                    {{ $character->getModifiedAttribute('body') }}
                </span>
            </div>
        </li>
        <li class="list-group-item">
            <span data-bs-toggle="tooltip" title="Agility measures things like hand-eye coordination, flexibility, nimbleness, and balance. Agility is the most important attribute when it comes to scoring hits during combat, as you need to be coordinated to land your blows, whether you’re swinging a sword or carefully aiming a rifle. It also is critical in non-combat situations, such as sneaking quietly past security guards or smoothly lifting a keycard from its secured position.">
                Agility
            </span>
            <div class="value">
                <span id="agility-natural">{{ $character->agility }}</span>
                <span id="agility-augment">
                    {{ $character->getModifiedAttribute('agility') - $character->agility }}
                </span>
                <span id="agility-total">
                    {{ $character->getModifiedAttribute('agility') }}
                </span>
            </div>
        </li>
        <li class="list-group-item">
            <span data-bs-toggle="tooltip" title="Reaction is about reflexes, awareness, and your character’s ability to respond to events happening around them. Reaction plays an important role in deciding how soon characters act in combat and how skilled they are in avoiding attacks from others. It also helps you make that quick turn down a narrow alley on your cycle to avoid the howling gangers on your tail.">
                Reaction
            </span>
            <div class="value">
                <span id="reaction-natural">{{ $character->reaction }}</span>
                <span id="reaction-augment">
                    {{ $character->getModifiedAttribute('reaction') - $character->reaction }}
                </span>
                <span id="reaction-total">
                    {{ $character->getModifiedAttribute('reaction') }}
                </span>
            </div>
        </li>
        <li class="list-group-item">
            <span data-bs-toggle="tooltip" title="Strength is an indicator of, well, how strong your character is. The higher your strength, the more damage you’ll do when you’re raining blows down on an opponent, and the more you’ll be able to move or carry when there’s stuff that needs to be moved. Or carried. Strength is also important with athletic tasks such as climbing, running, and swimming.">
                Strength
            </span>
            <div class="value">
                <span id="strength-natural">{{ $character->strength }}</span>
                <span id="strength-augment">
                    {{ $character->getModifiedAttribute('strength') - $character->strength }}
                </span>
                <span id="strength-total">
                    {{ $character->getModifiedAttribute('strength') }}
                </span>
            </div>
        </li>
        <li class="list-group-item">
            <span data-bs-toggle="tooltip" title="Willpower is your character’s desire to push through adversity, to resist the weariness of spellcasting, and to stay upright after being nailed in the head with a sap. Whether you’re testing yourself against a toxic wilderness or a pack of leather-clad orks with crowbars, Willpower will help you make it through.">
                Willpower
            </span>
            <div class="value">
                <span id="willpower-natural">{{ $character->willpower }}</span>
                <span id="willpower-augment">
                    {{ $character->getModifiedAttribute('willpower') - $character->willpower }}
                </span>
                <span id="willpower-total">
                    {{ $character->getModifiedAttribute('willpower') }}
                </span>
            </div>
        </li>
        <li class="list-group-item">
            <span data-bs-toggle="tooltip" title="The Logic attribute measures the cold, calculating power of your rational mind. Whether you are attempting to repair complicated machinery or patch up an injured teammate, Logic helps you get things right. Logic is also the attribute hermetic mages use to resist Drain from the spells they rain down on their hapless foes. Deckers also find Logic extremely useful, as it helps them develop the attacks and counterattacks that are part of their online battles.">
                Logic
            </span>
            <div class="value">
                <span id="logic-natural">{{ $character->logic }}</span>
                <span id="logic-augment">
                    {{ $character->getModifiedAttribute('logic') - $character->logic }}
                </span>
                <span id="logic-total">
                    {{ $character->getModifiedAttribute('logic') }}
                </span>
            </div>
        </li>
        <li class="list-group-item">
            <span data-bs-toggle="tooltip" title="Intuition is the voice of your gut, the instinct that tells you things before your logical brain can figure them out. Intuition helps you anticipate ambushes, notice that something is amiss or out of place, and stay on the trail of someone you’re pursuing.">
                Intuition
            </span>
            <div class="value">
                <span id="intuition-natural">{{ $character->intuition }}</span>
                <span id="intuition-augment">
                    {{ $character->getModifiedAttribute('intuition') - $character->intuition }}
                </span>
                <span id="intuition-total">
                    {{ $character->getModifiedAttribute('intuition') }}
                </span>
            </div>
        </li>
        <li class="list-group-item">
            <span data-bs-toggle="tooltip" title="Charisma is your force of personality, the persuasiveness and charm you can call on to get people to do what you want without having to go to the trouble of pulling a gun on them. It’s not entirely about your appearance, but it’s also not entirely not about your appearance. What it’s mostly about is how you use what you have—your voice, your face, your words, and all the tools at your disposal—to charm and/or intimidate the people you encounter. Additionally, Charisma is an important attribute for shamanic mages, as it helps them resist the damaging Drain from spells they cast.">
                Charisma
            </span>
            <div class="value">
                <span id="charisma-natural">{{ $character->charisma }}</span>
                <span id="charisma-augment">
                    {{ $character->getModifiedAttribute('charisma') - $character->charisma }}
                </span>
                <span id="charisma-total">
                    {{ $character->getModifiedAttribute('charisma') }}
                </span>
            </div>
        </li>
        <li class="list-group-item">
            <span data-bs-toggle="tooltip" title="Edge is the ultimate intangible, that certain something that provides a boost when you need it, that gets you out of a tough spot when the chips are down. It’s not used to calculate dice pools; instead, you spend a point of Edge to acquire a certain effect. Every character has at least one point of Edge, more if they want to take more frequent advantage of the boosts it offers. The possible effects of and more details about Edge are on p. 56.">
                Edge
            </span>
            <div class="value" id="edge">
                {{ $character->edgeCurrent ?? $character->edge }} /
                {{ $character->edge }}
            </div>
        </li>
        @if (in_array($character->priorities['magic'] ?? '', ['adept', 'aspected', 'magician', 'mystic'], true))
        <li class="list-group-item">
            <span data-bs-toggle="tooltip" title="If you intend to cast spells or use magic in any way, your character needs to have the Magic attribute. Most individuals do not have this attribute, meaning their rating is zero. Mages, who cast spells, and adepts, who channel magic into enhanced physical and mental abilities, need this quality. Their Magic rating measures how capable they are in the arcane arts and how much power they can draw down to help them in their efforts.">
                Magic
                @if (null !== $character->getTradition())
                    ({{ $character->getTradition() }})
                @endif
            </span>
            <div class="value">
                <span id="magic-natural">{{ $character->magic }}</span>
                <span></span>
                <span id="magic-total">
                    {{ $character->getModifiedAttribute('magic') }}
                </span>
            </div>
        </li>
        @endif
        @if (($character->priorities['magic'] ?? '') === 'technomancer')
        <li class="list-group-item">
            <span data-bs-toggle="tooltip" title="Similar to Magic for mages and adepts, Resonance is the special attribute for technomancers. Technomancers interface with the Matrix using the power of their mind, and Resonance measures the strength of their ability to interact with and shape that environment (see Technomancers, p. 249). Non-technomancers have a zero rating for Resonance.">
                Resonance
            </span>
            <div class="value">
                <span id="resonance-natural">{{ $character->resonance }}</span>
                <span></span>
                <span id="resonance-total">
                    {{ $character->getModifiedAttribute('resonance') }}
                </span>
            </div>
        </li>
        @endif
    </ul>
</div>
