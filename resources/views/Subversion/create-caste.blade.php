<x-app>
    <x-slot name="title">Create character: Caste</x-slot>
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

    <form action="{{ route('subversion.create-caste') }}" id="form" method="POST">
    @csrf
    <input name="nav" type="hidden" value="ideology">

    <div class="my-4 row">
        <div class="col-1"></div>
        <div class="col">
            <h1>Caste</h1>
            <p>
                Caste in Neo Babylon is your status in life—it determines not
                just your wealth but what jobs are available to you, where you
                can live, what amenities you have access to, and even what laws
                apply to you and what rights you have when charged with a crime.
            </p>

            <p>
                By default, your caste is the caste of your community, but you
                may decide to be above or below that if it fits your character
                concept—you might be a particularly down on their luck member of
                your community, or have some unique form of privilege. See Caste
                (page 78) for more detailed descriptions of the different
                castes.
            </p>
        </div>
        <div class="col-4"></div>
    </div>

    <div class="my-4 row">
        <div class="col-1"></div>
        <div class="col">
            <div class="accordion" id="caste-accordion">
                @foreach ($castes as $caste)
                    @php
                        $shown = ('lower-middle' === $caste->id && null === $casteId)
                            || $casteId === $caste->id;
                    @endphp
                <div class="accordion-item">
                    <h2 class="accordion-header" id="{{ $caste->id }}-header">
                        <button
                            class="accordion-button @if (!$shown) collapsed @endif"
                            type="button"
                            data-bs-toggle="collapse"
                            data-bs-target="#{{ $caste->id }}-collapse"
                            aria-expanded="{{ $shown ? 'true' : 'false' }}"
                            aria-controls="{{ $caste->id }}-collapse">
                            {{ $caste }}
                        </button>
                    </h2>
                    <div
                        id="{{ $caste->id }}-collapse"
                        class="accordion-collapse collapse @if ($shown) show @endif"
                        aria-labelledby="{{ $caste->id }}-header"
                        data-bs-parent="#caste-accordion">
                        <div class="accordion-body">
                            @can('view data')
                            <p>{!! str_replace('||', '</p><p>', $caste->description) !!}</p>
                            @endcan

                            <button class="btn btn-primary" name="caste"
                                type="submit" value="{{ $caste->id }}">
                                @if (null === $casteId)
                                Choose
                                @elseif ($shown)
                                Keep
                                @else
                                Change to
                                @endif
                                {{ $caste }}
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
