<x-app>
    <x-slot name="title">{{ $character }}</x-slot>
    <x-slot name="head">
        <style>
            body {
                background: #000000;
            }
            td {
                padding: .2em;
            }
            .box {
                border-bottom-left-radius: 0.5rem;
                border-bottom-right-radius: 0.5rem;
                border-top-left-radius: 0.5rem;
                background-color: #ffffff;
                color: #000000;
                height: 90%;
                padding-left: .7em;
                padding-top: 8px;
                position: relative;
            }
            .legend {
                background-color: #ffffff;
                border-top-right-radius: 0.5rem;
                color: #ffffff;
                font-size: 70%;
                line-height: .9;
                height: 11px;
                margin: 0;
                position: absolute;
                right: 0;
                text-transform: uppercase;
                top: -11px;
                width: 98%;
            }
            .legend div {
                background-color: #000000;
                border-bottom-right-radius: 1.2rem;
                display: inline-block;
                height: 11px;
                padding-right: 1em;
            }
            .tab {
                background-color: #ffffff;
                border-top-left-radius: 0.5rem;
                border-top-right-radius: 0.5rem;
                color: #000000;
                font-size: 70%;
                left: 47%;
                line-height: 1;
                margin: 0 auto;
                padding: 0 1em 0 .8em;
                position: absolute;
                text-transform: uppercase;
                top: -.5em;
            }
            .underlined {
                border-bottom: 1px dashed #cccccc;
            }
            #dehydrated + label,
            #starving + label,
            #exhausted + label,
            #freezing + label,
            #radiation label,
            #stress label {
                color: #ffffff;
            }
            #health .btn-check:checked + .btn {
                background-color: #198754;
                border-color: #198754;
                color: #198754;
            }
            .attribute-rank {
                background-color: #ffffff;
                border: 4px solid #cccccc;
                border-radius: 1.5rem;
                color: #000000;
                font-size: 200%;
                font-weight: 700;
                height: 4rem;
                line-height: 1;
                margin: 0 auto;
                padding-top: .7rem;
                position: relative;
                text-align: center;
                width: 4rem;
                z-index: 2;
            }
            .consumable-amount,
            .skill-rank {
                background-color: #ffffff;
                border: 3px solid #cccccc;
                border-radius: 1.5rem;
                font-size: 200%;
                font-weight: 700;
                height: 3rem;
                line-height: 1;
                margin: 0 auto;
                position: relative;
                text-align: center;
                width: 3rem;
                z-index: 2;
            }
            .consumable-amount div,
            .skill-rank div {
                padding-top: .1em
            }
            .consumable-amount {
                display: inline-block;
                margin-left: -1em;
                margin-top: -.1em;
            }
            .skill-name {
                background-color: #ffffff;
                border-radius: .5rem;
                display: inline-block;
                margin-top: -2em;
                padding: 1em 1em 0 1em;
                z-index: 1;
            }
            .consumable-name {
                background-color: #ffffff;
                border-radius: .5rem;
                display: inline-block;
                padding: .1em 1em .1em 1em;
                width: 40%;
                z-index: 1;
            }
            .column-heads {
                font-size: 70%;
                margin-top: -.7em;
                padding-top: 0;
            }
        </style>
    </x-slot>
    <x-slot name="navbar">
        <li class="nav-item">
            <a class="nav-link" href="{{ route('dashboard') }}">Home</a>
        </li>
        <li class="nav-item">
            <span class="nav-link active">{{ $character }}</span>
        </li>
    </x-slot>
    @includeWhen($creating ?? '' === 'review', 'alien::create-navigation')

    @if (($creating ?? '' === 'review') && 0 !== count($validationErrors))
        <div class="my-4 row">
            <div class="col-1"></div>
            <div class="col">
                <div class="alert alert-danger">
                    <ul>
                        @foreach ($validationErrors as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            </div>
            <div class="col-1"></div>
        </div>
    @endif

    <table width="100%">
        <tr>
            <td width="15%"></td>
            <td width="15%"></td>
            <td width="5%"></td>
            <td></td>
            <td></td>
            <td></td>
            <td></td>
            <td width="10%"></td>
            <td></td>
        </tr>
        <tr style="height:100%">
            <td colspan=3 rowspan=3 style="height:inherit">
                <div class="box">
                    <div class="legend"><div>Personal agenda</div></div>
                    {{ $character->agenda }}
                </div>
            </td>
            <td colspan=5 style="height:6px;"></td>
            <td rowspan=6>
                <div class="box">
                    <div class="legend"><div>Talents</div></div>
                    @foreach ($character->talents as $talent)
                    <div class="underlined">{{ $talent }}</div>
                    @endforeach
                    @for ($i = 0; $i < 4 - count($character->talents); $i++)
                    <div class="underlined">&nbsp;</div>
                    @endfor
                    <div></div>
                </div>
            </td>
        </tr>
        <tr>
            <td colspan=5>
                <div class="box text-center">
                    <div class="tab">Name</div>
                    {{ $character }}
                </div>
            </td>
        </tr>
        <tr>
            <td colspan=5>
                <div class="box text-center">
                    <div class="tab">Career</div>
                    {{ ucfirst($character->career) }}
                </div>
            </td>
        </tr>
        <tr>
            <td style="height:5px"></td>
        </tr>
        <tr style="height:100%">
            <td colspan=3 rowspan=2 style="height:inherit">
                <div class="box pb-2">
                    <div class="legend"><div>Relationships</div></div>
                    <div class="underlined">Buddy: {{ $character->buddy }}</div>
                    Rival: {{ $character->rival }}
                </div>
            </td>
            <td colspan=5 rowspan=2>
                <div class="box text-center">
                    <div class="tab">Appearance</div>
                    {{ $character->appearance }}
                </div>
            </td>
        </tr>
        <tr>
            <td></td>
        </tr>
        <tr>
            <td style="height:5px"></td>
        </tr>
        <tr style="height:100%">
            <td colspan=2 style="height:inherit">
                <div class="box" id="stress" style="height:3em;padding-top:.7em;">
                    <div class="legend"><div>Stress level</div></div>
                        @for ($i = 1; $i <= 10; $i++)
                        <input class="btn-check"
                            @if ($character->stress >= $i) checked @endif
                            id="stress-{{ $i }}" type="checkbox">
                        <label class="btn btn-outline-danger btn-sm" for="stress-{{ $i }}">
                            X
                        </label>
                        @endfor
                </div>
            </td>
            <td></td>
            <td></td>
            <td></td>
            <td class="close-combat text-center" style="position:relative">
                <div class="skill-rank"><div>{{ $character->skills['close-combat']->rank }}</div></div>
                <div class="skill-name">Close combat</div>
            </td>
            <td></td>
            <td colspan=2>
                <div class="box" id="experience" style="height:3em;padding-top:.7em;">
                    <div class="legend"><div>Experience</div></div>
                        @for ($i = 1; $i <= 10; $i++)
                        <input class="btn-check"
                            @if ($character->experience >= $i) checked @endif
                            id="experience-{{ $i }}" type="checkbox">
                        <label class="btn btn-outline-success btn-sm my-1" for="experience-{{ $i }}">
                            X
                        </label>
                        @endfor
                    </div>
                </div>
            </td>
        </tr>
        <tr>
            <td style="height:5px"></td>
        </tr>
        <tr style="height:100%">
            <td colspan=2 style="height:inherit">
                <div class="box" id="health" style="height:3em;padding-top:.7em">
                    <div class="legend"><div>Health</div></div>
                    @for ($i = $character->health_maximum; $i >= 1; $i--)
                    <input class="btn-check" id="health-{{ $i }}"
                        @if ($character->health_current >= $i) checked @endif
                        type="checkbox">
                    <label class="btn btn-outline-success btn-sm" for="health-{{ $i }}">
                        X
                    </label>
                    @endfor
                </div>
            </td>
            <td class="text-end" colspan=3>
                <div class="skill-rank d-inline-block"><div>{{ $character->skills['heavy-machinery']->rank }}</div></div>
                <div class="skill-name d-inline-block" style="margin-left:-1rem;">Heavy machinery</div>
            </td>
            <td class="align-top text-center text-white" rowspan=3>
                Strength
                <div class="attribute-rank">{{ $character->strength }}</div>
            </td>
            <td colspan=2>
                <div class="skill-name d-inline-block">Stamina</div>
                <div class="skill-rank d-inline-block" style="margin-left:-1rem;"><div>{{ $character->skills['stamina']->rank }}</div></div>
            </td>
            <td>
                <div class="box" id="story-points" style="height:3em;padding-top:.7em;">
                    <div class="legend"><div>Story Points</div></div>
                        @for ($i = 1; $i <= 3; $i++)
                        <input class="btn-check"
                            id="story-points-{{ $i }}" type="checkbox">
                        <label class="btn btn-outline-success btn-sm my-1" for="story-points-{{ $i }}">
                            X
                        </label>
                        @endfor
                    </div>
                </div>
            </td>
        </tr>
        <tr>
            <td style="height:1em"></td>
        </tr>
        <tr style="height:100%">
            <td rowspan=2 style="height:inherit">
                <div class="box" id="radiation" style="height:6em;padding-top:.7em;">
                    <div class="legend"><div>Radiation</div></div>
                        @for ($i = 1; $i <= 10; $i++)
                        <input class="btn-check"
                            @if ($character->radiation >= $i) checked @endif
                            id="radiation-{{ $i }}" type="checkbox">
                        <label class="btn btn-outline-danger btn-sm my-1" for="radiation-{{ $i }}">
                            X
                        </label>
                        @if (5 === $i) <br> @endif
                        @endfor
                </div>
            </td>
            <td></td>
            <td colspan=3 class="text-end pe-4">
                <div class="skill-rank d-inline-block"><div>{{ $character->skills['ranged-combat']->rank }}</div></div>
                <div class="skill-name d-inline-block" style="margin-left:-1rem">Ranged combat</div>
            </td>
            <td class="text-start ps-4" colspan=2>
                <div class="skill-name d-inline-block">Observation</div>
                <div class="skill-rank d-inline-block" style="margin-left:-1rem"><div>{{ $character->skills['observation']->rank }}</div></div>
            </td>
            <td rowspan=2 style="height:inherit">
                <div class="box">
                    <div class="legend"><div>Tiny items</div></div>
                </div>
            </td>
        </tr>
        <tr>
            <td></td>
            <td class="text-end" colspan=2>
                <div class="skill-rank d-inline-block"><div>{{ $character->skills['mobility']->rank }}</div></div>
                <div class="skill-name d-inline-block">Mobility</div>
            </td>
            <td class="text-end text-nowrap text-white">
                Agility&nbsp;<div class="attribute-rank d-inline-block">{{ $character->agility }}</div>
            </td>
            <td class="text-white text-center">Attributes</td>
            <td class="text-start text-white">
                <div class="attribute-rank d-inline-block">{{ $character->wits }}</div>
                Wits
            </td>
            <td>
                <div class="skill-name d-inline-block">Survival</div>
                <div class="skill-rank d-inline-block" style="margin-left:-1rem;"><div>{{ $character->skills['survival']->rank }}</div></div>
            </td>
        </tr>
        <tr>
            <td style="height:5px"></td>
        </tr>
        <tr style="height:100%">
            <td colspan=2 rowspan=2 style="height:inherit">
                <div class="box">
                    <div class="legend"><div>Critical Injuries</div></div>
                    @forelse ($character->injuries as $injury)
                        <div class="my-1
                            @if (($loop->first && $loop->last) || !$loop->last)
                                underlined
                            @endif ">{{ $injury }}</div>
                    @empty
                        <div class="my-1 underlined">&nbsp;</div>
                        <div class="my-1">&nbsp;</div>
                    @endforelse
                </div>
            </td>
            <td></td>
            <td class="text-end pe-4" colspan=2>
                <div class="skill-rank d-inline-block"><div>{{ $character->skills['piloting']->rank }}</div></div>
                <div class="skill-name d-inline-block" style="margin-left:-1rem;">Piloting</div>
            </td>
            <td></td>
            <td class="ps-4" colspan=2>
                <div class="skill-name d-inline-block">Comtech</div>
                <div class="skill-rank d-inline-block" style="margin-left:-1rem;"><div>{{ $character->skills['comtech']->rank }}</div></div>
            </td>
            <td style="height:inherit">
                <div class="box">
                    <div class="legend"><div>Signature item</div></div>
                </div>
            </td>
        </tr>
        <tr style="height:0.5em;">
            <td colspan=3></td>
            <td class="text-center text-white" rowspan=2>
                <div class="attribute-rank">{{ $character->empathy }}</div>
                Empathy
            </td>
            <td></td>
            <td></td>
            <td rowspan=7>
                <div class="box">
                    <div class="legend"><div>Gear</div></div>
                    @foreach ($character->gear as $key => $gear)
                    <div class="underlined">{{ $key + 1}}: {{ $gear }}</div>
                    @endforeach
                    @for ($i = 1; $i <= 10 - count($character->gear); $i++)
                    <div class="underlined">{{ $i }}:&nbsp;</div>
                    @endfor
                    <div></div>
                </div>
            </td>
        </tr>
        <tr style="height:100%">
            <td rowspan=3 colspan=2 style="height:inherit">
                <div class="box">
                    <div class="legend"><div>Conditions</div></div>
                    <div class="underlined">Starving</div>
                    <div class="underlined">Dehydrated</div>
                    <div class="underlined">Exhausted</div>
                    <div>Freezing</div>
                </div>
            </td>
            <td></td>
            <td class="text-end" colspan=2>
                <div class="skill-rank d-inline-block"><div>{{ $character->skills['command']->rank }}</div></div>
                <div class="skill-name d-inline-block" style="margin-left:-1rem;">Command</div>
            </td>
            <td class="text-start" colspan=2>
                <div class="skill-name d-inline-block">Medical Aid</div>
                <div class="skill-rank d-inline-block" style="margin-left:-1rem;"><div>{{ $character->skills['medical-aid']->rank }}</div></div>
            </td>
        </tr>
        <tr>
            <td></td>
            <td></td>
            <td></td>
            <td class="text-center pt-3" rowspan=2>
                <div class="skill-name pb-3 pt-0">Manipulation</div>
                <div class="skill-rank" style="margin-top:-.3em;"><div>{{ $character->skills['manipulation']->rank }}</div></div>
            </td>
            <td></td>
            <td></td>
        </tr>
        <tr>
            <td colspan=3>
                <div class="box">
                    <div class="legend"><div>Armor</div></div>
                    @if (null !== $character->armor)
                    {{ $character->armor }} ({{ $character->armor->rating }})
                    @endif
                </div>
            </td>
            <td colspan=2>
                <div class="skill-name d-inline-block">Encumbrance</div>
                <div class="skill-rank d-inline-block" style="margin-left:-1rem;"><div>{{ $character->encumbrance }}</div></div>
            </td>
        </tr>
        <tr>
            <td style="height:5px"></td>
        </tr>
        <tr style="height:100%">
            <td colspan=2 rowspan=2>
                <div style="position:relative">
                    <div class="legend" style="background-color:#000000;"><div>Consumables</div></div>
                    <div>
                        <div class="consumable-name">Air</div>
                        <div class="consumable-amount"><div>&nbsp;</div></div>
                        <div class="consumable-name">Food</div>
                        <div class="consumable-amount"><div>&nbsp;</div></div>
                    </div>
                    <div>
                        <div class="consumable-name">Power</div>
                        <div class="consumable-amount"><div>&nbsp;</div></div>
                        <div class="consumable-name">Water</div>
                        <div class="consumable-amount"><div>&nbsp;</div></div>
                    </div>
                </div>
            </td>
            <td colspan=6 rowspan=4 style="height:inherit">
                <div class="box">
                    <div class="legend"><div>Weapons</div></div>
                    <div class="column-heads px-2 mx-0 row">
                        <div class="col-8"></div>
                        <div class="col-1">Bonus</div>
                        <div class="col-1">Damage</div>
                        <div class="col-2 text-center">Range</div>
                    </div>
                    @foreach ($character->weapons as $weapon)
                    <div class="px-2 mx-0 row">
                        <div class="col-8 underlined">{{ $weapon }}</div>
                        <div class="col-1 text-center underlined">{{ $weapon->bonus }}</div>
                        <div class="col-1 text-center underlined">{{ $weapon->damage }}</div>
                        <div class="col-2 text-center underlined">{{ ucfirst($weapon->range) }}</div>
                    </div>
                    @endforeach
                    @for ($i = 1; $i < 3 - count($character->weapons); $i++)
                    <div class="px-2 mx-0 row">
                        <div class="underlined col">&nbsp;</div>
                    </div>
                    @endfor
                </div>
        </tr>
        <tr>
        </tr>
    </table>

    @if (($creating ?? '' === 'review') && 0 === count($validationErrors))
        <form action="{{ route('alien.save-character') }}" method="POST">
            @csrf
            <button class="btn btn-primary" id="submit" type="submit">
                Character looks good!
            </button>
        </form>
    @endif
</x-app>
