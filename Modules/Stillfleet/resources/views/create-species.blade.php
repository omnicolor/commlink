<x-app>
    <x-slot name="title">Create character: Species</x-slot>
    <x-slot name="head">
        <style>
            .accordion-header,
            .accordion-collapse {
                border-bottom: var(--bs-accordion-border-width) solid var(--bs-accordion-border-color);
            }

            .accordion-header:has(+ .show) {
                border-bottom: 0;
            }
        </style>
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
            <h1>Species</h1>

            <form action="{{ route('stillfleet.create-species') }}" method="POST">
                @csrf

                <div class="accordion" id="species-list">
                    <div class="accordion-item">
                        @foreach ($all_species as $species)
                            <h2 class="accordion-header" id="heading-{{ $species->id }}">
                                <button aria-controls="collapse-{{ $species->id }}"
                                        aria-expanded="{{ $chosen_species?->id === $species->id ? 'true' : 'false' }}"
                                        class="accordion-button @if ($chosen_species?->id !== $species->id) collapsed @endif"
                                        data-bs-target="#collapse-{{ $species->id }}"
                                        data-bs-toggle="collapse" type="button">
                                {{ $species }}
                            </h2>
                            <div aria-labelledby="heading-{{ $species->id }}"
                                 class="accordion-collapse collapse @if ($chosen_species?->id === $species->id) show @endif"
                                 data-bs-parent="#species-list"
                                 id="collapse-{{ $species->id }}">
                                <div class="accordion-body">
                                    <p><small class="fs-6 text-muted">
                                        {{ ucfirst($species->ruleset) }} p{{ $species->page }}
                                    </small></p>

                                    @can ('view data')
                                        <p>{{ $species->description }}</p>
                                        <p><strong>Languages:</strong> {{ $species->languages }}</p>
                                    @endcan

                                    @if (0 !== count($species->species_powers))
                                    <h3>Species powers:</h3>
                                    <ul>
                                        @foreach ($species->species_powers as $power)
                                            <li>
                                                {{ $power }}
                                                @can ('view data')
                                                    &mdash; {{ $power->description }}
                                                @endcan
                                            </li>
                                        @endforeach
                                    </ul>
                                    @endif

                                    @if (0 !== $species->powers_choose)
                                    <h3>Optional powers (choose {{ $species->powers_choose }}):</h3>
                                    <ul>
                                        @foreach ($species->optional_powers as $power)
                                            <li>
                                                {{ $power }}
                                                @can ('view data')
                                                    &mdash; {{ $power->description }}
                                                @endcan
                                            </li>
                                        @endforeach
                                    </ul>
                                    @endif

                                    <button class="btn btn-primary" name="species" type="submit" value="{{ $species->id }}">
                                        @if ($chosen_species?->id !== $species->id)
                                            Become a {{ $species }}
                                        @else
                                            Remain a {{ $species }}
                                        @endif
                                    </button>
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>
            </form>
        </div>
        <div class="col-1"></div>
    </div>
</x-app>
