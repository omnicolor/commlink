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
            @php
                $demeanors = collect($character->demeanors ?? []);
                $options = collect($character->playbook->demeanor_options);
                $extra_demeanors = $demeanors->diff($options);
            @endphp
            @foreach ($options as $demeanor)
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
                            <td><input class="form-check-input" name="balance" type="radio" value="3"></td>
                            <td><input class="form-check-input" name="balance" type="radio" value="2"></td>
                            <td><input class="form-check-input" name="balance" type="radio" value="1"></td>
                            <td><input class="form-check-input" name="balance" type="radio" value="0"></td>
                            <td><input class="form-check-input" name="balance" type="radio" value="-1"></td>
                            <td><input class="form-check-input" name="balance" type="radio" value="-2"></td>
                            <td><input class="form-check-input" name="balance" type="radio" value="-3"></td>
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
                        <input class="form-check-input" type="textbox">
                        Afraid
                        <div class="form-text">
                            Take -2 to intimidate and call someone out
                        </div>
                    </label>
                    <label class="form-check-label">
                        <input class="form-check-input" type="checkbox">
                        Angry
                        <div class="form-text">
                            Take -2 to guide and comfort and assess a situation
                        </div>
                    </label>
                    <label class="form-check-label">
                        <input class="form-check-input" type="checkbox">
                        Guilty
                        <div class="form-text">
                            Take -2 to push your luck and +2 to deny a callout
                        </div>
                    </label>
                    <label class="form-check-label">
                        <input class="form-check-input" type="checkbox">
                        Insecure
                        <div class="form-text">
                            Take -2 to trick and resist shifting your balance
                        </div>
                    </label>
                    <label class="form-check-label">
                        <input class="form-check-input" type="checkbox">
                        Troubled
                        <div class="form-text">
                            Take -2 to plead and rely on your skills or training
                        </div>
                    </label>
                </div>
            </div>
        </div>
    </div>

    <x-slot name="javascript">
        <script>
        </script>
    </x-slot>
</x-app>
