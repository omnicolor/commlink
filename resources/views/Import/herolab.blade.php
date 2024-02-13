<x-app>
    <x-slot name="title">Hero Lab Import</x-slot>
    <x-slot name="navbar">
        <li class="nav-item">
            <a class="nav-link" href="{{ route('dashboard') }}">Home</a>
        </li>
        <li class="nav-item">
            <span class="active nav-link">Hero Lab</span>
        </li>
    </x-slot>

    <div class="row mt-4">
        <div class="col-1"></div>
        <div class="col">
            <h1>Import - Hero Lab</h1>

            @if ($errors->any())
            <div class="alert alert-danger alert-dismissible fade mt-4 show" role="alert">
                There
                @if (1 === count($errors->all()))
                    was a problem
                @else
                    were problems
                @endif
                with your request:
                <ul>
                    @foreach ($errors->all() as $message)
                        <li>{!! $message !!}</li>
                    @endforeach
                </ul>
            </div>
            @endif

            <form action="{{ route('import.herolab.upload') }}"
                enctype="multipart/form-data" method="POST">
                @csrf
                <div class="mb-3 row">
                    <div class="col">
                        <label for="character" class="form-label">
                            Hero Lab portfolio
                        </label>
                        <input accept=".por,application/zip"
                            class="form-control" id="character"
                            name="character" required type="file">
                    </div>
                </div>

                <div class="row">
                    <div class="col">
                        <input class="btn btn-primary" type="submit">
                    </div>
                </div>
            </form>
        </div>
        <div class="col-1"></div>
        <aside class="col-3">
            <h2>About Hero Lab</h2>

            <p>
                Hero Lab Classic makes character creation a breeze,
                automatically tracking modifiers for every stat, ability, item,
                spell, and option you select. Our automated validation engine
                verifies that all prerequisites, minimums, and other
                requirements have been met, pointing out where your character
                conflicts with the rules.
            </p>
        </aside>
        <div class="col-1"></div>
    </div>
</x-app>
