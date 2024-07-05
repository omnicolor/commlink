<x-app>
    <x-slot name="title">Create character: Boosts</x-slot>
    @include('capers::create-navigation')

    <form action="{{ route('capers.create-boosts') }}" id="form" method="POST"
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
            <h1>Boosts</h1>

            <p>
                Powers have Boosts, special augmentations that improve the
                Power's capability, make it more versatile, or provide other
                effects. When your character gains rank 1 in any Power, choose
                three Boosts your character knows for that Power. Each time your
                rank in a Power increases by 1, select one additional Boost your
                character knows for that Power.
            </p>
        </div>
        <div class="col-1"></div>
    </div>

    @foreach ($powers as $power)
    <div class="my-4 row">
        <div class="col-1"></div>
        <div class="col">
            <h2>{{ $power }}</h2>
            <div><small class="text-muted">{{ $power->type }} power</small></div>

            <p>{{ $power->description }}</p>

            <p><strong>Effects:</strong> {{ $power->effect }}</p>
        </div>
        <div class="col-1"></div>
    </div>

    <div class="my-1 row">
        <div class="col-1"></div>
        <div class="col-1">
            Boosts
            <div><small class="text-muted">Choose {{ 2 + $power->rank }}</small></div>
        </div>
        @foreach (collect($power->availableBoosts)->chunk(ceil(count($power->availableBoosts) / 2)) as $chunk)
        <div class="col">
            @foreach ($chunk as $boost)
            <div class="form-check">
                <input class="form-check-input"
                    @if (in_array(sprintf('%s+%s', $power->id, $boost->id), $chosenBoosts, true)) checked @endif
                    id="{{ $power->id}}-{{ $boost->id }}" name="boosts[]"
                    type="checkbox" value="{{ $power->id }}+{{ $boost->id }}">
                <label class="form-check-label" data-bs-toggle="tooltip"
                    for="{{ $power->id }}-{{ $boost->id }}"
                    title="{{ $boost->description }}">
                    {{ $boost }}
                </label>
            </div>
            @endforeach
        </div>
        @endforeach
        <div class="col-1"></div>
    </div>
    @endforeach

    <div class="my-1 row">
        <div class="col-1"></div>
        <div class="col text-end">
            <button class="btn btn-secondary" name="nav" type="submit"
                value="powers">
                Previous: Powers
            </button>
            <button class="btn btn-primary" name="nav" type="submit"
                value="gear">
                Next: Gear
            </button>
        </div>
        <div class="col-1"></div>
    </div>
    </form>

    <x-slot name="javascript">
        <script>
			(function () {
				'use strict';
            })();
        </script>
    </x-slot>
</x-app>
