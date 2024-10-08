<div class="non-combat">
    <div class="card">
        <div class="card-header">metadata</div>
        <ul class="list-group list-group-flush">
            <li class="list-group-item">
                Alias
                <div class="value" id="handle">
                    {{ $character->handle }}
                </div>
            </li>
            <li class="list-group-item">
                Name
                <div class="value" id="real-name">
                    {{ $character->realName }}
                </div>
            </li>
            <li class="list-group-item">
                Metatype
                <div class="value" id="metatype">
                    {{ ucfirst($character->metatype) }}
                </div>
            </li>
            <li class="list-group-item">
                Total karma
                <div class="value" id="total-karma">
                    {{ $character->karma }}₭
                </div>
            </li>
            <li class="list-group-item">
                Current karma
                <div class="value" id="current-karma">
                    {{ $character->karmaCurrent }}₭
                </div>
            </li>
            <li class="list-group-item">
                Nuyen
                <div class="value" id="nuyen">
                    &yen;{{ number_format($character->nuyen) }}
                </div>
            </li>
            <li class="list-group-item" id="street-cred-li">
                Street cred
                <div class="value" id="street-cred">
                    {{ $character->getModifiedAttribute('streetCred') }}
                </div>
            </li>
            <li class="list-group-item" id="notoriety-li">
                Notoriety
                <div class="value" id="notoriety">
                    {{ $character->getModifiedAttribute('notoriety') }}
                </div>
            </li>
            <li class="list-group-item">
                Public awareness
                <div class="value" id="public-awareness">
                    {{ $character->getModifiedAttribute('publicAwareness') }}
                </div>
            </li>
            <li class="list-group-item">
                Height
                <div class="value" id="height">
                    {{ $character->height }}m
                </div>
            </li>
            <li class="list-group-item">
                Weight
                <div class="value" id="weight">
                    {{ $character->weight }}kg
                </div>
            </li>
            <li class="list-group-item" id="composure">
                @can('view data')
                <span data-bs-toggle="tooltip"
                    title="Some situations are tough to deal with, even for hardened professionals like shadowrunners. When a character is faced with an emotionally overwhelming situation there are only two choices. Stay and fight or turn into a quivering lump of goo. To find out which one happens, make a Willpower + Charisma Test, with a threshold based on the severity of the situation.Take note that repeating similar situations over and again eventually eliminates the need to perform this test. Staring down a group of well-armed gangers will be scary at first, but after a character does it a few times the fear gives way to instinct.">
                    Composure
                </span>
                @else
                    Composure
                @endcan
                <div class="value">
                    {{ $character->composure }}
                </div>
            </li>
            <li class="list-group-item" id="judge-intentions">
                @can('view data')
                <span data-bs-toggle="tooltip"
                    title="Reading another person is also a matter of instinct. A character can use their instincts to guess at the intentions of another person or to gauge how much they can trust someone. Make an Opposed Intuition + Charisma Test against the target's Willpower + Charisma. This is not an exact science. A successful test doesn't mean the target will never betray you (intentions have been known to change), and deceptive characters can gain another's confidence easily. This primarily serves as a benchmark or gut instinct about how much you can trust the person you are dealing with.">
                    Judge intentions
                </span>
                @else
                    Judge intentions
                @endcan
                <div class="value">
                    {{ $character->judge_intentions }}
                </div>
            </li>
            <li class="list-group-item" id="memory">
                @can('view data')
                <span data-bs-toggle="tooltip" data-bs-html="true"
                    title="<p>While there are numerous mnemonic devices, and even a few select pieces of bioware, designed for remembering information, memory is not a skill. If a character needs to recall information make a Logic + Willpower Test. Use the Knowledge Skill Table to determine the threshold. If a character actively tries to memorize information, make a Logic + Willpower Test at the time of memorization. Each hit adds a dice to the Recall Test later on.</p><p>Glitches can have a devastating effect on memory. A glitch means the character misremembers some portion of the information, such as order of numbers in a passcode. A critical glitch means the character has completely fooled himself into believing and thus remembering something that never actually happened.</p>">
                    Memory
                </span>
                @else
                    Memory
                @endcan
                <div class="value">
                    {{ $character->memory }}
                </div>
            </li>
            <li class="list-group-item" id="lift-carry">
                @can('view data')
                <span data-bs-toggle="tooltip" data-bs-html="true"
                    title="<p>The baseline for lifting weight is 15 kilograms per point of Strength. Anything more than that requires a Strength + Body Test. Each hit increases the max weight lifted by 15 kilograms. Lifting weight above your head, as with a clean &amp; jerk, is more difficult. The baseline for lifting weight above the head is 5 kilograms per point Strength. Each hit on the Lifting Test increases the maximum weight you can lift by 5 kilograms.</p><p>Carrying weight is significantly different than lifting weight. Characters can carry Strength x 10 kilograms in gear without effort. Additional weight requires a Lifting Test. Each hit increases the maximum by 10 kilograms. For more details on carrying gear, see Carrying Gear, p. 420.</p>">
                    Lift/carry
                </span>
                @else
                    Lift/carry
                @endcan
                <div class="value">
                    {{ $character->lift_carry }}
                </div>
            </li>
        </ul>
    </div>
</div>
