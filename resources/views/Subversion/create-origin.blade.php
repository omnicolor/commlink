<x-app>
    <x-slot name="title">Create character: Origin</x-slot>
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
            <h1>Origin</h1>
            <p>
                Origin represents the culture in which you were raised or with
                which you identify via shared experiences. While in many cases
                this corresponds to the part of the world the character was
                raised in (particularly if their family had long roots there),
                this is not always the case, especially in Neo Babylon were
                origin may derive from their family heritage (even back several
                generations), or from living near or within an expatriate
                community which influenced them. In any case, an origin is an
                influential part of any character's identity (regardless of how
                much they consciously identify with their background). Each PC
                chooses one origin at character creation.
            </p>
        </div>
        <div class="col-4"></div>
    </div>

    <form action="{{ route('subversion.create-origin') }}" id="form" method="POST">
    @csrf

    <div class="my-4 row">
        <div class="col-1"></div>
        <div class="col">
            <div class="accordion" id="origin-accordion">
                @foreach ($origins as $origin)
                    @php
                        $shown = ($loop->first && null === $originId)
                            || $originId === $origin->id;
                    @endphp
                <div class="accordion-item">
                    <h2 class="accordion-header" id="{{ $origin->id }}-header">
                        <button
                            class="accordion-button @if (!$shown) collapsed @endif"
                            type="button"
                            data-bs-toggle="collapse"
                            data-bs-target="#{{ $origin->id }}-collapse"
                            aria-expanded="{{ $shown ? 'true' : 'false' }}"
                            aria-controls="{{ $origin->id }}-collapse">
                            {{ $origin }}
                        </button>
                    </h2>
                    <div
                        id="{{ $origin->id }}-collapse"
                        class="accordion-collapse collapse @if ($shown) show @endif"
                        aria-labelledby="{{ $origin->id }}-header"
                        data-bs-parent="#origin-accordion">
                        <div class="accordion-body">
                            <p>{{ $origin->more }}</p>
                            @can('view data')
                            <p>{!! str_replace('||', '</p><p>', $origin->description) !!}</p>
                            @endcan

                            <button class="btn btn-primary" name="origin"
                                type="submit" value="{{ $origin->id }}">
                                @if (null === $originId)
                                Choose
                                @elseif ($shown)
                                Keep
                                @else
                                Change to
                                @endif
                                {{ $origin }}
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
