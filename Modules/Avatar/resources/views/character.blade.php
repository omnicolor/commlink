@use(Modules\Avatar\Enums\Condition)
@use(Illuminate\Support\Str)
<x-app>
    <x-slot name="title">{{ $character }}</x-slot>
    <x-slot name="head">
        <style>
            tr.darkness {
                background-color: #000000;
                color: #ffffff;
            }
            tr.lightness {
                background-color: #ffffff;
                color: #000000;
            }
            .box {
                border: 3px solid #69779c;
                padding: 2px;
            }
            .inner-box {
                background-color: #d3dbe6;
                border: 1px solid #8e9ab6;
            }
            .stat {
                background-color: #ffffff;
                border: 2px solid #000000;
                border-radius: 50%;
                color: #000000;
                font-size: 150%;
                height: 2em;
                margin-left: .6em;
                padding-left: calc(var(--bs-gutter-x) * .6);
                padding-top: .1em;
                width: 2em;
            }
            .technique-class {
                color: #64110b;
                font-size: large;
                font-weight: bold;
            }
            .techniques strong {
                font-size: x-large;
            }
            .techniques .form-check-reverse .form-check-input {
                margin-left: 1em;
                margin-top: .8em;
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

    <div class="row my-4">
        <div class="col">
            <h2>{{ $character->playbook }}</h2>
            <strong>Background</strong> {{ $character->background }}<br>
            <strong>Demeanor</strong>
            <div class="row">
            @foreach ($demeanor_options as $demeanor)
                <div class="col">
                    <div class="form-check">
                        <input
                        @if ($demeanors->contains($demeanor))
                            checked
                        @endif
                        class="form-check-input" disabled type="checkbox">
                    {{ Str::headline($demeanor) }}
                    </div>
                </div>
                @if ($loop->even)
                </div>
                <div class="row">
                @endif
            @endforeach
            </div>
            @foreach ($extra_demeanors as $demeanor)
                <div class="col">
                    <div class="form-check">
                        <input checked
                        class="form-check-input" disabled type="checkbox">
                    {{ Str::headline($demeanor) }}
                    </div>
                </div>
                @if ($loop->even)
                </div>
                <div class="row">
                @endif
            @endforeach
        </div>
        <div class="col">
            <h1>{{ $character }}</h1>
            <h2>{{ $character->training }}</h2>
            <p class="small">{{ $character->training?->description() }}</p>
        </div>
        <div class="col text-center">
            <img src="/images/Avatar/avatar.png"><br>
            <img src="/images/Avatar/legends.png"><br>
            <img src="/images/Avatar/the-roleplaying-game.png">

            <h3>Statuses</h3>
            <div class="row text-start">
                <div class="col">
                    <label class="form-check-label">
                        <input class="form-check-input" type="checkbox">
                        Empowered
                    </label>
                </div>
                <div class="col">
                    <label class="form-check-label">
                        <input class="form-check-input" type="checkbox">
                        Doomed
                    </label>
                </div>
            </div>
            <div class="row text-start">
                <div class="col">
                    <label class="form-check-label">
                        <input class="form-check-input" type="checkbox">
                        Favored
                    </label>
                </div>
                <div class="col">
                    <label class="form-check-label">
                        <input class="form-check-input" type="checkbox">
                        Impaired
                    </label>
                </div>
            </div>
            <div class="row text-start">
                <div class="col">
                    <label class="form-check-label">
                        <input class="form-check-input" type="checkbox">
                        Inspired
                    </label>
                </div>
                <div class="col">
                    <label class="form-check-label">
                        <input class="form-check-input" type="checkbox">
                        Trapped
                    </label>
                </div>
            </div>
            <div class="row text-start">
                <div class="col">
                    <label class="form-check-label">
                        <input class="form-check-input" type="checkbox">
                        Prepared
                    </label>
                </div>
                <div class="col">
                    <label class="form-check-label">
                        <input class="form-check-input" type="checkbox">
                        Stunned
                    </label>
                </div>
            </div>
        </div>
    </div>

    <div class="box">
        <div class="inner-box">
            <div class="row">
                <div class="col ms-4 p-4">
                    <h3>Stats</h3>

                    <div class="my-1 row">
                        <div class="col-1 stat"
                            @if (0 > $character->creativity)
                            style="padding-left: calc(var(--bs-gutter-x) * .4);"
                            @endif
                            >{{ $character->creativity }}</div>
                        <div class="col pt-1"><h4>Creativity</h4></div>
                    </div>
                    <div class="mb-1 row">
                        <div class="col-1 stat"
                            @if (0 > $character->focus)
                            style="padding-left: calc(var(--bs-gutter-x) * .4);"
                            @endif
                            >{{ $character->focus }}</div>
                        <div class="col pt-1"><h4>Focus</h4></div>
                    </div>
                    <div class="mb-1 row">
                        <div class="col-1 stat"
                            @if (0 > $character->harmony)
                            style="padding-left: calc(var(--bs-gutter-x) * .4);"
                            @endif
                            >{{ $character->harmony }}</div>
                        <div class="col pt-1"><h4>Harmony</h4></div>
                    </div>
                    <div class="mb-1 row">
                        <div class="col-1 stat"
                            @if (0 > $character->passion)
                            style="padding-left: calc(var(--bs-gutter-x) * .4);"
                            @endif
                            >{{ $character->passion }}</div>
                        <div class="col pt-1"><h4>Passion</h4></div>
                    </div>

                    <h3>Fatigue</h3>

                    <div class="form-check form-check-inline">
                        <input class="form-check-input" type="checkbox">
                    </div>
                    <div class="form-check form-check-inline">
                        <input class="form-check-input" type="checkbox">
                    </div>
                    <div class="form-check form-check-inline">
                        <input class="form-check-input" type="checkbox">
                    </div>
                    <div class="form-check form-check-inline">
                        <input class="form-check-input" type="checkbox">
                    </div>
                    <div class="form-check form-check-inline">
                        <input class="form-check-input" type="checkbox">
                    </div>
                </div>
                <div class="col p-4">
                    <h3>Balance</h3>

                    <table>
                        <tr>
                            <td rowspan="5">
                                <img src="/images/Avatar/adamant-restraint.png"
                                    style="margin-right:-1px;margin-top:-35px">
                            </td>
                            <td colspan="7"><img src="/images/Avatar/black-fish.png"></td>
                            <td rowspan="5">
                                <img src="/images/Avatar/adamant-results.png"
                                    style="margin-left:-1px;margin-top:25px">
                            </td>
                        </tr>
                        <tr class="darkness">
                            <td>+3</td>
                            <td>+2</td>
                            <td>+1</td>
                            <td>0</td>
                            <td>-1</td>
                            <td>-2</td>
                            <td>-3</td>
                        </tr>
                        <tr>
                            <td>
                                <input class="form-check-input" name="balance"
                                    @if (($character->balance ?? 0) === 3) checked @endif
                                    type="radio" value="3">
                            </td>
                            <td>
                                <input class="form-check-input" name="balance"
                                    @if (($character->balance ?? 0) === 2) checked @endif
                                    type="radio" value="2">
                            </td>
                            <td>
                                <input class="form-check-input" name="balance"
                                    @if (($character->balance ?? 0) === 1) checked @endif
                                    type="radio" value="1">
                            </td>
                            <td>
                                <input class="form-check-input" name="balance"
                                    @if (($character->balance ?? 0) === 0) checked @endif
                                    type="radio" value="0">
                            </td>
                            <td>
                                <input class="form-check-input" name="balance"
                                    @if (($character->balance ?? 0) === -1) checked @endif
                                    type="radio" value="-1">
                            </td>
                            <td>
                                <input class="form-check-input" name="balance"
                                    @if (($character->balance ?? 0) === -2) checked @endif
                                    type="radio" value="-2">
                            </td>
                            <td>
                                <input class="form-check-input" name="balance"
                                    @if (($character->balance ?? 0) === -3) checked @endif
                                    type="radio" value="-3">
                            </td>
                        </tr>
                        <tr class="lightness">
                            <td>-3</td>
                            <td>-2</td>
                            <td>-1</td>
                            <td>0</td>
                            <td>+1</td>
                            <td>+2</td>
                            <td>+3</td>
                        </tr>
                        <tr>
                            <td colspan="7"><img src="/images/Avatar/white-fish.png"></td>
                        </tr>
                    </table>
                </div>
                <div class="col p-4">
                    <h3>Conditions</h3>

                    <label class="form-check-label">
                        <input class="form-check-input conditions"
                            @if (in_array(Condition::Afraid, $character->conditions)) checked @endif
                            id="afraid" type="checkbox">
                        Afraid
                        <div class="form-text">
                            Take -2 to intimidate and call someone out
                        </div>
                    </label>
                    <label class="form-check-label">
                        <input class="form-check-input conditions"
                            @if (in_array(Condition::Angry, $character->conditions)) checked @endif
                            id="angry" type="checkbox">
                        Angry
                        <div class="form-text">
                            Take -2 to guide and comfort and assess a situation
                        </div>
                    </label>
                    <label class="form-check-label">
                        <input class="form-check-input conditions"
                            @if (in_array(Condition::Guilty, $character->conditions)) checked @endif
                            id="guilty" type="checkbox">
                        Guilty
                        <div class="form-text">
                            Take -2 to push your luck and +2 to deny a callout
                        </div>
                    </label>
                    <label class="form-check-label">
                        <input class="form-check-input conditions"
                            @if (in_array(Condition::Insecure, $character->conditions)) checked @endif
                            id="insecure" type="checkbox">
                        Insecure
                        <div class="form-text">
                            Take -2 to trick and resist shifting your balance
                        </div>
                    </label>
                    <label class="form-check-label">
                        <input class="form-check-input conditions"
                            @if (in_array(Condition::Troubled, $character->conditions)) checked @endif
                            id="troubled" type="checkbox">
                        Troubled
                        <div class="form-text">
                            Take -2 to plead and rely on your skills or training
                        </div>
                    </label>
                </div>
            </div>
        </div>
    </div>

    <div class="row mb-4">
        <div class="col">
            <h3>{{ $character->playbook->feature }}</h3>

            {!! $feature_description !!}
        </div>
        <div class="col">
            <h3>Moves</h3>
            @foreach ($character->playbook->moves as $move)
                <div class="form-check">
                    <input
                        @if ($moves->contains($move->id))
                            checked
                        @endif
                        class="form-check-input" disabled type="checkbox">
                    <strong>{{ $move }}</strong><br>
                    {!! str_replace('•', '<li>', $move->description) !!}
                </div>
            @endforeach
        </div>
    </div>

    <div class="mt-4 row">
        <div class="col">
            <h3>Your Character</h3>

            <strong>Look:</strong><br />
            <p>{{ $character->appearance }}</p>

            <strong>Home Town:</strong> {{ $character->home_town ?? '' }}

            <h4 class="mt-1">History</h4>
            <ul>
            @foreach ($character->playbook->history as $history)
                <li>{{ $history }}</li>
            @endforeach
            </ul>

            <h4 class="mt-1">Connections</h4>

            <ul>
            @foreach ($character->playbook->getConnections($character->connections ?? []) as $connection)
                <li>{{ $connection }}</li>
            @endforeach
            </ul>

            <h4 class="mt-1">Moment of Balance</h4>
            <p>{{ $character->playbook->moment_of_balance }}</p>

            <h4 class="mt-1">Clearing Conditions</h4>

            <ul>
                <li><strong>Afraid:</strong> run from danger or difficulty.</li>
                <li><strong>Angry:</strong> break something important or lash out at a friend.</li>
                <li><strong>Guilty:</strong> make a personal sacrifice to absolve your guilt.</li>
                <li><strong>Insecure:</strong> take foolhardy action without talking to your companions.</li>
                <li><strong>Troubled:</strong> seek guidance from a mentor or powerful figure.</li>
            </ul>

            <h4 class="mt-1">
                Growth
                @for ($i = 1; $i <= 4; $i++)
                <input class="form-check-input" disabled
                    @if (($character->growth ?? 0) >= $i) checked @endif
                    id="growth-{{ $i }}" type="checkbox" value="true">
                @endfor
            </h4>

            <h5>Growth questions</h5>
            <p>At the end of each session, answer these questions:</p>

            <ul>
                <li>
                    Did you learn something challenging, exciting, or
                    complicated about the world?
                </li>
                <li>
                    Did you stop a dangerous threat or solve a community
                    problem?
                </li>
                <li>
                    Did you guide a character towards balance or end the
                    session at your center?
                </li>
                <li><strong>
                    {{ $character->playbook->growth_question ?? '' }}
                </strong></li>
            </ul>

            <h5>Growth Advancements</h5>
            <ul>
                <li>
                    Take a new move from your playbook
                    <input class="form-check-input" disabled
                        @if ($character->growth_advancements->new_move_from_my_playbook >= 1) checked @endif
                        id="move-from-my-playbook-1" type="checkbox" value="true">
                    <input class="form-check-input move-from-my-playbook" disabled
                        @if ($character->growth_advancements->new_move_from_my_playbook === 2) checked @endif
                        disabled id="move-from-my-playbook-2" type="checkbox" value="true">
                </li>
                <li>
                    Take a new move from another playbook
                    <input class="form-check-input" disabled
                        @if ($character->growth_advancements->new_move_from_another_playbook >= 1) checked @endif
                        id="move-from-another-playbook-1" type="checkbox" value="true">
                    <input class="form-check-input" disabled
                        @if ($character->growth_advancements->new_move_from_another_playbook >= 2) checked @endif
                        id="move-from-another-playbook-2" type="checkbox" value="true">
                </li>
                <li>Raise a stat by +1 (maximum of +2 in any given stat)</li>
                <li>
                    Shift your center one step
                    <input class="form-check-input" disabled
                        @if ($character->growth_advancements->shift_your_center >= 1) checked @endif
                        id="shift-your-center-1" type="checkbox" value="true">
                    <input class="form-check-input" disabled
                        @if ($character->growth_advancements->shift_your_center >= 2) checked @endif
                        id="shift-your-center-2" type="checkbox" value="true">
                </li>
                <li>
                    Unlock your Moment of Balance
                    <input class="form-check-input" disabled
                        @if ($character->growth_advancements->unlock_your_moment_of_balance >= 1) checked @endif
                        id="unlock-your-moment-of-balance-1" type="checkbox" value="true">
                    <input class="form-check-input" disabled
                        @if ($character->growth_advancements->unlock_your_moment_of_balance >= 2) checked @endif
                        id="unlock-your-moment-of-balance-2" type="checkbox" value="true">
                </li>
            </ul>
        </div>
        <div class="col techniques">
            <h3>Fighting Techniques</h3>

            @foreach ($character->techniques as $technique)
            <div>
                <strong>{{ $technique }}</strong>
                <div class="float-end">
                    <div class="form-check form-check-inline form-check-reverse">
                        <label class="form-check-label">
                            <strong>L</strong>
                            <input class="form-check-input"
                                @if ($technique->level->isLearned()) checked @endif
                                disabled type="checkbox" value="true">
                        </label>
                    </div>
                    <div class="form-check form-check-inline form-check-reverse">
                        <label class="form-check-label">
                            <strong>P</strong>
                            <input class="form-check-input"
                                @if ($technique->level->isPracticed()) checked @endif
                                disabled type="checkbox" value="true">
                        </label>
                    </div>
                    <div class="form-check form-check-inline form-check-reverse">
                        <label class="form-check-label">
                            <strong>M</strong>
                            <input class="form-check-input"
                                @if ($technique->level->isMastered()) checked @endif
                                disabled type="checkbox" value="true">
                        </label>
                    </div>
                </div>
                <br>
                <div class="technique-class">{{ $technique->class->name() }}</div>
                <div>
                    {{ $technique->description }}
                </div>
            </div>
            @endforeach
        </div>
    </div>

    <x-slot name="javascript">
        <script>
        </script>
    </x-slot>
</x-app>
