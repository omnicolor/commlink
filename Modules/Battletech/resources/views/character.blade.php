@php
    $creating = 'review' === ($creating ?? '');
    $player = !$creating && null !== $user && $user->email === $character->owner;
@endphp
<x-app>
    <x-slot name="title">{{ $character }}</x-slot>
    <x-slot name="head">
        <style>
            .box {
                border: 2px solid #000000;
                margin-bottom: 1em;
                margin-top: 1em;
                padding: 1em .5em .5em .5em;
                position: relative;
            }
            header {
                background-color: #000000;
                border-radius: .5em;
                color: #ffffff;
                display: inline;
                padding: .1em 1em;
                position: absolute;
                text-transform: uppercase;
                top: -1em;
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

    @if (($creating) && 0 !== count($validationErrors))
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

    <!--
    <div class="row">
        <div class="col text-center"><img alt="Battletech" src="/images/Battletech/character-sheet-heading.png"></div>
    </div>
    -->

    <div class="row">
        <div class="col-lg-2"></div>
        <div class="col">
            <div class="box">
                <header>Personal data</header>

                <div class="row">
                    <div class="col text-decoration-underline w-100">
                        <span class="text-decoration-none">Name: </span>{{ $character->name }}
                    </div>
                    <div class="col">
                        Player: <div class="d-inline-block text-decoration-underline">{{ $character->user()->name }}</div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-lg-2"></div>
    </div>

    <div class="row">
        <div class="col-lg-2"></div>
        <div class="col">
            <div class="box">
                <header>Attributes</header>

                <div class="row">
                    <div class="col">Attribute</div>
                    <div class="col">Score</div>
                    <div class="col">Link</div>
                    <div class="col">XP</div>
                </div>

                <div class="row">
                    <div class="col">STR</div>
                    <div class="col">{{ $character->attributes->strength }}</div>
                    <div class="col"></div>
                    <div class="col"></div>
                </div>
                <div class="row">
                    <div class="col">BOD</div>
                    <div class="col">{{ $character->attributes->body }}</div>
                    <div class="col"></div>
                    <div class="col"></div>
                </div>
                <div class="row">
                    <div class="col">RFL</div>
                    <div class="col">{{ $character->attributes->reflexes }}</div>
                    <div class="col"></div>
                    <div class="col"></div>
                </div>
                <div class="row">
                    <div class="col">DEX</div>
                    <div class="col">{{ $character->attributes->dexterity }}</div>
                    <div class="col"></div>
                    <div class="col"></div>
                </div>
                <div class="row">
                    <div class="col">INT</div>
                    <div class="col">{{ $character->attributes->intelligence }}</div>
                    <div class="col"></div>
                    <div class="col"></div>
                </div>
                <div class="row">
                    <div class="col">WIL</div>
                    <div class="col">{{ $character->attributes->willpower }}</div>
                    <div class="col"></div>
                    <div class="col"></div>
                </div>
                <div class="row">
                    <div class="col">CHA</div>
                    <div class="col">{{ $character->attributes->charisma }}</div>
                    <div class="col"></div>
                    <div class="col"></div>
                </div>
                <div class="row">
                    <div class="col">EDG</div>
                    <div class="col">{{ $character->attributes->edge }}</div>
                    <div class="col"></div>
                    <div class="col"></div>
                </div>
            </div>
            <div class="box">
                <header>Traits (Personal)</header>

                <div class="row">
                    <div class="col">Trait</div>
                    <div class="col">TP</div>
                    <div class="col">Page</div>
                    <div class="col">XP</div>
                </div>
                @foreach ($character->traits as $trait)
                <div class="row">
                    <div class="col">{{ $trait }}</div>
                    <div class="col">{{ $trait->cost }}</div>
                    <div class="col">{{ $trait->page }}</div>
                    <div class="col"></div>
                </div>
                @endforeach
            </div>
        </div>
        <div class="col">
            <div class="box">
                <header>Combat Data</header>
            </div>
        </div>
        <div class="col-lg-2"></div>
    </div>

    <div class="row">
        <div class="col-lg-2"></div>
        <div class="col">
            <div class="box">
                <header>Skills</header>
                <div class="row">
                    <div class="col">Skill</div>
                </div>
                @foreach ($character->skills as $skill)
                <div class="row">
                    <div class="col">{{ $skill }}
                </div>
                @endforeach
            </div>
        </div>
        <div class="col-lg-2"></div>
    </div>

    <div class="row">
        <div class="col-lg-2"></div>
        <div class="col">
            <div class="box">
                <header>Inventory</header>
            </div>
        </div>
        <div class="col-lg-2"></div>
    </div>

    <div class="row">
        <div class="col-lg-2"></div>
        <div class="col">
            <div class="box">
                <header>Vehicle Data</header>
            </div>
        </div>
        <div class="col-lg-2"></div>
    </div>

    <div class="row">
        <div class="col-lg-2"></div>
        <div class="col">
            <div class="box">
                <header>Biography</header>
            </div>
        </div>
        <div class="col-lg-2"></div>
    </div>
</x-app>
