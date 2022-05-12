<x-app>
    <x-slot name="title">Create character: Gear</x-slot>
    <x-slot name="head">
        <link rel="stylesheet" href="/css/datatables.min.css">
        <style>
            tr.invalid {
                opacity: .5;
            }
        </style>
    </x-slot>

    @include('Capers.create-navigation')

    <form action="{{ route('capers.create-gear') }}" id="form" method="POST"
        @if ($errors->any()) class="was-validated" @endif
        novalidate>
    @csrf

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

    <div class="my-4 row">
        <div class="col-1"></div>
        <div class="col">
            <h1>Gear</h1>

            <p>
                You keep track of what equipment your character owns. You don’t
                need to track everything here, but you should at least cover
                your most commonly used items as well as valuable or rare ones.
            </p>

            <p>
                Your character starts with $150. Purchase whatever gear you want
                with that money. You have
                <span id="remaining">${{ number_format($money, 2) }}</span>
                remaining.
            </p>
        </div>
        <div class="col-1"></div>
    </div>

    <div class="mb-4 row">
        <div class="col-1"></div>
        <div class="col">
            <ul class="list-group" id="purchased-gear">
            @forelse ($gearPurchased as $item)
                <li class="list-group-item">
                    <input name="gear[]" type="hidden" value="{{ $item->id }}">
                    <div class="row">
                        <div class="col">
                            {{ $item->name }}
                        </div>
                        <div class="col-2">
                            <input class="form-control form-control-sm gear text-center"
                                min="0" name="quantity[]" type="number"
                                value="{{ $item->quantity }}">
                        </div>
                        <div class="col-1">
                            @ $<span class="item-cost">{{ $item->cost }}</span> =
                        </div>
                        <div class="col-1 text-end">
                            <span class="item-total">
                                ${{ $item->cost * $item->quantity }}
                            </span>
                        </div>
                    </div>
                </li>
            @empty
                <li class="list-group-item" id="no-gear">
                    You have no gear.
                </li>
            @endforelse
                <li class="list-group-item" id="total-row">
                    <div class="row">
                        <div class="col">
                            <button class="btn btn-sm btn-success"
                                data-bs-toggle="modal"
                                data-bs-target="#gear-modal" type="button">
                                <span aria-hidden="true" class="bi bi-plus"></span>
                                Buy item
                            </button>
                        </div>
                        <div class="col-1">
                            <strong>Total:</strong>
                        </div>
                        <div class="col-1">
                            <strong id="total">
                                ${{ number_format(150 - $money, 2) }}
                            </strong>
                        </div>
                    </div>
                </li>
            </ul>
        </div>
        <div class="col-1"></div>
    </div>

    <div class="my-1 row">
        <div class="col-1"></div>
        <div class="col text-end">
            @if (\App\Models\Capers\Character::TYPE_CAPER === $character->type)
            <button class="btn btn-secondary" name="nav" type="submit"
                value="boosts">
                Previous: Boosts
            </button>
            @elseif (\App\Models\Capers\Character::TYPE_EXCEPTIONAL === $character->type)
            <button class="btn btn-secondary" name="nav" type="submit"
                value="perks">
                Previous: Perks
            </button>
            @endif
            <button class="btn btn-primary" name="nav" type="submit"
                value="review">
                Next: Review
            </button>
        </div>
        <div class="col-1"></div>
    </div>
    </form>

    <div class="modal" id="gear-modal" tabindex="-1" role="dialog">
        <div class="modal-dialog modal-xl" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Choose gear</h5>
                    <button aria-label="Close" class="close"
                        data-bs-dismiss="modal" type="button">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body row">
                    <div class="col">
                        <div class="row mx-1">
                            <select class="col form-control form-control-sm"
                                id="filter-type">
                                <option value="">All gear</option>
                                @foreach ($types as $type)
                                <option>{{ $type }}</option>
                                @endforeach
                            </select>&nbsp;
                            <input class="col form-control form-control-sm"
                                id="search-gear" placeholder="Search gear"
                                type="search">
                        </div>
                        <div class="row">
                            <table class="table" id="gear-list"
                                style="width: 100%;">
                                <thead>
                                    <tr>
                                        <th scope="col">Name</th>
                                        <th scope="col">Type</th>
                                        <th class="text-end" scope="col">Cost</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach ($gear as $item)
                                        <tr data-cost="{{ $item->cost }}"
                                            data-id="{{ $item->id }}">
                                            <td class="name">{{ $item->name }}</td>
                                            <td class="type">{{ $item->getType() }}</td>
                                            <td class="text-end">
                                                ${{ number_format($item->cost, 2) }}
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>
                    <div class="col">
                        <div id="click-panel">
                            Click an item to purchase it.
                        </div>
                        <div id="info-panel" style="display: none;">
                            <h3 id="item-name">.</h3>
                            <small class="text-muted" id="item-type"></small>
                            <div class="row mt-2">
                                <div class="col-2">Cost</div>
                                <div class="col" id="item-cost"></div>
                            </div>
                            <div class="mt-2 row">
                                <div class="col">
                                    <input class="form-control"
                                        id="item-quantity" min="0" type="number">
                                </div>
                                <div class="col">
                                    <button class="btn btn-success" id="item-buy">
                                        Buy item
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <template id="item-row">
        <li class="list-group-item">
            <input name="gear[]" type="hidden">
            <div class="row">
                <div class="col name"></div>
                <div class="col-2">
                    <input class="form-control form-control-sm gear text-center"
                        min="0" name="quantity[]" type="number">
                </div>
                <div class="col-1">@ $<span class="item-cost"></span> =</div>
                <div class="col-1 text-end">
                    <span class="item-total"></span>
                </div>
            </div>
        </li>
    </template>

    <x-slot name="javascript">
        <script src="/js/datatables.min.js"></script>
        <script>
            'use strict';

            const usd = new Intl.NumberFormat(
                `en-US`,
                {
                    currency: `USD`,
                    style: 'currency'
                }
            );

            /**
             * Handle the user clicking to buy an item.
             */
            function handleBuyClick() {
                const button = $('#item-buy');
                const id = button.data('id');
                const cost = parseFloat(button.data('cost'));
                const quantity = parseInt($('#item-quantity').val(), 10);
                const name = $('#item-name').text();

                const row = $($('#item-row')[0].content.cloneNode(true));
                row.find('.name').html(name);
                row.find('input[name="gear[]"]').val(id);
                row.find('input[name="quantity[]"]').val(quantity);
                row.find('.item-cost').html(usd.format(cost).replace('$', ''));
                row.find('.item-total').html(usd.format(cost * quantity));

                const totalRow = $('#total-row');
                row.insertBefore(totalRow);

                // Force an update of the money lists.
                const insertedRow = $('#total-row').prev();
                insertedRow.find('input[name="quantity[]"]').trigger('change');

                $('#no-gear').remove();
                $('#click-panel').show();
                $('#info-panel').hide();
            }

            /**
             * Handle the user clicking an item from the gear list to
             * potentially purchase it.
             */
            function handleGearClick(e) {
                const row = $(e.target).parents('tr');
                const id = row.data('id');
                const cost = parseFloat(row.data('cost'));

                $('#item-name').html(row.find('.name').text());
                $('#item-cost').html(usd.format(cost));
                $('#item-type').html(row.find('.type').text());
                $('#item-quantity').val(1);

                $('#item-buy').data('id', id).data('cost', cost);

                handleUpdatedQuantityModal();
                $('#click-panel').hide();
                $('#info-panel').show();
            }

            /**
             * Update the currently displayed gear list for validity.
             */
            function updateListValidity() {
                const list = $('#gear-list tbody tr');
                const remainingMoney = parseFloat($('#remaining').text().replace('$', ''));
                $.each(list, function (unused, row) {
                    let rowEl = $(row);
                    if (rowEl.children().hasClass('dataTables_empty')) {
                        return;
                    }

                    const cost = parseFloat(rowEl.data('cost'));
                    rowEl.toggleClass('invalid', cost > remainingMoney);
                });
            }

            /**
             * Update the character's remaining money when a quantity of an item
             * is updated or if they add a new purchase.
             */
            function updateMoney() {
                let money = 0;
                $.each($('input.gear'), function (index, el) {
                    el = $(el);
                    const parent = el.parent().parent();
                    const quantity = parseInt(el.val(), 10);
                    const cost = parseFloat(parent.find('.item-cost')[0].innerText);
                    const total = quantity * cost;
                    money += total;
                });
                $('#remaining').text(usd.format(150 - money));
                $('#total').text(usd.format(money));
            }

            /**
             * Handle the user updating the amount of an item they want to buy
             * on the buying modal.
             */
            function handleUpdatedQuantityModal() {
                const button = $('#item-buy');
                const cost = parseFloat(button.data('cost'));
                const quantity = parseInt($('#item-quantity').val(), 10);
                const money = parseFloat($('#remaining').text().replace('$', ''));

                button.prop('disabled', money < cost * quantity);
            }

            function handleUpdateQuantityList(e) {
                const el = $(e.target);
                const parent = el.parent().parent();
                const quantity = parseInt(el.val(), 10);
                const cost = parseFloat(parent.find('.item-cost')[0].innerText);
                const total = quantity * cost;

                parent.find('.item-total').text(usd.format(total));
                updateMoney();
            }

			(function () {
                const table = $('#gear-list').DataTable({
                    columns: [
                        {}, // name
                        {}, // type
                        {} // cost
                    ],
                    info: false
                });
                $('#filter-type').on('change', function () {
                    table.columns(1).search(this.value).draw();
                });
                $('#search-gear').on('keyup', function () {
                    table.search(this.value).draw();
                });
                $('#gear-list_length').remove();
                $('#gear-list_filter').remove();
                updateListValidity();

                $('#purchased-gear').on('change', 'input', handleUpdateQuantityList);
                $('#gear-list tbody').on('click', 'tr', handleGearClick);
                $('#item-quantity').on('change', handleUpdatedQuantityModal);
                $('#item-buy').on('click', handleBuyClick);
            })();
        </script>
    </x-slot>
</x-app>
