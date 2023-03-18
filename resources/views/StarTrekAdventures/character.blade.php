<x-app>
    <x-slot name="title">{{ $character->name }}</x-slot>
    <x-slot name="head">
        <style>
            .attributes {
                line-height: normal;
            }
            .attributes .col {
                background-color: #9676a9;
                border-radius: 10px 0 0 10px;
                color: #ffffff;
                text-align: right;
                text-transform: uppercase;
            }
            .attributes .col-1 {
                border: 1px solid #9676a9;
                border-radius: 0 10px 10px 0;
            }
            .monitor {
                border-color: #000000;
                border-style: solid;
                border-width: 1px;
                display: inline-block;
                float: left;
                height: 1.4em;
                margin-left: 0.2em;
                padding-left: 0.2em;
                width: 1.4em;
            }
            .orange-background {
                background-color: #f39d09;
            }
            .orange {
                color: #f39d09;
            }
            .light-purple-background {
                background-color: #c4bcde;
            }
            .light-purple {
                color: #c4bcde;
            }
            .pink-background {
                background-color: #db9eba;
            }
            .purple-background {
                background-color: #9676a9;
            }
            .purple {
                color: #9676a9;
            }
            .section-heading {
                background-color: #f39d09;
                border-radius: 10px;
                line-height: normal;
            }
            .section-heading span {
                background-color: #ffffff;
                color: #a44071;
                padding-left: .5em;
                padding-right: .5em;
                text-transform: uppercase;
            }
            .underlined {
                border-bottom: 1px solid #c4bcde;
            }
        </style>
    </x-slot>
    <x-slot name="navbar">
        <li class="nav-item">
            <a class="nav-link" href="/dashboard">Home</a>
        </li>
        <li class="nav-item">
            <span class="nav-link active">{{ $character }}</span>
        </li>
    </x-slot>

    <div class="row">
        <div class="col-1"></div>
        <div class="align-items-center col-1 d-flex orange-background text-center">
            <img alt="Star Trek logo" height="66"
                src="/images/StarTrekAdventures/logo.png" style="margin:auto;"
                width="66">
        </div>
        <div class="col">
            <div class="row mt-1">
                <div class="col orange text-end">STARFLEET PERSONNEL FILE</div>
            </div>
            <div class="row">
                <div class="col-2 purple">NAME:</div>
                <div class="col underlined">{{ $character }}</div>
            </div>
            <div class="row">
                <div class="col-2 purple">SPECIES:</div>
                <div class="col-4 underlined" data-bs-toggle="tooltip"
                    title="{{ $character->species->description }}">
                    {{ $character->species }}
                </div>
                <div class="col-2 purple">RANK:</div>
                <div class="col-4 underlined">{{ $character->rank }}</div>
            </div>
            <div class="row">
                <div class="col-2 purple">ENVIRONMENT:</div>
                <div class="col-4 underlined">{{ $character->environment }}</div>
                <div class="col-2 purple">UPBRINGING:</div>
                <div class="col-4 underlined">{{ $character->upbringing }}</div>
            </div>
            <div class="row">
                <div class="col-2 purple">ASSIGNMENT:</div>
                <div class="col underlined">{{ $character->assignment }}</div>
            </div>
            <div class="row mb-1">
                <div class="col-2 purple">TRAITS:</div>
                <div class="col underlined">{{ $character->traits }}</div>
            </div>
        </div>
        <div class="col-1"></div>
    </div>
    <div class="row">
        <div class="col-1"></div>
        <div class="col-1 orange-background" style="border-bottom-left-radius: 500rem 50rem;">&nbsp;</div>
        <div class="col orange-background">&nbsp;</div>
    </div>

    <div class="row mt-2">
        <div class="col-1"></div>
        <div class="col-1 light-purple-background" style="border-top-left-radius: 500rem 50rem;">&nbsp;</div>
        <div class="col light-purple-background"></div>
    </div>
    <div class="row">
        <div class="col-1"></div>
        <div class="col-2 light-purple-background"></div>
        <div class="col">
            <div class="row">
                <div class="col">
                    <div class="row ms-2 mt-4">
                        <div class="col">
                            <div class="row">
                                <div class="col me-2 ps-3 section-heading">
                                    <span>Attributes</span>
                                </div>
                            </div>
                            <div class="attributes mt-1 row">
                                <div class="col">Control</div>
                                <div class="col-1 me-1">
                                    {{ $character->attributes->control }}
                                </div>
                                <div class="col">Fitness</div>
                                <div class="col-1 me-1">
                                    {{ $character->attributes->fitness }}
                                </div>
                                <div class="col">Presence</div>
                                <div class="col-1 me-2">
                                    {{ $character->attributes->presence }}
                                </div>
                            </div>
                            <div class="attributes mt-1 row">
                                <div class="col">Daring</div>
                                <div class="col-1 me-1">
                                    {{ $character->attributes->daring }}
                                </div>
                                <div class="col">Insight</div>
                                <div class="col-1 me-1">
                                    {{ $character->attributes->insight }}
                                </div>
                                <div class="col">Reason</div>
                                <div class="col-1 me-2">
                                    {{ $character->attributes->reason }}
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="row ms-2 mt-4">
                        <div class="col">
                            <div class="row">
                                <div class="col me-2 ps-3 section-heading">
                                    <span>Disciplines</span>
                                </div>
                            </div>
                            <div class="attributes mt-1 row">
                                <div class="col">Command</div>
                                <div class="col-1 me-1">
                                    {{ $character->disciplines->command }}
                                </div>
                                <div class="col">Security</div>
                                <div class="col-1 me-1">
                                    {{ $character->disciplines->security }}
                                </div>
                                <div class="col">Science</div>
                                <div class="col-1 me-2">
                                    {{ $character->disciplines->science }}
                                </div>
                            </div>
                            <div class="attributes mt-1 row">
                                <div class="col">Conn</div>
                                <div class="col-1 me-1">
                                    {{ $character->disciplines->conn }}
                                </div>
                                <div class="col"><span style="font-size:90%">
                                    Engineering
                                </span></div>
                                <div class="col-1 me-1">
                                    {{ $character->disciplines->engineering }}
                                </div>
                                <div class="col">Medicine</div>
                                <div class="col-1 me-2">
                                    {{ $character->disciplines->medicine }}
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col mt-4">
                    <div class="ps-3 section-heading"><span>Focuses</span></div>
                    @foreach ($character->focuses as $focus)
                    <div class="underlined">{{ $focus }}</div>
                    @endforeach
                </div>
            </div>
        </div>
        <div class="col-1"></div>
    </div>

    <div class="row mt-2">
        <div class="col-1"></div>
        <div class="col-2 purple-background"></div>
        <div class="col">
            <div class="ps-3 section-heading"><span>Values</span></div>
            @foreach ($character->values as $value)
            <div class="underlined">{{ $value }}</div>
            @endforeach
        </div>
        <div class="col">
            <div class="mb-1 ps-3 section-heading"><span>Stress</span></div>
            @for ($i = 1; $i <= $character->stress; $i++)
            <span class="monitor"></span>
            @endfor
        </div>
        <div class="col-1"></div>
    </div>

    <div class="row mt-2">
        <div class="col-1"></div>
        <div class="col-2 light-purple-background"></div>
        <div class="col">
            <div class="ps-3 section-heading"><span>Talents</span></div>
            @foreach ($character->talents as $talent)
                <div class="underlined">{{ $talent }}</div>
            @endforeach
        </div>
        <div class="col">
            <div class="ps-3 section-heading"><span>Injuries</span></div>
        </div>
        <div class="col-1"></div>
    </div>

    <div class="row mt-2">
        <div class="col-1"></div>
        <div class="col-2 pink-background"></div>
        <div class="col">
            <div class="ps-3 section-heading"><span>Weapons</span></div>
        </div>
        <div class="col">
            <div class="ps-3 section-heading"><span>Other equipment</span></div>
        </div>
        <div class="col-1"></div>
    </div>

    <x-slot name="javascript">
        <script>
            $(function () {
                $('[data-bs-toggle="tooltip"]').tooltip();
            });
        </script>
    </x-slot>
</x-app>
