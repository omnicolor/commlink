<x-app>
    <x-slot name="title">Create character</x-slot>
    <x-slot name="head">
        <style>
            .points {
                position: fixed;
                right: 0;
                top: 5em;
            }
            .tooltip-inner {
                max-width: 600px;
                text-align: left;
            }
            tr.invalid {
                opacity: .5;
            }
            #points-button {
                position: fixed;
                right: 0;
                top: 5rem;
            }
            .offcanvas {
                border-bottom: 1px solid rgba(0, 0, 0, .2);
                border-top: 1px solid rgba(0, 0, 0, .2);
                bottom: 5rem;
                top: 4.5rem;
                width: 300px;
            }
        </style>
    </x-slot>
    @include('Shadowrun5e.create-navigation')

    @if ($errors->any())
        <div class="alert alert-danger mt-4">
            <ul>
                @foreach ($errors->all() as $error)
                <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <form action="{{ route('shadowrun5e.create-rules') }}" method="POST">
    @csrf

    <div class="row">
        <div class="col-1"></div>
        <div class="col">
            <h1>Rules</h1>
        </div>
    </div>

    <div class="row">
        <div class="col-1"></div>
        <div class="col">
            If this character is for a campaign, your GM should have given you a
            link that will prepopulate all of the options on this page. If not,
            check with your GM to figure out what level the campaign is (street,
            established, prime), what rulebooks are allowed, and the starting
            date of the campaign. They may also give other rules about what is
            or isn't allowed at their table.
        </div>
        <div class="col-2"></div>
    </div>

    <div class="row mt-2">
        <div class="col-1"></div>
        <div class="col-3 form-check-label col-form-label">
            Creation system
        </div>
        <div class="col">
            <div class="form-check">
                <input checked class="form-check-input" id="system-priority"
                    @if ('priority' === ($character->priorities['system'] ?? 'priority'))
                        checked
                    @endif
                    name="system" type="radio" value="priority">
                <label class="form-check-label" data-bs-placement="right"
                    data-bs-toggle="tooltip" for="system-priority"
                    title="Standard priority-based character generation from the Core Rulebook.">
                    Priority
                </label>
            </div>
            <div class="form-check">
                <input class="form-check-input"
                    @if (is_array($selectedBooks) && !in_array('run-faster', $selectedBooks))
                        disabled
                    @elseif ('sum-to-ten' === ($character->priorities['system'] ?? 'priority'))
                        checked
                    @endif
                    id="system-ten" name="system" type="radio"
                    value="sum-to-ten">
                <label class="form-check-label" data-bs-placement="right"
                    data-bs-toggle="tooltip" for="system-ten"
                    title="Sum to Ten priority system from Run Faster page 62.">
                    Sum to Ten
                </label>
            </div>
            <div class="form-check disabled">
                <input class="form-check-input" disabled id="system-karma"
                    name="system" type="radio" value="karma">
                <label class="form-check-label" data-bs-placement="right"
                    data-bs-toggle="tooltip" for="system-karma"
                    title="Karma buy system from Run Faster page 64. Not yet implemented.">
                    Karma Buy
                </label>
            </div>
            <div class="form-check disabled">
                <input class="form-check-input" disabled id="system-life"
                    name="system" type="radio" value="life">
                <label class="form-check-label" data-bs-placement="right"
                    data-bs-toggle="tooltip" for="system-life"
                    title="Life module system from Run Faster page 65. Not yet implemented.">
                    Life Modules
                </label>
            </div>
        </div>
        <div class="col-2"></div>
    </div>

    <div class="row mt-2">
        <div class="col-1"></div>
        <label class="col-3 col-form-label">Start date</label>
        <div class="col">
            <input aria-describedby="date-help" class="form-control"
                id="start-date" name="start-date" type="date"
                @if (isset($character->priorities['startDate']))
                    value="{{ $character->priorities['startDate'] }}"
                @endif
                >
            <small class="form-text text-muted" id="date-help">
                The start date lets the system determine how old your
                character is. mm/dd/yyyy format.
            </small>
        </div>
        <div class="col-2"></div>
    </div>

    <div class="row mt-2">
        <div class="col-1"></div>
        <div class="col-3 form-check-label">Gameplay</div>
        <div class="col">
            <div class="form-check">
                <input class="form-check-input" id="gameplay-street" name="gameplay"
                    @if ('street' === ($character->priorities['gameplay'] ?? 'established'))
                        checked
                    @endif
                    type="radio" value="street">
                <label class="form-check-label" data-bs-placement="right"
                    data-bs-toggle="tooltip" for="gameplay-street"
                    title="Street-level runners: characters that have not yet had a chance to establish themselves as runners and are still in the process of earning their street cred. Obviously, these characters will not have the same gear or resources as the experienced shadowrunner.">
                    Street
                </label>
            </div>
            <div class="form-check">
                <input class="form-check-input" id="gameplay-established"
                    @if ('established' === ($character->priorities['gameplay'] ?? 'established'))
                        checked
                    @endif
                    name="gameplay" type="radio" value="established">
                <label class="form-check-label" data-bs-placement="right"
                    data-bs-toggle="tooltip" for="gameplay-established"
                    title="Standard runners: characters have progressed in their careers long enough to not immediately be geeked at the first sign of trouble, but are still relatively unknown to the Johnsons of the world.">
                Established
                </label>
            </div>
            <div class="form-check">
                <input class="form-check-input" id="gameplay-prime" name="gameplay"
                    @if ('prime' === ($character->priorities['gameplay'] ?? 'established'))
                        checked
                    @endif
                    type="radio" value="prime">
                <label class="form-check-label" data-bs-placement="right"
                    data-bs-toggle="tooltip" for="gameplay-prime"
                    title="Prime runners: characters who have successfully been running the shadows long enough to have established their reputations as professionals in the eyes of Mr. Johnson. They possess the gear, the connections, and the talent to back up those reputations.">
                Prime
                </label>
            </div>
        </div>
        <div class="col-2"></div>
    </div>

    <div class="row mt-2">
        <div class="col-1"></div>
        <div class="col-3 form-check-label">Rulebooks</div>
        <div class="col">
            @foreach ($books['even'] as $book)
            <div class="form-check">
                <input
                    @if ($book->required)
                        required
                    @endif
                    @if ((false !== $selectedBooks && in_array($book->id, $selectedBooks)) || (false === $selectedBooks && $book->default))
                        checked
                    @endif
                    class="form-check-input"
                    id="rulebook-{{ $book->id }}" name="rulebook[]"
                    type="checkbox" value="{{ $book->id }}">
                <label class="form-check-label" for="rulebook-{{ $book->id }}"
                    title="{{ $book->description }}">
                    {{ $book }}
                    @if ($book->required)
                        <small class="text-muted">(Required)</small>
                    @endif
                </label>
            </div>
            @endforeach
        </div>
        <div class="col">
            @foreach ($books['odd'] as $book)
            <div class="form-check">
                <input checked class="form-check-input"
                    id="rulebook-{{ $book->id }}" name="rulebook[]"
                    type="checkbox" value="{{ $book->id }}">
                <label class="form-check-label" for="rulebook-{{ $book->id }}"
                    title="{{ $book->description }}">
                    {{ $book }}
                </label>
            </div>
            @endforeach
        </div>
        <div class="col-2"></div>
    </div>

    @include('Shadowrun5e.create-next')
    </form>

    @include('Shadowrun5e.create-points', ['hide' => true])

    <x-slot name="javascript">
        <script>
            let character = @json($character);
        </script>
        <script src="/js/Shadowrun5e/Points.js"></script>
        <script src="/js/Shadowrun5e/create-common.js"></script>
        <script src="/js/Shadowrun5e/create-rules.js"></script>
    </x-slot>
</x-app>
