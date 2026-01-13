<x-app>
    <x-slot name="title">Create character: Attributes</x-slot>
    @include('stillfleet::create-navigation')

    @if ($errors->any())
        <div class="my-4 row">
            <div class="col-1"></div>
            <div class="col">
                <div class="alert alert-danger">
                    <ul>
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            </div>
            <div class="col-1"></div>
        </div>
    @endif

    <form action="{{ route('stillfleet.create-attributes') }}" method="POST">
    @csrf
    <div class="my-4 row">
        <div class="col-1"></div>
        <div class="col">
            <h1>Assign attributes</h1>

            <h2 class="mt-4">Dice options</h2>
            <p>First, you need to choose the dice you have available:</p>

            <ul class="list-group">
                <li class="list-group-item">
                    <label class="form-label">
                        <input class="form-check-input" name="dice-option"
                               @if ('option1' === $option) checked @endif
                               required type="radio" value="option1">
                        d12, d10, d8, d6, d6
                    </label>
                </li>
                <li class="list-group-item">
                    <label class="form-label">
                        <input class="form-check-input" name="dice-option"
                               @if ('option2' === $option) checked @endif
                               required type="radio" value="option2">
                        d12, d10, d8, d8, d4
                    </label>
                    <span class="ms-4 text-muted">Note: A d4 score will limit you to easy, aided checks only!</span>
                </li>
            </ul>

            <h2 class="mt-4">Scores (checks)</h2>
            <ul class="list-group">
                <li class="list-group-item">
                    <div class="row d-flex align-items-center">
                        <div class="col"><span class="fs-1 align-middle">COM</span>bat - attack, grapple</div>
                        <div class="col">
                            <select class="form-control" id="COM" name="COM" required>
                                <option value="">Choose die</option>
                            </select>
                        </div>
                        <div class="col-1 fs-1 align-middle">
                            {{ $character->combat_modifier !== 0 ? '+' . $character->combat_modifier : '' }}
                        </div>
                    </div>
                </li>
                <li class="list-group-item">
                    <div class="row d-flex align-items-center">
                        <div class="col"><span class="fs-1 align-middle">MOV</span>ement - drive/pilot, dodge, initiate</div>
                        <div class="col">
                            <select class="form-control" id="MOV" name="MOV" required>
                                <option value="">Choose die</option>
                            </select>
                        </div>
                        <div class="col-1 fs-1 align-middle">
                            {{ $character->movement_modifier !== 0 ? '+' . $character->movement_modifier : '' }}
                        </div>
                    </div>
                </li>
                <li class="list-group-item">
                    <div class="row d-flex align-items-center">
                        <div class="col"><span class="fs-1 align-middle">REA</span>son - heal, know, make/repair</div>
                        <div class="col">
                            <select class="form-control" id="REA" name="REA" required>
                                <option value="">Choose die</option>
                            </select>
                        </div>
                        <div class="col-1 fs-1 align-middle">
                            {{ $character->reason_modifier !== 0 ? '+' . $character->reason_modifier : '' }}
                        </div>
                    </div>
                </li>
                <li class="list-group-item">
                    <div class="row d-flex align-items-center">
                        <div class="col"><span class="fs-1 align-middle">WIL</span>l - empathize, perceive, resist</div>
                        <div class="col">
                            <select class="form-control" id="WIL" name="WIL" required>
                                <option value="">Choose die</option>
                            </select>
                        </div>
                        <div class="col-1 fs-1 align-middle">
                            {{ $character->will_modifier !== 0 ? '+' . $character->will_modifier : '' }}
                        </div>
                    </div>
                </li>
                <li class="list-group-item">
                    <div class="row d-flex align-items-center">
                        <div class="col"><span class="fs-1 align-middle">CHA</span>rm - control, negotiate, seduce</div>
                        <div class="col">
                            <select class="form-control" id="CHA" name="CHA" required>
                                <option value="">Choose die</option>
                            </select>
                        </div>
                        <div class="col-1 fs-1 align-middle">
                            {{ $character->charm_modifier !== 0 ? '+' . $character->charm_modifier : '' }}
                        </div>
                    </div>
                </li>
            </ul>

            <h2 class="mt-4">Pool</h2>
            <ul class="list-group">
                <li class="list-group-item">
                    <div class="row d-flex align-items-center">
                        <div class="col"><span class="fs-1 align-middle">HEA</span>lth</div>
                        <div class="col fs-1 align-middle text-center">
                            <span id="health"></span>
                            <div class="fs-6 text-muted">(maxCOM &plus; maxMOV)</div>
                        </div>
                        <div class="col-1"></div>
                    </div>
                </li>
                <li class="list-group-item">
                    <div class="row d-flex align-items-center">
                        <div class="col"><span class="fs-1 align-middle">GR<span class="fs-6 align-middle">i</span>T</span></div>
                        <div class="col fs-1 align-middle text-center">
                            <span id="grit"></span>
                            <div class="fs-6 text-muted">
                                (max{{ $grit[0] }} &plus; max{{ $grit[1] }})
                            </div>
                        </div>
                        <div class="col-1"></div>
                    </div>
                </li>
            </ul>

            <button class="btn btn-primary my-4" type="submit">Set attributes</button>
        </div>
        <div class="col-1"></div>
    </div>
    </form>

    <x-slot name="javascript">
        <script>
            const options = {
                option1: ['d12', 'd10', 'd8', 'd6', 'd6'],
                option2: ['d12', 'd10', 'd8', 'd8', 'd4']
            };
            const grit = {!! json_encode($grit) !!};
            const attributes = {
                'COM': {!! $character->combat ? '\'' . $character->combat . '\'' : 'null' !!},
                'MOV': {!! $character->movement ? '\'' . $character->movement . '\'' : 'null' !!},
                'REA': {!! $character->reason ? '\'' . $character->reason . '\'' : 'null' !!},
                'WIL': {!! $character->will ? '\'' . $character->will . '\'' : 'null' !!},
                'CHA': {!! $character->charm ? '\'' . $character->charm . '\'' : 'null' !!}
            };

            function initializeForm() {
                const checked = $('input[name="dice-option"]:checked').val();
                if (undefined === checked) {
                    $('select').val('')
                        .prop('disabled', true)
                        .prop('title', 'Choose dice option first');
                    return;
                }

                setDiceOptions(checked);
                setAttributes();
            }

            function setDiceOptions(choice) {
                const dice = options[choice];
                let html = '<option value="">Choose die</option>';
                $.each(dice, function (index, value) {
                    html += '<option value="' + value + '" data-position="'
                        + (1 + index) + '">' + value + '</option>';
                });
                $('select').html(html)
                    .prop('disabled', false)
                    .prop('title', '');
            }

            function setAttributes() {
                $.each(attributes, function (attribute, value) {
                    $('#' + attribute)
                        .val(value)
                        .change();
                });
            }

            $(function () {
                'use strict';

                $('input[type="radio"]').on('change', function (event) {
                    setDiceOptions($(event.target).val());
                    $('#grit').text('');
                    $('#health').text('');
                });

                $('select').on('change', function (event) {
                    const changed = $(event.target);
                    const id = changed.prop('id');
                    const chosen = changed.find(':selected').data('position');
                    const die = changed.val();
                    if ('' === die) {
                        attributes[id] = null;
                    } else {
                        attributes[id] = die;
                    }

                    if (undefined != changed.data('last')) {
                        const last = changed.data('last');
                        $.each($('select'), function (index, el) {
                            $(el.options[last]).prop('disabled', false);
                        });
                    }
                    changed.data('last', chosen);

                    // Disable the corresponding items.
                    $.each($('select'), function (index, el) {
                        if (el.id !== id) {
                            $(el.options[chosen]).prop('disabled', true);
                        }
                    });

                    if (grit.includes(id)) {
                        if (null === attributes[grit[0]] || null === attributes[grit[1]]) {
                            $('#grit').text('');
                        } else {
                            $('#grit').text(
                                parseInt(attributes[grit[0]].substring(1), 10)
                                + parseInt(attributes[grit[1]].substring(1), 10)
                            );
                        }
                    }

                    if (['COM', 'MOV'].includes(id)) {
                        if (null === attributes['COM'] || null === attributes['MOV']) {
                            $('#health').text('');
                        } else {
                            $('#health').text(
                                parseInt(attributes['COM'].substring(1), 10)
                                + parseInt(attributes['MOV'].substring(1), 10)
                            );
                        }
                    }
                });
                initializeForm();
            });
        </script>
    </x-slot>
</x-app>
