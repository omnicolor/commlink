<x-app>
    <x-slot name="title">
        {{ $character->handle }}
    </x-slot>
    <x-slot name="head">
        <style>
            .value {
                display: inline-block;
                float: right;
            }
            .value span {
                color: #999;
                display: inline-block;
                text-align: center;
                white-space: nowrap;
                width: 2em;
            }
            .value span:last-child {
                color: #000;
                text-align: right;
            }
            .card {
                margin-top: 1em;
            }
            .show-more {
                bottom: -18px;
                display: inline-block;
                font-size: 80%;
                position: absolute;
                right: 0;
            }
            #weaponlist th {
                font-size: 80%;
            }
            span.monitor {
                background-color: #66cc66;
                border-color: #000000;
                border-style: solid;
                border-width: 1px 0 1px 1px;
                color: #66cc66;
                display: inline-block;
                float: left;
                height: 1.4em;
                margin: 0;
                padding-left: 0.2em;
                width: 1.4em;
            }
            span.monitor:last-child {
                border-width: 1px;
            }
            span.monitor.used {
                background-color: #cc6666;
                color: #000000;
            }
            .tooltip-inner {
                text-align: left;
            }
        </style>
    </x-slot>

    <div class="row">
        <div class="col">
            <div class="card">
                <div class="card-header">metadata</div>
                <ul class="list-group list-group-flush">
                    <li class="list-group-item">
                        Handle
                        <div class="value" id="handle">
                            {{ $character->handle }}
                        </div>
                    </li>
                    <li class="list-group-item">
                        INT
                        <div class="value" id="intelligence">
                            {{ $character->intelligence }}
                        </div>
                    </li>
                    <li class="list-group-item">
                        REF
                        <div class="value" id="reflexes">
                            {{ $character->reflexes }}
                        </div>
                    </li>
                    <li class="list-group-item">
                        DEX
                        <div class="value" id="dexterity">
                            {{ $character->dexterity }}
                        </div>
                    </li>
                    <li class="list-group-item">
                        TECH
                        <div class="value" id="technique">
                            {{ $character->technique }}
                        </div>
                    </li>
                    <li class="list-group-item">
                        COOL
                        <div class="value" id="cool">
                            {{ $character->cool }}
                        </div>
                    </li>
                    <li class="list-group-item">
                        WILL
                        <div class="value" id="willpower">
                            {{ $character->willpower }}
                        </div>
                    </li>
                    <li class="list-group-item">
                        LUCK
                        <div class="value">
                            {{ $character->luck }}
                        </div>
                    </li>
                    <li class="list-group-item">
                        MOVE
                        <div class="value" id="movement">
                            {{ $character->movement }}
                        </div>
                    </li>
                </ul>
            </div>
        </div>
    </div>
</x-app>
