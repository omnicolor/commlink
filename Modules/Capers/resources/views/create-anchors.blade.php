@php
use Modules\Capers\Models\Identity;
use Modules\Capers\Models\Vice;
use Modules\Capers\Models\Virtue;
@endphp
<x-app>
    <x-slot name="title">Create character: Anchors</x-slot>
    <x-slot name="head">
        <style>
            .♣, .♠ {
                color: #000000;
            }
            .♦, .♥ {
                color: #ff0000;
            }
        </style>
    </x-slot>
    @include('capers::create-navigation')

    <form action="{{ route('capers.create-anchors') }}" id="form" method="POST"
        @if ($errors->any()) class="was-validated" @endif
        novalidate>
    @csrf

    @if ($errors->any())
        <div class="my-4 row">
            <div class="col-1"></div>
            <div class="col">
                <div class="alert alert-danger">
                    One or more of the selected skills were invalid.
                </div>
            </div>
            <div class="col-1"></div>
        </div>
    @endif

    <div class="my-4 row">
        <div class="col-1"></div>
        <div class="col">
            <h1>Anchors</h1>
            <p>
                Your character's Anchors – Identity, Virtue, and Vice – are
                their core personality characteristics. Each serves as a guide
                for roleplaying as well as providing you with opportunities to
                earn an important in-game resource called Moxie.
            </p>

            <p>
                You’ll need to make a choice for each of these three roleplaying
                guides eventually. If you have a strong idea of who your
                character is, fill this information in now. As you develop your
                character further, you might return to this section and make
                modifications.
            </p>

            <p>
                The lists of Anchors below are just suggestions. You can develop
                more with the approval of the GM.
            </p>

            <p>
                You can choose your Anchors or determine them randomly. To
                determine them randomly, click the "Random" button to draw
                cards for each.
            </p>
        </div>
        <div class="col-1"></div>
    </div>

    <div class="my-1 row">
        <div class="col-1"></div>
        <div class="col">
            <h2>
                Identity
                <button aria-controls="about-identity" aria-expanded="false"
                    class="btn btn-outline-info btn-sm"
                    data-bs-target="#about-identity" data-bs-toggle="collapse"
                    type="button">
                    ?
                </button>
            </h2>

            <div class="collapse mb-2" id="about-identity">
                <div class="card card-body">
                    <p>
                        Your character’s Identity is who your character is at
                        their core. It’s a guiding principle for your
                        roleplaying. It’s a term that other characters might use
                        when describing your character’s actions and beliefs.
                    </p>

                    <p>
                        But it’s not everything. Your character can have many
                        facets to their personality. But, when in doubt about
                        how your character might react to a situation, their
                        Identity can make for a pretty good guide.
                    </p>

                    <p>
                        And keep in mind your character’s Identity might change
                        over time. Actions have consequences. If your character
                        experiences a trauma or a great victory or something
                        else that changes their viewpoint on life and
                        themselves, change their Identity.
                    </p>

                    <p>
                        You can gain Moxie if your character stays consistent to
                        their Identity.
                    </p>
                </div>
            </div>
        </div>
        <div class="col-1"></div>
    </div>

    <div class="row">
        <div class="col-1"></div>
        <label for="identity" class="col-1 col-form-label">
            Identity
            <span id="identity-card"></span>
        </label>
        <div class="col">
            <select class="form-control" id="identity" name="identity" required>
                <option value="">Choose identity</option>
                @foreach (Identity::all() as $option)
                    <option @if ($identity === $option->id) selected @endif
                        value="{{ $option->id }}">
                        {{ $option }} ({{ $option->card}})
                    </option>
                @endforeach
            </select>
            <div class="invalid-feedback">You must choose an identity.</div>
        </div>
        <div class="col" id="identity-description">
            @if ('' !== $identity)
                {{ (new Identity($identity))->description }}
            @endif
        </div>
        <div class="col-1"></div>
    </div>

    <div class="mt-4 row">
        <div class="col-1"></div>
        <div class="col">
            <h2>
                Virtue
                <button aria-controls="about-virtue" aria-expanded="false"
                    class="btn btn-outline-info btn-sm"
                    data-bs-target="#about-virtue" data-bs-toggle="collapse"
                    type="button">
                    ?
                </button>
            </h2>

            <div class="collapse mb-2" id="about-virtue">
                <div class="card card-body">
                    <p>
                        Your character’s Virtue is their most morally
                        commendable characteristic. It’s something that others
                        respect and look up to them for. It’s a quality they
                        never betray no matter the cost.
                    </p>

                    <p>
                        You can gain Moxie if your character stays true to their
                        Virtue when it would be easier to ignore it to
                        accomplish something.
                    </p>
                </div>
            </div>
        </div>
        <div class="col-1"></div>
    </div>

    <div class="row">
        <div class="col-1"></div>
        <label for="vice" class="col-1 col-form-label">
            Virtue
            <span id="virtue-card"></span>
        </label>
        <div class="col">
            <select class="form-control" id="virtue" name="virtue" required>
                <option value="">Choose virtue</option>
                @foreach (Virtue::all() as $option)
                    <option
                        @if ($virtue === $option->id) selected @endif
                        value="{{ $option->id }}">
                        {{ $option }} ({{ $option->card}})
                    </option>
                @endforeach
            </select>
            <div class="invalid-feedback">You must choose a virtue.</div>
        </div>
        <div class="col" id="virtue-description"></div>
        <div class="col-1"></div>
    </div>

    <div class="mt-4 row">
        <div class="col-1"></div>
        <div class="col">
            <h2>
                Vice
                <button aria-controls="about-vice" aria-expanded="false"
                    class="btn btn-outline-info btn-sm"
                    data-bs-target="#about-vice" data-bs-toggle="collapse"
                    type="button">
                    ?
                </button>
            </h2>

            <div class="collapse mb-2" id="about-vice">
                <div class="card card-body">
                    <p>
                        Your character’s Vice is their greatest weakness. It
                        regularly causes problems for them and takes a long time
                        to overcome, if they manage to overcome it at all.
                    </p>

                    <p>
                        You can gain Moxie if your character is hindered by
                        their Vice.
                    </p>
                </div>
            </div>
        </div>
        <div class="col-1"></div>
    </div>

    <div class="mb-4 row">
        <div class="col-1"></div>
        <label for="vice" class="col-1 col-form-label">
            Vice
            <span id="vice-card"></span>
        </label>
        <div class="col">
            <select class="form-control" id="vice" name="vice" required>
                <option value="">Choose vice</option>
                @foreach (Vice::all() as $option)
                    <option
                        @if ($vice === $option->id) selected @endif
                        value="{{ $option->id }}">
                        {{ $option }} ({{ $option->card}})
                    </option>
                @endforeach
            </select>
            <div class="invalid-feedback">You must choose a vice.</div>
        </div>
        <div class="col" id="vice-description"></div>
        <div class="col-1"></div>
    </div>

    <div class="my-4 row"></div>

    <div class="my-4 row">
        <div class="col-1"></div>
        <div class="col text-start">
            <button class="btn btn-success" id="random" type="button">
                Random
            </button>
        </div>
        <div class="col text-end">
            <button class="btn btn-secondary" name="nav" type="submit"
                value="basics">
                Previous: Basics
            </button>
            <button class="btn btn-primary" name="nav" type="submit"
                value="traits">
                Next: Traits
            </button>
        </div>
        <div class="col-1"></div>
    </div>

    </form>

    <x-slot name="javascript">
        <script>
            const suits = ['♣', '♦', '♥', '♠'];
            const identities = {
                @foreach (Identity::all() as $identity)
                    '{{ $identity->id }}': '{{ $identity->description }}',
                @endforeach
                '': ''
            };
            const virtues = {
                @foreach (Virtue::all() as $virtue)
                    '{{ $virtue->id }}': '{{ $virtue->description }}',
                @endforeach
                '': ''
            };
            const vices = {
                @foreach (Vice::all() as $vice)
                    '{{ $vice->id }}': '{{ $vice->description }}',
                @endforeach
                '': ''
            };

            function shuffle(array) {
                let curId = array.length;
                while (0 !== curId) {
                    let randId = Math.floor(Math.random() * curId);
                    curId -= 1;
                    let tmp = array[curId];
                    array[curId] = array[randId];
                    array[randId] = tmp;
                }
                return array;
            }

            function createDeck() {
                const cards = [
                    '2', '3', '4', '5', '6', '7', '8', '9', '10', 'J', 'Q',
                    'K', 'A'
                ];
                let deck = [];
                $.each(suits, function (index, suit) {
                    $.each(cards, function (index, card) {
                        deck.push(card + suit);
                    });
                });
                return shuffle(deck);
            }

            function setCard(element, card) {
                const suit = card.slice(-1);
                element.removeClass(suits.join(' '));
                element.addClass(suit);
                element.html(card);
            }

            function cardToValue(card) {
                const value = card.slice(0, -1);
                switch (value) {
                    case 'A':
                        return 14;
                    case 'K':
                        return 13;
                    case 'Q':
                        return 12;
                    case 'J':
                        return 11;
                    default:
                        return parseInt(value, 10);
                }
            }

            function updateIdentity() {
                const card = $('#identity-card').html();
                const suit = card.slice(-1);
                const value = cardToValue(card);
                let offset = 1;
                if (suit === '♣' || suit === '♠') {
                    offset = -12;
                }
                $('#identity')[0].selectedIndex = value - offset;
                $('#identity-description')
                    .html(identities[$('#identity')[0].value]);
            }

            function updateVice() {
                const card = $('#vice-card').html();
                const value = cardToValue(card);
                $('#vice')[0].selectedIndex = value - 1;
                $('#vice-description').html(vices[$('#vice')[0].value]);
            }

            function updateVirtue() {
                const card = $('#virtue-card').html();
                const value = cardToValue(card);
                $('#virtue')[0].selectedIndex = value - 1;
                $('#virtue-description').html(virtues[$('#virtue')[0].value]);
            }

			(function () {
				'use strict';

                const tooltipTriggerList = [].slice.call(
                    document.querySelectorAll('[data-bs-toggle="tooltip"]')
                );
                tooltipTriggerList.map(function (tooltipTriggerEl) {
                    return new bootstrap.Tooltip(tooltipTriggerEl);
                });

                $('#identity').on('change', function (e) {
                    $('#identity-description').html(identities[e.target.value]);
                    $('#identity-card').html('');
                });

                $('#virtue').on('change', function (e) {
                    $('#virtue-description').html(virtues[e.target.value]);
                    $('#virtue-card').html('');
                });

                $('#vice').on('change', function (e) {
                    $('#vice-description').html(vices[e.target.value]);
                    $('#vice-card').html('');
                });

                let deck = null;
                $('#random').on('click', function () {
                    if (!deck || deck.length < 3) {
                        deck = createDeck();
                    }
                    setCard($('#identity-card'), deck.shift());
                    setCard($('#virtue-card'), deck.shift());
                    setCard($('#vice-card'), deck.shift());
                    updateIdentity();
                    updateVirtue();
                    updateVice();
                });

                $('#form').on('submit', function (event) {
                    form.classList.add('was-validated');
                    if (!form.checkValidity()) {
                        event.preventDefault();
                        event.stopPropagation();
                        return false;
                    }
                });
            })();
        </script>
    </x-slot>
</x-app>
