<x-app>
    <x-slot name="title">Create character: Details</x-slot>
    @include('stillfleet::create-navigation')

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

    <div class="my-4 row">
        <div class="col-1"></div>
        <div class="col">
            <h1>Critical details</h1>

            <p>
                Before getting too far into character creation, you should
                think about the character a bit to help you flesh them out.
            </p>

            <form action="{{ route('stillfleet.create-details') }}" method="POST">
                @csrf

                <ol>
                    <li>
                        <label class="form-label" for="name">
                            What is your name?</label>
                        <input class="form-control" id="name" name="name"
                            required type="text" value="{{ $name }}">
                    </li>
                    <li>
                        <label class="form-label" for="appearance">
                            What do you look like? What kind of clothes do you
                            wear?
                        </label>
                        <textarea class="form-control" id="appearance"
                            name="appearance">{{ $appearance }}</textarea>
                    </li>
                    <li>
                        <label class="form-label" for="origin">
                            Did you come from Terra or one of the provinces, or
                            were you born on Spindle?
                        </label>
                        <textarea class="form-control" id="origin"
                            name="origin">{{ $origin }}</textarea>
                    </li>
                    <li>
                        <label class="form-label" for="family">
                            Do you have a family? Close friends? Pets?
                        </label>
                        <textarea class="form-control" id="family"
                            name="family">{{ $family }}</textarea>
                    </li>
                    <li>
                        <label class="form-label" for="team">
                            Do you know the other voidminers, or are you
                            meeting for the first time now?
                        </label>
                        <textarea class="form-control" id="team"
                            name="team">{{ $team }}</textarea>
                    </li>
                    <li>
                        <label class="form-label" for="others">
                            What do you think about the other voidminers? Is
                            one of them a lover? A rival?
                            <a href="#" id="choose">Choose for me</a>
                        </label>
                        <textarea class="form-control" id="others"
                            name="others">{{ $others }}</textarea>
                    </li>
                    <li>
                        <label class="form-label" for="crew_nickname">
                            What is the nickname of your crew? Always name your
                            crew!
                        </label>
                        <textarea class="form-control" id="crew_nickname"
                            name="crew_nickname">{{ $crew_nickname }}</textarea>
                    </li>
                    <li>
                        <label class="form-label" for="refactor">
                            What do you think about your refactor and/or other
                            superior(s)?
                        </label>
                        <textarea class="form-control" id="refactor"
                            name="refactor">{{ $refactor }}</textarea>
                    </li>
                    <li>
                        <label class="form-label" for="company">
                            What do you think about the Co. generally? Is it a
                             force of good or evil?
                        </label>
                        <textarea class="form-control" id="company"
                            name="company">{{ $company }}</textarea>
                    </li>
                    <li>
                        <label class="form-label" for="motivation">
                            Most importantly—what do you want? What motivates
                            you to travel through the void, to new worlds?
                        </label>
                        <textarea class="form-control" id="motivation"
                            name="motivation">{{ $motivation }}</textarea>
                    </li>
                </ol>

                <div class="mt-4">
                    <button class="btn btn-primary" type="submit">
                        Set details
                    </button>
                </div>
            </form>
        </div>
        <div class="col-1"></div>
    </div>

    <x-slot name="javascript">
        <script>
            const team = [
                'All dying of the same mysterious plague. Must work together to live!',
                'Siblings, on good terms.',
                'Siblings, on poor terms.',
                'Extended family.',
                'Extended family, currently not speaking.',
                'Helped save each other from a memorable disaster.',
                'Met in court when PC 1 sued PC 2 for theft. (The case was dismissed.)',
                'Lovers (one or more couples).',
                'Ex-lovers.',
                'Work together.',
                'Engage in criminal activities together.',
                'PCs are addicts, hooked on the same substance.',
                'One PC is a pusher; the others are ex-addicts.',
                'One or more PCs owe another PC lots of money.',
                'Old buddies, haven’t seen each other for a spell.',
                'One PC has a crush on another PC and has enlisted the others’ help.',
                'PCs have fought each other, metaphorically and literally, and come to a truce.',
                'Worked together as kids in the bar or kitchen of a café.',
                'Political rivals.',
                'Political potentate and lackey(s).',
                'All seek vengeance for the same misdeed.',
                'All worship the same eldritch deity.',
                'All seek the same fortune—the legendary [item of renown].',
                'Roomies, star-crossed.',
                'Have co-adopted a pet (dangerous) alien.',
                'Working together to solve some essential riddle of timespace/do science stuff.',
                'Currently students together, under the tutelage of a dangerous alien scholar.',
                'Currently students together, under the tutelage of an Archivist.',
                'The PCs were individually chosen as champions by the Directorate.',
                'The PCs were created by a cthulhicate entity to serve some unscrupulous and inscrutable purpose.',
            ];

            $('#choose').on('click', function (e) {
                e.preventDefault();
                const choice = team[Math.floor(Math.random() * team.length)];
                $('#others').val(choice);
            });
        </script>
    </x-slot>
</x-app>
