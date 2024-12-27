<x-app>
    <x-slot name="title">{{ $character }}</x-slot>
    <x-slot name="head">
        <style>
            body {
                background: #ebd49d;
                color: #242021;
            }
            .track-box {
                border: 1px solid #242021;
                border-radius: 3px;
                display: inline-block;
                height: 2em;
                width: 2em;
            }
            .track-box.filled {
                background: #242021;
            }
            .attribute {
                border: 3px solid #242021;
                border-radius: 10px;
            }
            .attribute .name {
                background: #242021;
                color: #ebd49d;
                padding: 0.2em 0;
            }
            .attribute .value {
                font-size: 200%;
                font-weight: 700;
                padding: 0.2em 0;
            }
            .attribute .starting {
                padding-right: .5em;
                text-align: right;
            }
            .choose-nature {
                border-color: #242021;
                border-style: solid;
                border-width: 1px 1px 0 1px;
                margin-top: 1em;
            }
            .choose-nature div {
                margin-top: -1em;
            }
            .choose-nature span {
                background: #ebd49d;
                padding: 0 .5em;
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

<div class="row">
    <div class="col">
        <h1>{{ $character }}, {{ $character->playbook }}</h1>
    </div>
    <div class="col-2 text-center">
        injury<br>
        @for ($i = 1; $i <= $character->injury_max; $i++)
        <div class="track-box @if ($character->injury >= $i) filled @endif"></div>
        @endfor
    </div>
</div>

<div class="row">
    <div class="col-2 text-center"><div class="attribute">
        <div class="name">Charm</div>
        <div class="value">{{ $character->charm }}</div>
        <div class="starting text-muted">@if (0 <= $character->playbook->charm->value)+@endif{{ $character->playbook->charm }}</div>
    </div></div>
    <div class="col-2 text-center"><div class="attribute">
        <div class="name">Cunning</div>
        <div class="value">{{ $character->cunning }}</div>
        <div class="starting text-muted">@if (0 <= $character->playbook->cunning->value)+@endif{{ $character->playbook->cunning }}</div>
    </div></div>
    <div class="col-2 text-center"><div class="attribute">
        <div class="name">Finesse</div>
        <div class="value">{{ $character->finesse }}</div>
        <div class="starting text-muted">@if (0 <= $character->playbook->finesse->value)+@endif{{ $character->playbook->finesse }}</div>
    </div></div>
    <div class="col-2 text-center"><div class="attribute">
        <div class="name">Luck</div>
        <div class="value">{{ $character->luck }}</div>
        <div class="starting text-muted">@if (0 <= $character->playbook->luck->value)+@endif{{ $character->playbook->luck }}</div>
    </div></div>
    <div class="col-2 text-center"><div class="attribute">
        <div class="name">Might</div>
        <div class="value">{{ $character->might }}</div>
        <div class="starting text-muted">@if (0 <= $character->playbook->might->value)+@endif{{ $character->playbook->might }}</div>
    </div></div>
    <div class="col-2 text-center">
        exhaustion<br>
        @for ($i = 1; $i <= $character->exhaustion_max; $i++)
        <div class="track-box @if ($character->exhaustion >= $i) filled @endif"></div>
        @endfor
        <br>decay<br>
        @for ($i = 1; $i <= $character->decay_max; $i++)
        <div class="track-box @if ($character->decay >= $i) filled @endif"></div>
        @endfor
    </div>
</div>
<div class="row">
    <div class="col text-center text-muted">
        add +1 to a stat of your choice, to a max of +2
    </div>
</div>

<div class="row">
    <div class="col-3"></div>
    <div class="col text-center mx-4">
        <div class="choose-nature"><div>
            <span>choose your nature</span>
        </div></div>
    </div>
    <div class="col-3"></div>
</div>
<div class="row">
    <div class="col-2"></div>
    @php
    $nature = current($natures);
    @endphp
    <div class="col text-center @if ($nature->id !== $character->nature->id) text-muted @endif">
        <h3>{{ $nature }}</h3>
        <p>{{ $nature->description }}</p>
    </div>
    <div class="col-2 text-center mt-2" style="line-height:0.6;">
        .<br>
        .<br>
        .<br>
        <div style="margin-top: 4px;">or</div>
        .<br>
        .<br>
        .<br>
    </div>
    @php
    $nature = end($natures);
    @endphp
    <div class="col text-center @if ($nature->id !== $character->nature->id) text-muted @endif">
        <h3>{{ $nature }}</h3>
        <p>{{ $nature->description }}</p>
    </div>
    <div class="col-2"></div>
</div>

<div class="row">
    <div class="col-10 text-center fs-4">
        your connections
    </div>
    <div class="col-2">
        <div class="fs-4 text-center">weapon skills</div>
        <div class="text-center text-muted">
            choose one bolded<br>
            weapon skill to start
        </div>
        <div class="row">
            <div class="col">
                @foreach ($weapon_skills_starting as $move)
                    @if ($loop->even)
                        @continue
                    @endif
                    <strong>
                        <input class="form-check-input" disabled readonly type="checkbox"
                            @if ($character->moves->keyBy('id')->has($move->id))
                                checked
                            @endif
                        >
                        {{ $move }}
                    </strong><br>
                @endforeach
                @foreach ($weapon_skills as $move)
                    @if ($loop->even)
                        @continue
                    @endif
                    <input class="form-check-input" disabled readonly type="checkbox"
                        @if ($character->moves->keyBy('id')->has($move->id))
                            checked
                        @endif
                    >
                    {{ $move }}<br>
                @endforeach
            </div>
            <div class="col">
                @foreach ($weapon_skills_starting as $move)
                    @if ($loop->odd)
                        @continue
                    @endif
                    <strong>
                        <input class="form-check-input" disabled readonly type="checkbox"
                            @if ($character->moves->keyBy('id')->has($move->id))
                                checked
                            @endif
                        >
                        {{ $move }}
                    </strong><br>
                @endforeach
                @foreach ($weapon_skills as $move)
                    @if ($loop->odd)
                        @continue
                    @endif
                    <input class="form-check-input" disabled readonly type="checkbox"
                        @if ($character->moves->keyBy('id')->has($move->id))
                            checked
                        @endif
                    >
                    {{ $move }}<br>
                @endforeach
            </div>
        </div>
    </div>
</div>

<div class="row">
    <div class="col text-center fs-4">
        your moves
    </div>
</div>
</x-app>
