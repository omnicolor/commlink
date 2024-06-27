<x-app>
    <x-slot name="title">Create character</x-slot>
    @include('Transformers.create-navigation')

    <div class="row">
        <div class="col">
            <h1>Create a new transformer</h1>
        </div>
    </div>

    <form action="{{ route('transformers.create-base') }}" method="post">
    @csrf

    @if ($errors->any())
        <div class="alert alert-danger">
            <ul>
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <div class="row">
        <div class="col">
            <h2>00. About</h2>
        </div>
    </div>

    <div class="row mt-1">
        <div class="col-1">
            <label class="col-form-label" for="name">Name</label>
        </div>
        <div class="col">
            <input aria-describedby="name-help" class="form-control" id="name"
                name="name" required type="text" value="{{ $name }}">
            <div class="form-text" id="name-help">
                What do other transformers know you as?
            </div>
        </div>
    </div>

    <div class="row mt-1">
        <label class="col-1 col-form-label">Prime.Color</label>
        <div class="col">
            <input aria=describedby="color-help" class="form-control"
                id="color-prime" name="color_primary" required type="text"
                value="{{ $color_primary }}">
            <div class="form-text" id="color-help">
                Distinct colors help other players keep track of who you are.
            </div>
        </div>
    </div>

    <div class="row mt-1">
        <label class="col-1 col-form-label">Secd.Color</label>
        <div class="col">
            <input aria=describedby="color-help" class="form-control"
                id="color-secondary" name="color_secondary" required
                type="text" value="{{ $color_secondary }}">
        </div>
    </div>

    <div class="row mt-1">
        <label class="col-1 col-form-label">Quote</label>
        <div class="col">
            <textarea class="form-control" id="quote" name="quote" required
                >{{ $quote }}</textarea>
            <div class="form-text" id="quote-help">
                A Quote makes your character seem full and interesting. Don’t
                forget to put one in before play, and use it liberally!
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col">
            <h2>01. Allegiance</h2>
        </div>
    </div>

    <div class="row">
        <div class="col-1">
            <label class="col-form-label" for="allegiance">Allegiance</label>
        </div>
        <div class="col">
            <select aria-describedby="allegiance-help" class="form-control"
                id="allegiance" name="allegiance" required>
                <option>Choose allegiance</option>
                <option @if ('Autobots' === $allegiance) selected @endif
                    value="Autobots">Autobots</option>
                <option @if ('Decepticons' === $allegiance) selected @endif
                    value="Decepticons">Decepticons</option>
            </select>
            <div class="form-text" id="allegiance-help">
                Know who's team you're on!
            </div>
        </div>
    </div>

    <div class="row mt-1">
        <div class="col">
            <button class="btn btn-primary" type="submit">Save</button>
        </div>
    </div>
    </form>
</x-app>
