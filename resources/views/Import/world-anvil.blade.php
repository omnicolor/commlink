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
        <div class="col-3">
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
        <aside class="col">
            <h2>About World Anvil</h2>

            <p>
                World Anvil is a set of worldbuilding tools that helps you
                create, organize and store your world setting. With wiki-like
                articles, interactive maps, historical timelines, an RPG
                Campaign Manager, and a full novel-writing software, we have all
                the tools you’ll need to run your RPG Campaign or write your
                novel!
            </p>

            <p>
                {{ config('app.name') }} currently supports importing characters
                from World Anvil for these systems:
            </p>

            <ul>
                <li>Cyberpunk Red</li>
                <li>The Expanse</li>
            </ul>

            <button aria-controls="help-downloading" aria-expanded="false"
                class="btn btn-outline-info" data-bs-target="#help-downloading"
                data-bs-toggle="collapse" type="button">
                How do I download a character?
            </button>

            <div class="collapse" id="help-downloading">
            <h2>Downloading a character from World Anvil</h2>

            <ol>
                <li class="mb-3">
                    <p>
                        First, click the &ldquo;switch between&rdquo; link at
                        the top of the screen. If you don't see something like
                        it, you may need to click the World Anvil logo to go to
                        the dashboard.
                    </p>
                    <img alt="Box highlighting where on the World Anvil page to click"
                        src="/images/WorldAnvil/character-import-step-1.png">
                </li>
                <li class="mb-3">
                    <p>
                        This will bring up a widget you can use to swtich
                        between worlds, campaigns, and characters. Click on
                        &ldquo;characters&rdquo;.
                    </p>
                    <img alt="Box highlighting where on the switcher to click"
                        src="/images/WorldAnvil/character-import-step-2.png">
                </li>
                <li class="mb-3">
                    <p>
                        Next, click
                        &ldquo;<i class="bi bi-eye-fill"></i>View&rdquo;
                        for the character you'd like to import.
                    </p>
                    <img alt="Boxes highlighting the View buttons to click"
                        src="/images/WorldAnvil/character-import-step-3.png">
                </li>
                <li class="mb-3">
                    <p>
                        You should now see your character's information, but not
                        the character sheet. Click &ldquo;Sheet&rdquo;.
                    </p>
                    <img alt="Box highlighting to click on the word 'sheet'"
                        src="/images/WorldAnvil/character-import-step-4.png">
                </li>
                <li class="mb-3">
                    <p>
                        Now you should see your character's sheet. Click the
                        eyeball icon in the sheet's top block. You'll need to
                        hover your mouse over the block for the icon to appear.
                    </p>
                    <img alt="Box highlighting an eyeball icon to click"
                        src="/images/WorldAnvil/character-import-step-5.png">

                    <p>
                        Depending on the system the character belongs to, the
                        icon may be difficult to see.
                    </p>
                    <img alt="Box highlighting an eyeball that blends into the background"
                        src="/images/WorldAnvil/character-import-step-5-alt.png">
                </li>
                <li class="mb-3">
                    <p>
                        Finally, you'll be able to click a button to show you
                        the computer-readable version of the character sheet.
                        Click the &ldquo;<i class="bi bi-box-arrow-up-right"></i>JSON&rqduo;
                        button and save the page that opens up.
                    </p>
                    <img alt="Box showing to click on the JSON button"
                        src="/images/WorldAnvil/character-import-step-6.png">
                </li>
                <li>
                    Upload that file to {{ config('app.name') }} and we'll take
                    care of the rest.
                </li>
            </ol>
            </div>
        </aside>
    </div>
</x-app>
