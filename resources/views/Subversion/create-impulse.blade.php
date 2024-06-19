<x-app>
    <x-slot name="title">Create character: Impulse</x-slot>
    @include('Subversion.create-navigation')

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
            <div class="col-4"></div>
        </div>
    @endif

    <div class="my-4 row">
        <div class="col-1"></div>
        <div class="col">
            <h1>Impulse</h1>
            <p>
                If Values are a character's motivations driven by their beliefs,
                Impulses are the subconscious motivations driven by their wants,
                fears, and desires. Impulses serve as a coping method for
                dealing with Health, Animity, or Grit loss which may be helpful
                in the short term, but may cause or reflect deeper harm. For
                most, Impulses are a manageable part of their life, a tendency
                balanced by moderation and other drives in their life. For
                others, however, Impulses are core to a self-destructive spiral,
                a compulsive behavior whose short term benefits are outweighed
                by the long term damage it does to themselves and the people
                around them.
            </p>

            <p>
                Each character chooses one Impulse. See Impulses (see page 27,
                core) for more details.
            </p>
        </div>
        <div class="col-4"></div>
    </div>

    <form action="{{ route('subversion.create-impulse') }}" id="form"
        method="POST">
    @csrf

    <div class="my-4 row">
        <div class="col-1"></div>
        <div class="col">
            <div class="accordion" id="impulse-accordion">
                @foreach ($impulses as $impulse)
                    @php
                        $shown = ($loop->first && null === $impulseId)
                            || $impulseId === $impulse->id;
                    @endphp
                <div class="accordion-item">
                    <h2 class="accordion-header" id="{{ $impulse->id }}-header">
                        <button
                            class="accordion-button @if (!$shown) collapsed @endif"
                            type="button"
                            data-bs-toggle="collapse"
                            data-bs-target="#{{ $impulse->id }}-collapse"
                            aria-expanded="{{ $shown ? 'true' : 'false' }}"
                            aria-controls="{{ $impulse->id }}-collapse">
                            {{ $impulse }}
                        </button>
                    </h2>
                    <div
                        id="{{ $impulse->id }}-collapse"
                        class="accordion-collapse collapse @if ($shown) show @endif"
                        aria-labelledby="{{ $impulse->id }}-header"
                        data-bs-parent="#impulse-accordion">
                        <div class="accordion-body">
                            <p>
                                See page {{ $impulse->page }},
                                {{ $impulse->ruleset }} for more information.
                            </p>
                            @can('view data')
                            <p>{{ $impulse->description }}</p>
                            <h5>Triggers:</h5>
                            <p>{{ $impulse->triggers }}</p>
                            @endcan
                            <h5>{{ \Str::plural('Response', count($impulse->responses)) }}:</h5>
                            <ul>
                            @foreach ($impulse->responses as $response)
                                @can('view data')
                                <li>
                                    <strong>{{ $response }}:</strong>
                                    {{ $response->description }}
                                </li>
                                @else
                                <li>{{ $response }}</li>
                                @endcan
                            @endforeach
                            </ul>

                            <h5>Downtime action:</h5>
                            <p>
                                <strong>{{ $impulse->downtime }}</strong>
                                @can('view data')
                                <strong>{{ $impulse->downtime }}:</strong>
                                {{ $impulse->downtime->description }}
                                @else
                                <strong>{{ $impulse->downtime }}</strong>
                                @endcan
                            </p>

                            <button class="btn btn-primary" name="impulse"
                                type="submit" value="{{ $impulse->id }}">
                                @if (null === $impulseId)
                                Choose
                                @elseif ($shown)
                                Keep
                                @else
                                Change to
                                @endif
                                {{ $impulse }}
                            </button>
                        </div>
                    </div>
                </div>
                @endforeach
            </div>
        </div>
        <div class="col-4"></div>
    </div>
    </form>

    @include('Subversion.create-fortune')
</x-app>
