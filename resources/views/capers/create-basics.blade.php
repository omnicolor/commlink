<x-app>
    <x-slot name="title">Create character: Basics</x-slot>
    @include('capers.create-navigation')

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

    <form action="{{ route('capers.create-basics') }}" id="form" method="POST"
        @if ($errors->any()) class="was-validated" @endif
        novalidate>
    @csrf
    <div class="my-4 row">
        <div class="col-1"></div>
        <div class="col">
            <h1>The basics</h1>
        </div>
        <div class="col-1"></div>
    </div>

    <div class="my-1 row">
        <div class="col-1"></div>
        <label class="col-2 col-form-label" for="name">Name</label>
        <div class="col">
            <input class="form-control" id="name" name="name" required
                type="text" value="{{ $name }}">
            <div class="invalid-feedback">The name field is required.</div>
        </div>
        <div class="col-1"></div>
    </div>

    <div class="my-1 row">
        <div class="col-1"></div>
        <label class="col-2 col-form-label" for="description">
            Description
        </label>
        <div class="col">
            <textarea class="form-control" id="description" name="description">{{ $description }}</textarea>
        </div>
        <div class="col-1"></div>
    </div>

    <div class="my-1 row">
        <div class="col-1"></div>
        <label class="col-2 col-form-label" for="background">
            Background
        </label>
        <div class="col">
            <textarea class="form-control" id="background" name="background">{{ $background }}</textarea>
        </div>
        <div class="col-1"></div>
    </div>

    <div class="my-1 row">
        <div class="col-1"></div>
        <label class="col-2 col-form-label" for="mannerisms">
            Mannerisms
        </label>
        <div class="col">
            <textarea class="form-control" id="mannerisms" name="mannerisms">{{ $mannerisms }}</textarea>
        </div>
        <div class="col-1"></div>
    </div>

    <div class="my-1 row">
        <div class="col-1"></div>
        <div class="col-2 col-form-label">Type</div>
        <div class="col">
            <div class="form-check" data-bs-toggle="tooltip"
                title="The bulk of the people in the world are Regulars. These are your average, run-of-the-mill folk. While there are certainly talented people among their number, they are limited by what the human body, mind, and spirit have been capable of throughout the ages.">
                <input class="form-check-input" disabled id="type-regular"
                    name="type" required type="radio" value="regular">
                <label class="form-check-label" for="type-regular">
                    Regular
                </label>
            </div>
            <div class="form-check" data-bs-toggle="tooltip" data-bs-html="true"
                title="<p>These people are special in some way. They are tougher and more skilled than Regulars, possessing special talents called Perks and a sort of inherent importance granted them by the cosmos for no particular reason.<p><p>Exceptionals typically have a broader base of good general capabilities along with special Perks. If you’re playing an Exceptional, use the Perks but not Powers.</p>">
                <input class="form-check-input" id="type-exceptional"
                    @if ('exceptional' === $type) checked @endif
                    name="type" type="radio" value="exceptional">
                <label class="form-check-label" for="type-exceptional">
                    Exceptional
                </label>
            </div>
            <div class="form-check" data-bs-toggle="tooltip" data-bs-html="true"
                title="<p>Capers possess one or more unique characteristics that stretch beyond the normal realm of man.</p><p>Capers have a narrower base of general capabilities augmented by fantastic Powers. If you’re playing a Caper, use the Powers but not Perks.">
                <input class="form-check-input" id="type-caper"
                    @if ('caper' === $type) checked @endif
                    name="type" type="radio" value="caper">
                <label class="form-check-label" for="type-caper">
                    Caper
                </label>
                <div class="invalid-feedback" style="margin-left:-1.5rem">You must choose a character type.</div>
            </div>
        </div>
        <div class="col-1"></div>
    </div>

    <div class="my-1 row">
        <div class="col-1"></div>
        <div class="col text-end">
            <button class="btn btn-primary" name="nav" type="submit"
                value="anchors">
                Next: Anchors
            </button>
        </div>
        <div class="col-1"></div>
    </div>
    </form>

    <x-slot name="javascript">
        <script>
			(function () {
				'use strict';

                const tooltipTriggerList = [].slice.call(
                    document.querySelectorAll('[data-bs-toggle="tooltip"]')
                );
                tooltipTriggerList.map(function (tooltipTriggerEl) {
                    return new bootstrap.Tooltip(tooltipTriggerEl);
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
