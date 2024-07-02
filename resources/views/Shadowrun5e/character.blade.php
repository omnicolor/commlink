<x-app>
    <x-slot name="title">{{ $character->handle }}</x-slot>
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
        @if ($currentStep ?? false)
        <link href="/css/Shadowrun5e/character-generation.css" rel="stylesheet">
        @endif
    </x-slot>
    @includeWhen($currentStep ?? false, 'Shadowrun5e.create-navigation')

    @unless ($currentStep ?? false)
    <x-slot name="navbar">
        <li class="nav-item">
            <a class="nav-link" href="{{ route('dashboard') }}">Home</a>
        </li>
        <li class="nav-item">
            <span class="nav-link active">{{ $character }}</span>
        </li>
    </x-slot>
    @endunless

    @if ($errors->any())
        <div class="alert alert-danger mt-4">
            <ul>
                @foreach ($errors->all() as $error)
                    <li>{!! $error !!}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <div class="toast-container position-fixed top-0 end-0 p-3" id="toasts" style="z-index:100">
    </div>

    <div class="row">
        <div class="col">
            <x-Shadowrun5e.metadata :character="$character"/>
            <x-Shadowrun5e.skills :character="$character"/>
        </div>

        <div class="col">
            <x-Shadowrun5e.attributes :character="$character"/>
            <x-Shadowrun5e.qualities :character="$character"/>
        </div>
    </div>

    <div class="row">
        <div class="col">
            <x-Shadowrun5e.weapons :character="$character"/>
        </div>
    </div>

    <div class="row">
        <div class="col">
            <x-Shadowrun5e.augmentations :character="$character"/>
            <x-Shadowrun5e.spells :character="$character"/>
            <x-Shadowrun5e.powers :character="$character"/>
            <x-Shadowrun5e.gear :character="$character"/>
            <x-Shadowrun5e.knowledge :character="$character"/>
        </div>
        <div class="col">
            <x-Shadowrun5e.armor :character="$character"/>
            <x-Shadowrun5e.contacts :character="$character"/>
            <x-Shadowrun5e.identities :character="$character"/>
            <x-Shadowrun5e.martial-arts :character="$character"/>
        </div>
    </div>

    <div class="row mb-4">
        <div class="col">
            <x-Shadowrun5e.vehicles :character="$character"/>
            <x-Shadowrun5e.matrix :character="$character"/>
        </div>
    </div>

    <template id="damage-toast">
        <div class="toast" role="alert" aria-live="assertive" aria-atomic="true" data-bs-autohide="false">
            <div class="toast-header">
                <svg class="bd-placeholder-img rounded me-2" width="20" height="20" xmlns="http://www.w3.org/2000/svg" aria-hidden="true" preserveAspectRatio="xMidYMid slice" focusable="false"><rect width="100%" height="100%" fill="#e66465"></rect></svg>
                <strong class="me-auto title">Damage</strong>
                <button type="button" class="btn-close" data-bs-dismiss="toast" aria-label="Close"></button>
            </div>
            <div class="toast-body">
                <div class="stun"></div>
                <div class="physical"></div>
                <div class="overflow"></div>
            </div>
        </div>
    </template>

    <x-slot name="javascript">
        <script>
            $(function () {
                $('[data-bs-toggle="tooltip"]').tooltip();
                Echo.private('App.Models.Shadowrun5e.Character.{{ $character->id }}')
                    .notification((notification) => {
                        let toastFragment = $($('#damage-toast')[0].content.cloneNode(true));

                        let stun = '';
                        let physical = '';
                        let overflow = '';
                        if (0 < notification.stun) {
                            stun = notification.stun + ' point';
                            if (1 !== notification.stun) {
                                stun += 's';
                            }
                            stun += ' of stun damage!';
                        }
                        if (0 < notification.physical) {
                            physical = notification.physical + ' point';
                            if (1 !== notification.physical) {
                                physical += 's';
                            }
                            physical += ' of physical damage!';
                        }
                        if (0 < notification.overflow) {
                            overflow = notification.overflow + ' point';
                            if (1 !== notification.overflow) {
                                overflow += 's';
                            }
                            overflow += ' of overflow damage!';
                        }
                        toastFragment.find('.stun').html(stun);
                        toastFragment.find('.physical').html(physical);
                        toastFragment.find('.overflow').html(overflow);
                        $('#toasts').append(toastFragment);

                        let toast = new bootstrap.Toast($('#toasts').children().last());
                        toast.show();
                    });
            });
        </script>
    </x-slot>
</x-app>
