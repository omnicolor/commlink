<x-app>
    <x-slot name="title">{{ $character->name }}</x-slot>
    <x-slot name="head">
        <style>
            .attribute-card {
                position: relative;
            }
            .attribute-label {
                margin-bottom: -1.25rem;
            }
            .circle {
                background-color: #ffffff;
                border: 3px dashed #dee2e6;
                border-radius: 50%;
                height: 3em;
                position: absolute;
                right: .5em;
                top: -1.5em;
                width: 3em;
            }
            .computed-trait {
                background-position: center;
                background-repeat: no-repeat;
            }
            .rotated {
                display: inline-block;
                transform: scale(-1, -1);
            }
            #body {
                background-image: url('/images/Capers/body.png');
            }
            #current-hits {
                background-image: url('/images/Capers/current-hits.png');
            }
            #mind {
                background-image: url('/images/Capers/mind.png');
            }
            #moxie {
                background-image: url('/images/Capers/moxie.png');
            }
            #speed {
                background-image: url('/images/Capers/speed.png');
            }
        </style>
    </x-slot>

    @includeWhen($creating ?? false, 'Capers.create-navigation')

    <div class="row my-4">
        <div class="col-1"></div>
        <div class="col"><img alt="Capers" src="/images/Capers/capers.png"></div>
        <div class="border-bottom col mb-1">
            <div class="fs-2">{{ $character }}</div>
            <div class="attribute-label"><small class="text-muted">Name</small></div>
        </div>
        <div class="border-bottom col-1 mb-1">
            <div class="fs-2">{{ $character->level ?? 1 }}</div>
            <div class="attribute-label"><small class="text-muted">Level</small></div>
        </div>
        <div class="col-1"></div>
    </div>

    <div class="row my-4"></div>

    <div class="row mt-4">
        <div class="col-1"></div>
        <div class="attribute-card border col d-flex mx-1 rounded"
            title="Charisma is your character’s personality and force of will. It defines how your character interacts with others, regardless of whether they’re telling the truth or lying. It also governs their maximum Hits."
            data-bs-toggle="tooltip">
            <div class="circle d-flex float-end">
                <div class="align-self-center flex-fill text-center">
                    {{ $character->getTraitDefense('charisma') }}
                </div>
            </div>
            <div>C</div>
            <div class="align-self-center flex-fill fs-1 text-center">
                {{ $character->charisma ?? '?' }}
            </div>
            <div class="align-self-end"><span class="rotated">C</span></div>
        </div>
        <div class="attribute-card border col d-flex mx-1 rounded"
            title="Agility is your character’s physical dexterity. It governs both their flexibility and hand-eye coordination. It comes into play with ranged combat, as well as tumbling, balancing, and sleight of hand. It also governs their Body score."
            data-bs-toggle="tooltip">
            <div class="circle d-flex float-end">
                <div class="align-self-center flex-fill text-center">
                    {{ $character->getTraitDefense('agility') }}
                </div>
            </div>
            <div>A</div>
            <div class="align-self-center flex-fill fs-1 text-center">
                {{ $character->agility ?? '?' }}
            </div>
            <div class="align-self-end"><span class="rotated">A</span></div>
        </div>
        <div class="attribute-card border col d-flex mx-1 rounded"
            title="Perception is your character’s ability to take in everything around them. It covers sight, hearing, all the other senses, and your character’s ability to read others’ motives, strengths, and weaknesses. It also governs their Mind score."
            data-bs-toggle="tooltip">
            <div class="circle d-flex float-end">
                <div class="align-self-center flex-fill text-center">
                    {{ $character->getTraitDefense('perception') }}
                </div>
            </div>
            <div>P</div>
            <div class="align-self-center flex-fill fs-1 text-center">
                {{ $character->perception ?? '?' }}
            </div>
            <div class="align-self-end"><span class="rotated">P</span></div>
        </div>
        <div class="attribute-card border col d-flex mx-1 rounded"
            title="Expertise is your character’s book smarts, intelligence, and ability to draw conclusions in a logical manner. It covers many areas of academia. It also governs the number of Skills they have."
            data-bs-toggle="tooltip">
            <div class="circle d-flex float-end">
                <div class="align-self-center flex-fill text-center">
                    {{ $character->getTraitDefense('expertise') }}
                </div>
            </div>
            <div>E</div>
            <div class="align-self-center flex-fill fs-1 text-center">
                {{ $character->expertise ?? '?' }}
            </div>
            <div class="align-self-end"><span class="rotated">E</span></div>
        </div>
        <div class="attribute-card border col d-flex mx-1 rounded"
            title="Resilience is your character’s physical toughness. It defines how your character resists physical pain as well as mental exhaustion. It also governs their maximum Hits."
            data-bs-toggle="tooltip">
            <div class="circle d-flex float-end">
                <div class="align-self-center flex-fill text-center">
                    {{ $character->getTraitDefense('resilience') }}
                </div>
            </div>
            <div>R</div>
            <div class="align-self-center flex-fill fs-1 text-center">
                {{ $character->resilience ?? '?' }}
            </div>
            <div class="align-self-end"><span class="rotated">R</span></div>
        </div>
        <div class="attribute-card border col d-flex mx-1 rounded"
            title="Strength covers your character’s raw physical power. It comes into play in melee combat and in performing feats of strength, such as jumping, climbing, lifting, and swimming."
            data-bs-toggle="tooltip">
            <div class="circle d-flex float-end">
                <div class="align-self-center flex-fill text-center">
                    {{ $character->getTraitDefense('strength') }}
                </div>
            </div>
            <div>S</div>
            <div class="align-self-center flex-fill fs-1 text-center">
                {{ $character->strength ?? '?' }}
            </div>
            <div class="align-self-end"><span class="rotated">S</span></div>
        </div>
        <div class="border col ms-3 rounded">
            <ul>
            @forelse ($character->skills as $skill)
                <li data-bs-toggle="tooltip" title="{{ $skill->description }}">
                    {{ (string)$skill }}
                </li>
            @empty
                <li>No skills chosen.</li>
            @endforelse
            </ul>
        </div>
        <div class="col-1"></div>
    </div>
    <div class="row mb-4">
        <div class="col-1"></div>
        <div class="col fw-bold text-center">Charisma</div>
        <div class="col fw-bold text-center">Agility</div>
        <div class="col fw-bold text-center">Perception</div>
        <div class="col fw-bold text-center">Expertise</div>
        <div class="col fw-bold text-center">Resilience</div>
        <div class="col fw-bold text-center">Strength</div>
        <div class="col fw-bold text-center">Skills</div>
        <div class="col-1"></div>
    </div>

    <div class="row">
        <div class="col-1"></div>
        <div class="col-3 me-2">
            <div class="row">
                <div class="bg-light p-2 rounded">
                    <div class="row">
                        <div class="col text-center">
                            <h5>Character</h5>
                        </div>
                    </div>
                    <div class="row m-2">
                        <div class="bg-white border col rounded">
                            {{ $character->description }}
                            <div class="fw-bold mt-2 text-center">Description</div>
                        </div>
                    </div>
                    <div class="row m-2">
                        <div class="bg-white border col rounded">
                            {{ $character->background ?? 'No background supplied.' }}
                            <div class="fw-bold mt-2 text-center">Background</div>
                        </div>
                    </div>
                    <div class="row m-2">
                        <div class="bg-white border col rounded">
                            {{ $character->mannerisms ?? 'No mannerisms known.' }}
                            <div class="fw-bold mt-2 text-center">Mannerisms</div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="row mt-2">
                <div class="bg-light p-2 rounded">
                    <div class="row">
                        <div class="col text-center">
                            <h5>Anchors</h5>
                        </div>
                    </div>
                    <div class="row m-2">
                        <div class="bg-white border col rounded">
                            @if (null !== $character->identity)
                            <span class="fw-bold">{{ $character->identity }}:</span>
                            {{ $character->identity->description }}
                            @else
                            No identity chosen.
                            @endif
                            <div class="fw-bold mt-2 text-center">Identity</div>
                        </div>
                    </div>
                    <div class="row m-2">
                        <div class="bg-white border col rounded">
                            @if (null !== $character->virtue)
                            <span class="fw-bold">{{ $character->virtue }}:</span>
                            {{ $character->virtue->description }}
                            @else
                            No virtue chosen.
                            @endif
                            <div class="fw-bold mt-2 text-center">Virtue</div>
                        </div>
                    </div>
                    <div class="row m-2">
                        <div class="bg-white border col rounded">
                            @if (null !== $character->vice)
                            <span class="fw-bold">{{ $character->vice }}:</span>
                            {{ $character->vice->description }}
                            @else
                            No vice chosen.
                            @endif
                            <div class="fw-bold mt-2 text-center">Vice</div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="row mt-2">
                <div class="col text-center">
                    <h5>Damage by Suit/Color</h5>
                </div>
            </div>
            <div class="row mb-5">
                <div class="border col p-3 rounded text-center">
                    <div class="row">
                        <div class="col-1"></div>
                        <div class="border-bottom col">♠ : 4</div>
                        <div class="border-bottom col">♥ : 3</div>
                        <div class="border-bottom col">♦ : 2</div>
                        <div class="border-bottom col">♣ : 1</div>
                        <div class="col-1"></div>
                    </div>
                    <div class="row">
                        <div class="col-1"></div>
                        <div class="col">Black : 2</div>
                        <div class="col">Red : 1</div>
                        <div class="col-1"></div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-7">
            <div class="row">
                <div class="border col-4 mx-1 rounded"
                    title="Your character’s Hits score represents how hard your character is to take down. It’s a combination of physical toughness, sheer willpower to keep going while hurt, and a little bit of luck. If a character’s Hits drops to 0, they fall unconscious and might die."
                    data-bs-toggle="tooltip">
                    <div class="row">
                        <div class="border-end col-4 my-1 text-center">
                            <div class="fs-2">{{ $character->maximum_hits }}</div>
                            <div class="fw-bold">Max<br>hits</div>
                        </div>
                        <div class="col my-1 text-center">
                            <div class="computed-trait fs-2" id="current-hits">{{ $character->current_hits ?? '?' }}</div>
                            <div class="fw-bold">Current<br>hits</div>
                        </div>
                    </div>
                </div>
                <div class="border col mx-1 rounded text-center"
                    title="Your character’s Body score represents how hard it is to hit them in physical combat."
                    data-bs-toggle="tooltip">
                    <div class="computed-trait fs-2" id="body">{{ $character->body }}</div>
                    <div class="fw-bold">Body</div>
                </div>
                <div class="border col mx-1 rounded text-center"
                    title="Your character’s Mind score represents how difficult it is to affect their mind."
                    data-bs-toggle="tooltip">
                    <div class="computed-trait fs-2" id="mind">{{ $character->mind }}</div>
                    <div class="fw-bold">Mind</div>
                </div>
                <div class="border col mx-1 rounded text-center"
                    title="This score represents how far your character can move, in feet, in one round."
                    data-bs-toggle="tooltip">
                    <div class="computed-trait fs-2" id="speed">{{ $character->speed }}</div>
                    <div class="fw-bold">Speed</div>
                </div>
                <div class="border col mx-1 rounded text-center"
                    title="Moxie represents added effort and determination people can exert when they really need to succeed."
                    data-bs-toggle="tooltip">
                    <div class="computed-trait fs-2" id="moxie">{{ $character->moxie ?? '?' }}</div>
                    <div class="fw-bold">Moxie</div>
                </div>
            </div>
            <div class="row mt-2">
                <div class="border col-4 mx-1 pb-5 rounded">
                    <ul>
                        @forelse ($character->gear as $item)
                            <li>
                                {{ $item }}
                                @if ($item->quantity > 1)
                                    (&times; {{ $item->quantity }})
                                @endif
                            </li>
                        @empty
                            <li>No gear</li>
                        @endforelse
                    </ul>
                </div>
                <div class="border col mx-1 px-0 pb-5 rounded">
                    <table style="width:100%">
                        <thead>
                            <tr class="border-bottom text-muted">
                                <th class="ps-2" scope="col">Weapon</th>
                                <th scope="col">Damage</th>
                                <th scope="col">Range</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr>
                                <td class="ps-2">Fists</td>
                                <td>Color</td>
                                <td>&mdash;</td>
                            </tr>
                            <tr>
                                <td class="ps-2">Brass knuckles</td>
                                <td>Color + 1</td>
                                <td>&mdash;</td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
            <div class="row" style="margin-top: -1.75rem">
                <div class="col-4 fw-bold text-center">Gear</div>
                <div class="col fw-bold text-center">Weapons</div>
            </div>

            <div class="row mt-3">
                <div class="bg-light col p-3 rounded">
                    <div class="row">
                        <div class="col text-center">
                            <h5>
                                @if ('caper' === $character->type)
                                Powers | Trem-Gear
                                @elseif ('exceptional' === $character->type)
                                Perks | Trem-Gear
                                @else
                                Trem-Gear
                                @endif
                            </h5>
                        </div>
                    </div>
                    @foreach ($character->powers as $power)
                    <div class="row my-2">
                        <div class="bg-white col mx-1">
                            <div class="row">
                                <div class="col-6"><strong>Name:</strong> {{ $power }}</div>
                                <div class="col-3">Type: {{ $power->type }}</div>
                                <div class="col-3">Rank: {{ $power->rank }}</div>
                            </div>
                            <p class="my-2">{{ $power->description }}</p>
                            <div class="fw-bold mt-2 text-center">Description</div>
                        </div>
                    </div>
                    <div class="row my-1">
                        <div class="bg-white col mx-1 pb-4">{{ $power->activation }}</div>
                        <div class="bg-white col mx-1 pb-4">{{ $power->target }}</div>
                        <div class="bg-white col mx-1 pb-4">{{ $power->range }}</div>
                        <div class="bg-white col mx-1 pb-4">{{ $power->duration }}</div>
                    </div>
                    <div class="row mb-2" style="margin-top: -1.75rem">
                        <div class="col fw-bold mx-1 text-center">Activation</div>
                        <div class="col fw-bold mx-1 text-center">Target</div>
                        <div class="col fw-bold mx-1 text-center">Range</div>
                        <div class="col fw-bold mx-1 text-center">Duration</div>
                    </div>
                    <div class="row my-1">
                        <div class="bg-white col mx-1">
                            <p class="my-2">{{ $power->effect }}</p>
                            <div class="fw-bold mt-2 text-center">Effect</div>
                        </div>
                    </div>
                    <div class="row my-1">
                        <div class="bg-white col mx-1">
                            @foreach ($power->boosts as $boost)
                                <span data-bs-toggle="tooltip" title="{{ $boost->description }}">
                                    {{ $boost }}<br>
                                </span>
                            @endforeach
                            <div class="fw-bold mt-2 text-center">Boosts</div>
                        </div>
                    </div>
                    @endforeach
                </div>
            </div>
        </div>
        <div class="col-1"></div>
    </div>

    @if ($creating ?? false)
    <form action="{{ route('capers.create-save') }}" id="form" method="POST">
    @csrf
    <div class="my-1 row">
        <div class="col-1"></div>
        <div class="col text-end">
            <a class="btn btn-secondary" href="{{ route('capers.create-gear') }}">
                Previous: Gear
            </a>
            <button class="btn btn-success" name="nav" type="submit"
                value="save">
                Save character
            </button>
        </div>
        <div class="col-1"></div>
    </div>
    </form>
    @endif

    <x-slot name="javascript">
        <script>
            const tooltipTriggerList = [].slice.call(
                document.querySelectorAll('[data-bs-toggle="tooltip"]')
            );
            tooltipTriggerList.map(function (tooltipTriggerEl) {
                return new bootstrap.Tooltip(tooltipTriggerEl);
            });
        </script>
    </x-slot>
</x-app>
