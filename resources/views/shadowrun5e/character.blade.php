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
            #identities .collapse {
                display: none;
            }
            #identities .collapse.show {
                display: flex;
            }
        </style>
    </x-slot>

    @if ($errors->any())
        <div class="alert alert-danger mt-4">
            <ul>
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <div class="row">
        <div class="col">
            <x-shadowrun5e.metadata :character="$character"/>
            <x-shadowrun5e.skills :character="$character"/>
        </div>

        <div class="col">
            <x-shadowrun5e.attributes :character="$character"/>
            <x-shadowrun5e.qualities :character="$character"/>
        </div>
    </div>

    <div class="row">
        <div class="col">
            <x-shadowrun5e.weapons :character="$character"/>
        </div>
    </div>

    <div class="row">
        <div class="col">
            <x-shadowrun5e.augmentations :character="$character"/>
            <x-shadowrun5e.spells :character="$character"/>
            <x-shadowrun5e.powers :character="$character"/>
            <x-shadowrun5e.gear :character="$character"/>
        </div>
        <div class="col">
            <x-shadowrun5e.armor :character="$character"/>
            <x-shadowrun5e.contacts :character="$character"/>
            <x-shadowrun5e.identities :character="$character"/>
            <x-shadowrun5e.martial-arts :character="$character"/>
        </div>
    </div>

    <div class="row mb-4">
        <div class="col">
            <x-shadowrun5e.vehicles :character="$character"/>
            <x-shadowrun5e.matrix :character="$character"/>
        </div>
    </div>

    <x-slot name="javascript">
        <script>
            $(function () {
                $('[data-bs-toggle="tooltip"]').tooltip();
            });
        </script>
    </x-slot>
</x-app>
