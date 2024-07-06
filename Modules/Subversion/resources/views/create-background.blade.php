<x-app>
    <x-slot name="title">Create character: Background</x-slot>
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

    <div class="my-4 row">
        <div class="col-1"></div>
        <div class="col">
            <h1>Background</h1>
            <p>
                The final component of your PC’s identity is their background,
                describing the specific conditions they encountered in life so
                far, and what they learned along the way. While lineage
                describes what they are, and origins tell some of who they are,
                backgrounds explain how they have lived. Each PC chooses one
                background. You can choose a background similar to your
                Community’s background or completely different. Backgrounds are
                purely narrative, so you can explain your character’s journey
                however you like.
            </p>
        </div>
        <div class="col-4"></div>
    </div>

    <form action="{{ route('subversion.create-background') }}" id="form"
        method="POST">
    @csrf

    <div class="my-4 row">
        <div class="col-1"></div>
        <div class="col">
            <div class="accordion" id="background-accordion">
                @foreach ($backgrounds as $background)
                    @php
                        $shown = ($loop->first && null === $backgroundId)
                            || $backgroundId === $background->id;
                    @endphp
                <div class="accordion-item">
                    <h2 class="accordion-header" id="{{ $background->id }}-header">
                        <button
                            class="accordion-button @if (!$shown) collapsed @endif"
                            type="button"
                            data-bs-toggle="collapse"
                            data-bs-target="#{{ $background->id }}-collapse"
                            aria-expanded="{{ $shown ? 'true' : 'false' }}"
                            aria-controls="{{ $background->id }}-collapse">
                            {{ $background }}
                        </button>
                    </h2>
                    <div
                        id="{{ $background->id }}-collapse"
                        class="accordion-collapse collapse @if ($shown) show @endif"
                        aria-labelledby="{{ $background->id }}-header"
                        data-bs-parent="#background-accordion">
                        <div class="accordion-body">
                            @can('view data')
                            <p>{{ $background->description }}</p>
                            @else
                            <p>See page {{ $background->page }} in {{ $background->ruleset }} for more information.</p>
                            @endcan

                            <button class="btn btn-primary" name="background"
                                type="submit" value="{{ $background->id }}">
                                @if (null === $backgroundId)
                                Choose
                                @elseif ($shown)
                                Keep
                                @else
                                Change to
                                @endif
                                {{ $background }}
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
