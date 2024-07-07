<x-app>
    <x-slot name="title">Create character: Ideology</x-slot>
    @include('subversion::create-navigation')

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

    <form action="{{ route('subversion.create-ideology') }}" id="form" method="POST">
    @csrf

    <div class="my-4 row">
        <div class="col-1"></div>
        <div class="col">
            <h1>Ideology</h1>
            <p>
                Ideology and Values are the fundamental beliefs of the PC. They
                guide the PC’s behavior and frame how the PC decides right and
                wrong. Ideology and Values CAN be contradictory, whether
                slightly or directly. Ideology and Values influence what
                decisions a character makes as well as providing a drive to the
                character—a character that consistently lives up to their
                beliefs gain Grit, while those that betray them lose Grit (see
                &ldquo;Values&rdquo; on pg 29). First, choose an ideology, then
                choose Values.
            </p>

            <p>
                Every Envoy brings with them their own set of beliefs and
                opinions about the world (and how it might be made better).
                Centered around the Envoy movement (though Factions are made up
                in large part of nonenvoys), these (often competing) core
                beliefs have settled into a number of Ideological blocs which
                push towards a slightly different aim. Most Envoys are aligned
                with one of these ideologies, representing the values and
                approach they’re most aligned with. Ideologies serve both as a
                personal Value, but also as a network Envoys may rely on for
                help if need be. Each Ideology has an associated Value and
                Faction.
            </p>
        </div>
        <div class="col-4"></div>
    </div>

    <div class="my-4 row">
        <div class="col-1"></div>
        <div class="col">
            <div class="accordion" id="ideology-accordion">
                @foreach ($ideologies as $ideology)
                    @php
                        $shown = $ideologyId === $ideology->id
                            || ('aiders' === $ideology->id && null === $ideologyId);
                    @endphp
                <div class="accordion-item">
                    <h2 class="accordion-header" id="{{ $ideology->id }}-header">
                        <button
                            class="accordion-button @if (!$shown) collapsed @endif"
                            type="button"
                            data-bs-toggle="collapse"
                            data-bs-target="#{{ $ideology->id }}-collapse"
                            aria-expanded="{{ $shown ? 'true' : 'false' }}"
                            aria-controls="{{ $ideology->id }}-collapse">
                            {{ $ideology }}
                        </button>
                    </h2>
                    <div
                        id="{{ $ideology->id }}-collapse"
                        class="accordion-collapse collapse @if ($shown) show @endif"
                        aria-labelledby="{{ $ideology->id }}-header"
                        data-bs-parent="#ideology-accordion">
                        <div class="accordion-body">
                            @can('view data')
                            <p>{!! str_replace('||', '</p><p>', $ideology->description) !!}</p>
                            @endcan
                            <p><strong>Value:</strong> {{ $ideology->value }}</p>

                            <p>
                                See page {{ $ideology->page }},
                                {{ $ideology->ruleset }} for more information.
                            </p>

                            <button class="btn btn-primary" name="ideology"
                                type="submit" value="{{ $ideology->id }}">
                                @if (null === $ideologyId)
                                Choose
                                @elseif ($shown)
                                Keep
                                @else
                                Change to
                                @endif
                                {{ $ideology }}
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

    @include('subversion::create-fortune')
</x-app>
