<x-app>
    <x-slot name="title">World Anvil Import</x-slot>
    <x-slot name="navbar">
        <li class="nav-item">
            <a class="nav-link" href="{{ route('dashboard') }}">Home</a>
        </li>
        <li class="nav-item">
            <span class="active nav-link">World Anvil</span>
        </li>
    </x-slot>

    <div class="row mt-4">
        <div class="col-1"></div>
        <div class="col">
            <h1>Import - World Anvil</h1>

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

            <form action="{{ route('import.world-anvil.upload') }}"
                enctype="multipart/form-data" method="POST">
                @csrf
                <div class="mb-3 row">
                    <div class="col">
                        <label for="character" class="form-label">
                            World Anvil character file
                        </label>
                        <input accept=".json,application/json"
                            class="form-control" id="character"
                            name="character" required type="file">
                    </div>
                </div>

                <div class="row">
                    <div class="col">
                        <input class="btn btn-primary" type="submit" value="Import">
                    </div>
                </div>
            </form>
        </div>
        <div class="col-1"></div>
        <aside class="col-3">
            <h2>About World Anvil</h2>

            <p>
                World Anvil is a set of worldbuilding tools that helps you
                create, organize and store your world setting. With wiki-like
                articles, interactive maps, historical timelines, an RPG
                Campaign Manager and a full novel-writing software, we have all
                the tools you’ll need to run your RPG Campaign or write your
                novel!
            </p>
        </aside>
        <div class="col-1"></div>
    </div>
</x-app>
