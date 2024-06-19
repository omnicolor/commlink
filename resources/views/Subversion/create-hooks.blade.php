<x-app>
    <x-slot name="title">Create character: Dramatic hooks</x-slot>
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
            <h1>Dramatic hooks</h1>
            <p>
                Each PC picks two <strong>Dramatic Hooks</strong> for their
                characters – motivations and situations particular to them that
                drive their story. These hooks can take the form of missions
                they are on, recurring problems they are seeking to overcome, or
                even a tragic flaws that drive the story arcs of their
                character. Hooks serve as a signal to the GM, letting them know
                what sort of stories you’re most interested in playing when
                events focus on your PC—be creative, and choose whatever you
                think will make the story you’re most excited about!
            </p>

            <p>
                Whenever your dramatic hooks becomes relevant in a game, both
                you and the GM gain 3 Grit.
            </p>

            <p>
                Dramatic hooks can come in a wide range of forms. Below are some
                examples:
            </p>

            <ul>
                <li>I want to find out what happened to my missing sister</li>
                <li>I want to become a master Yojin</li>
                <li>
                    I need to find a way to get an expensive medical treatment
                    for my son
                </li>
                <li>
                    My granddaughter has gotten in trouble with her involvement
                    in the Bravia crime family and I need to bail her out
                </li>
                <li>
                    I keep making horrible romantic decisions and leaving a
                    trail of exes that are causing me problems
                </li>
                <li>I want to find the lost city that my mother never could</li>
                <li>
                    I want to prove that Dynacorp was responsible for the dam
                    breaking and make them pay
                </li>
                <li>
                    My character wants to get paid (but I want them to realize
                    they actually care about stuff)
                </li>
                <li>
                    My character is determined to take down the system or die
                    trying (and if they do go, I want them to do so
                    dramatically)
                </li>
            </ul>
        </div>
        <div class="col-4"></div>
    </div>

    <form action="" id="form" method="POST">
    @csrf

    <div class="my-1 row">
        <div class="col-1"></div>
        <div class="col-1">
            <label class="form-label" for="hook1">Hook 1</label>
        </div>
        <div class="col">
            <input class="form-control" id="hook1" name="hook1" required
                type="text"
                value="{{ old('hook1') ?? $character->hooks[0] ?? '' }}">
            <div class="invalid-feedback">Hooks are required.</div>
        </div>
        <div class="col-4"></div>
    </div>

    <div class="my-1 row">
        <div class="col-1"></div>
        <div class="col-1">
            <label class="form-label" for="hook2">Hook 2</label>
        </div>
        <div class="col">
            <input class="form-control" id="hook2" name="hook2" required
                type="text"
                value="{{ old('hook2') ?? $character->hooks[1] ?? '' }}">
            <div class="invalid-feedback">Hooks are required.</div>
        </div>
        <div class="col-4"></div>
    </div>

    <div class="my-1 row">
        <div class="col-1"></div>
        <div class="col text-end">
            <button class="btn btn-primary" type="submit">
                Next: Relations
            </button>
        </div>
        <div class="col-4"></div>
    </div>
    </form>

    @include('Subversion.create-fortune')
</x-app>
