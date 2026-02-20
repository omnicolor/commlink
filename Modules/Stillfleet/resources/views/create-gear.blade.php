<x-app>
    <x-slot name="title">Create character: Gear</x-slot>
    <x-slot name="head">
        <link rel="stylesheet" href="/css/datatables.min.css">
    </x-slot>

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

    <div class="my-4 row">
        <div class="col-1"></div>
        <div class="col">
            <h1>Gear</h1>

            <p>You have {{ $money }} voidguilder to start with.</p>

            <form action="" method="POST">
                @csrf

                <ul class="list-group" id="purchased-gear">
                    <li class="list-group-item" id="no-gear">
                        You have no gear.
                    </li>
                    <li class="list-group-item" id="no-armor">
                        You have no armor.
                    </li>
                    <li class="list-group-item" id="no-weapons">
                        You have no weapons.
                    </li>
                    <li class="list-group-item" id="total-row">
                        <div class="row">
                            <div class="col">
                                <button class="btn btn-sm btn-success"
                                        data-bs-toggle="modal"
                                        data-bs-target="#gear-modal" type="button">
                                    <span aria-hidden="true" class="bi bi-plus"></span>
                                    Buy gear
                                </button>
                            </div>
                            <div class="col-2 text-end">
                                <strong>Total:</strong>
                                <strong id="remaining">
                                    {{ number_format($money) }}gl
                                </strong>
                            </div>
                        </div>
                    </li>
                </ul>
            </form>
        </div>
        <div class="col-1"></div>
    </div>

    <div class="modal" id="gear-modal" tabindex="-1" role="dialog">
        <div class="modal-dialog modal-xl" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Choose gear</h5>
                    <button aria-label="Close" class="btn-close"
                            data-bs-dismiss="modal" type="button"></button>
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
                                    <tr data-cost="{{ $item->price }}"
                                        @if ($user->can('view data'))
                                        data-description="{{ $item->description }}"
                                        @endif
                                        data-id="{{ $item->id }}"
                                        data-page="{{ $item->page }}"
                                        data-ruleset="{{ $item->ruleset }}"
                                        data-tech="{{ $item->tech_cost }} {{ $item->tech_strata->name }}">
                                        <td class="name">{{ $item->name }}</td>
                                        <td class="type">{{ $item->type->name }}</td>
                                        <td class="text-end">
                                            {{ number_format($item->price) }}gl
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
                            @if ($user->can('view data'))
                                <p id="item-description"></p>
                            @endif
                            <div class="row mt-2">
                                <div class="col-2">Tech</div>
                                <div class="col" id="item-tech-cost"></div>
                            </div>
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

            /**
             * Handle the user clicking an item from the gear list to
             * potentially purchase it.
             */
            function handleGearClick(e) {
                const row = $(e.target).parents('tr');
                const id = row.data('id');
                const cost = row.data('cost');

                $('#item-name').html(row.find('.name').text());
                $('#item-cost').html(cost);
                $('#item-description').html(row.data('description'));
                $('#item-tech-cost').html(row.data('tech'));
                $('#item-type').html(row.find('.type').text());
                $('#item-quantity').val(1);

                $('#item-buy').data('id', id).data('cost', cost);

                handleUpdatedQuantityModal();
                $('#click-panel').hide();
                $('#info-panel').show();
            }

            /**
             * Handle the user updating the amount of an item they want to buy
             * on the buying modal.
             */
            function handleUpdatedQuantityModal() {
                const button = $('#item-buy');
                const cost = parseFloat(button.data('cost'));
                const quantity = parseInt($('#item-quantity').val(), 10);
                const money = $('#remaining').text().replace('$', '');

                button.prop('disabled', money < cost * quantity);
            }

            const gear_table = $('#gear-list').DataTable({
                columns: [
                    {}, // name
                    {}, // type
                    {} // cost
                ],
                info: false
            });

            $('#gear-list_length').remove();
            $('#gear-list_filter').remove();

            $('#filter-type').on('change', function () {
                gear_table.columns(1).search(this.value).draw();
            });

            $('#gear-list tbody').on('click', 'tr', handleGearClick);
        </script>
    </x-slot>
</x-app>
