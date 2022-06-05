<x-app>
    <x-slot name="title">Chummer 5 Import</x-slot>
    <x-slot name="navbar">
        <li class="nav-item">
            <a class="nav-link" href="{{ route('dashboard') }}">Home</a>
        </li>
        <li class="nav-item">
            <span class="active nav-link">Chummer 5</span>
        </li>
    </x-slot>

    <div class="row mt-4">
        <div class="col-1"></div>
        <div class="col">
            <h1>Import - Chummer 5</h1>

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

            <form action="{{ route('import.chummer5.upload') }}"
                enctype="multipart/form-data" method="POST">
                @csrf
                <div class="mb-3 row">
                    <div class="col">
                        <label for="character" class="form-label">
                            Chummer 5 character file
                        </label>
                        <input accept=".chum5,application/xml,text/xml"
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
            <h2>About Chummer 5</h2>

            <p>
                Chummer is a character creation and management application for
                the tabletop RPG Shadowrun, Fifth Edition running on Windows.
                Not only can you create your character quickly and easily, but
                you can also use Chummer during your character's shadowrunning
                career, to accurately track your Karma, Nuyen, ammo, and
                everything else all in one place. Chummer also includes support
                for a number of optional rules and house rules and even includes
                support for critters and is useful for players and Game Masters
                alike! It also supports a number of languages: supports multiple
                languages: English (US), French, German, Japanese, Portuguese
                (Brazil) and Chinese (Simplified) files are pre-installed, while
                additional languages can be added and shared through use of our
                in-house translator tool.
            </p>
        </aside>
        <div class="col-1"></div>
    </div>
</x-app>
