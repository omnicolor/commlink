<x-app>
    <x-slot name="title">Create character</x-slot>
    <x-slot name="head">
        <link href="/css/datatables.min.css" rel="stylesheet">
        <link href="/css/Shadowrun5e/character-generation.css" rel="stylesheet">
        <style>
            ol li {
                margin-top: 1em;
            }
        </style>
    </x-slot>
    @include('shadowrun5e::create-navigation')

    @if ($errors->any())
        <div class="alert alert-danger mt-4">
            <ul>
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <form action="{{ route('shadowrun5e.create-background') }}" method="POST">
    @csrf

    <div class="row mt-3">
        <div class="col-1"></div>
        <div class="col">
            <h1>Background</h1>
            <p>
                These questions are to help you understand your character
                better, as well as for your GM to understand it. They help your
                GM tailor the game to the characters. The more thoughtful and
                complete you make the answers, the better. At a minimum, they'll
                help you roleplay your character better. In addition, if you
                have a rich history for your character, your GM is more likely
                to give your character more spotlight to shine in.
            </p>

            <p>
                All questions are completely optional, but it recommended that
                you at least give them some thought for your character!
            </p>
            <ol>
                <li class="form-group" id="gender-row">
                    <label for="gender">
                        What is the character's gender?
                    </label>
                    <textarea aria-describedby="gender-help"
                        class="form-control" id="gender" name="gender-identity"
                        rows="3">{{ $background['gender-identity'] }}</textarea>
                    <small class="form-text text-muted" id="vitals-gender">
                    </small>
                    <small id="gender-help" class="form-text text-muted">
                        Standard male or female? Transgender? Indeterminate? How
                        do they present to the world? A lot of the issues around
                        gender have been dealt with by the 2070s, but
                        individuals still have opinions and prejudices—and their
                        own ways of expressing themselves.
                    </small>
                </li>
                <li class="form-group" id="size-row">
                    <label for="size">
                        What is the character's physical size?
                    </label>
                    <textarea aria-describedby="size-help" class="form-control"
                        id="size" name="size"
                        rows="3">{{ $background['size'] }}</textarea>
                    <small class="form-text text-muted" id="vitals-size">
                    </small>
                    <small id="size-help" class="form-text text-muted">
                        Is he pretty standard for his gender and metatype, or is
                        he a tall, skinny dwarf or short for a troll?
                    </small>
                </li>
                <li class="form-group" id="description-row">
                    <label for="description">
                        What is the color of the character's hair, eyes, and
                        skin?
                    </label>
                    <textarea aria-describedby="description-help"
                        class="form-control" id="description" name="description"
                        rows="3">{{ $background['description'] }}</textarea>
                    <small class="form-text text-muted" id="vitals-description">
                    </small>
                    <small id="description-help" class="form-text text-muted">
                        Is her coloring particularly striking, or so average
                        that she blends easily into crowds? Remember, with
                        cyberware, surgery, and cosmetics, your character can
                        have just about any coloring you want her to have.
                        Remember too that most racism in the Shadowrun world is
                        centered around metatype rather than ethnicity.
                    </small>
                </li>
                <li class="form-group" id="appearance-row">
                    <label for="appearance">
                        What is the character's general appearance?
                    </label>
                    <textarea aria-describedby="appearance-help"
                        class="form-control" id="appearance" name="appearance"
                        rows="3">{{ $background['appearance'] }}</textarea>
                    <small id="appearance-help" class="form-text text-muted">
                        First impressions matter. Is your character a slob or
                        neatly dressed? Does she slouch? Does she like to make a
                        splash when she enters a room? Is she drop-dead
                        gorgeous, butt-ugly, or somewhere in between?
                    </small>
                </li>
                <li class="form-group" id="born-row">
                    <label for="born">
                        Where was the character born?
                    </label>
                    <textarea aria-describedby="born-help" class="form-control"
                        id="born" name="born"
                        rows="3">{{ $background['born'] }}</textarea>
                    <small class="form-text text-muted" id="vitals-birthplace">
                    </small>
                    <small id="born-help" class="form-text text-muted">
                        Was he raised a rich corp brat, or did he grow up as an
                        orphan on the streets fighting for every meal? Was his
                        childhood spent in a megasprawl or in a more natural
                        setting like the NAN lands or Tir Tairngire?
                    </small>
                </li>
                <li class="form-group" id="age-row">
                    <label for="age">
                        What is the character's age?
                    </label>
                    <textarea aria-describedby="age-help" class="form-control"
                        id="age" name="age"
                        rows="3">{{ $background['age'] }}</textarea>
                    <small class="form-text text-muted" id="vitals-age">
                    </small>
                    <small id="age-help" class="form-text text-muted">
                        A very young character will have a different perspective
                        on the world than an older one; likewise, an ork with a
                        short lifespan will see things differently than an elf
                        with a very long one. What important Sixth World events
                        does your character remember? Was she involved in any of
                        them?
                    </small>
                </li>
                <li class="form-group" id="family-row">
                    <label for="family">
                        What was the character's family like?
                    </label>
                    <textarea aria-describedby="family-help"
                        class="form-control" id="family" name="family"
                        rows="3">{{ $background['family'] }}</textarea>
                    <small id="family-help" class="form-text text-muted">
                        A character's childhood shapes who he is today. Did he
                        have siblings? If so, does he keep in touch with them?
                        Did he know his parents? Did he grow up in a large,
                        close-knit group, or was he an orphan with no one he
                        could trust to look out for him? Does he have any dark
                        family secrets?
                    </small>
                </li>
                <li id="married-row">
                    <label for="married">
                        Has the character begun her own family?
                    </label>
                    <textarea aria-describedby="married-life-help"
                        class="form-control" id="married" name="married"
                        rows="3">{{ $background['married'] }}</textarea>
                    <small id="married-life-help" class="form-text text-muted">
                        Is she married or partnered? Separated? Widowed? Does
                        she have children? If your character is male, does he
                        have children he doesn't know about? (Even if you don't
                        think so, your gamemaster might think otherwise!)
                    </small>
                </li>
                <li class="form-group" id="education-row">
                    <label for="education">
                        Where or how was the character educated?
                    </label>
                    <textarea aria-describedby="education-help"
                        class="form-control" id="education" name="education"
                        rows="3">{{ $background['education'] }}</textarea>
                    <small id="education-help" class="form-text text-muted">
                        Did she get her education from the School of Hard
                        Knocks? Does she have an advanced degree from a
                        respected university? Was she raised in the corporate
                        educational system, or did she learn her skills from a
                        mentor?
                    </small>
                </li>
                <li class="form-group" id="living-row">
                    <label for="living">
                        Has the character done anything else for a living?
                    </label>
                    <textarea aria-describedby="living-help"
                        class="form-control" id="living" name="living"
                        rows="3">{{ $background['living'] }}</textarea>
                    <small id="living-help" class="form-text text-muted">
                        What did he do before he ran the shadows? Was he a
                        professional, a student, a ganger, a corporate cog, or
                        something more exotic? Why did he give it up to become a
                        shadowrunner?
                    </small>
                </li>
                <li class="form-group" id="religion-row">
                    <label for="religion">
                        What are the character's political and religious
                        beliefs?
                    </label>
                    <textarea aria-describedby="religion-help"
                        class="form-control" id="religion" name="religion"
                        rows="3">{{ $background['religion'] }}</textarea>
                    <small id="religion-help" class="form-text text-muted">
                        The two big things you never want to discuss at a
                        friendly gathering are politics and religion. Does your
                        character have strong political beliefs, or any at all?
                        Is she religious? Atheist? Anti-religion? How important
                        are these beliefs to defining the character?
                    </small>
                </li>
                <li class="form-group" id="moral-row">
                    <label for="moral">
                        What is the character's moral code?
                    </label>
                    <textarea aria-describedby="moral-help" class="form-control"
                        id="moral" name="moral"
                        rows="3">{{ $background['moral'] }}</textarea>
                    <small id="moral-help" class="form-text text-muted">
                        Does the character refuse to kill? Does he have any kind
                        of sexual ethics? Does his morality have a large bearing
                        on his actions, or is he an amoral hedonist whose
                        actions change depending on the situation? What (if
                        anything) might compel him to break one of his moral
                        strictures?
                    </small>
                </li>
                <li class="form-group" id="goals-row">
                    <label for="goals">
                        Does the character have any goals?
                    </label>
                    <textarea aria-describedby="goals-help" class="form-control"
                        id="goals" name="goals"
                        rows="3">{{ $background['goals'] }}</textarea>
                    <small id="goals-help" class="form-text text-muted">
                        Everybody wants something. Does your character want
                        money? Fame? That big score that will allow her to
                        retire to anonymity? Security for her friends and
                        family? Revenge? What kind of effort is she willing to
                        make to achieve these goals?
                    </small>
                </li>
                <li class="form-group" id="why-row">
                    <label for="why">
                        Why does the character run the shadows?
                    </label>
                    <textarea aria-describedby="why-help" class="form-control"
                        id="why" name="why"
                        rows="3">{{ $background['why'] }}</textarea>
                    <small id="why-help" class="form-text text-muted">
                        Is he doing it because he wants to, or was he forced
                        into it? Does he do it for the thrill, the money, or
                        because he hates the powers that be and wants to do his
                        small part to bring them down? What would make him stop
                        running the shadows?
                    </small>
                </li>
                <li class="form-group" id="personality-row">
                    <label for="personality">
                        What is the character's personality?
                    </label>
                    <textarea aria-describedby="personality-help"
                        class="form-control" id="personality" name="personality"
                        rows="3">{{ $background['personality'] }}</textarea>
                    <small id="personality-help" class="form-text text-muted">
                        Is she an introvert or an extrovert? Is she funny,
                        grumpy, flirtatious, or just plain weird? Does she have
                        social skills, or is she uncomfortable relating to
                        others? Is she opinionated, easygoing, or downright
                        apathetic? How do others tend to see her?
                    </small>
                </li>
                <li class="form-group" id="qualities-row">
                    <label for="qualities">
                        What special qualities does the character possess?
                    </label>
                    <textarea aria-describedby="qualities-help"
                        class="form-control" id="qualities" name="qualities"
                        rows="3">{{ $background['qualities'] }}</textarea>
                    <small id="qualities-help" class="form-text text-muted">
                        Not every quality is directly related to shadowrunning.
                        Can he draw well? Does he have perfect pitch? Is he a
                        really good organizer? Does he have a knack with
                        animals?
                    </small>
                </li>
                <li class="form-group" id="limitations-row">
                    <label for="limitations">
                        Are there certain things the character just cannot do?
                    </label>
                    <textarea aria-describedby="limitations-help"
                        class="form-control" id="limitations" name="limitations"
                        rows="3">{{ $background['limitations'] }}</textarea>
                    <small id="limitations-help" class="form-text text-muted">
                        What are her limitations? Is she terrible with money? Is
                        she incapable of harming children? Does she have a
                        crippling fear of heights, or find it nearly impossible
                        to form close relationships?
                    </small>
                </li>
                <li class="form-group" id="hate-row">
                    <label for="hate">What does the character hate?</label>
                    <textarea aria-describedby="hate-help" class="form-control"
                        id="hate" name="hate"
                        rows="3">{{ $background['hate'] }}</textarea>
                    <small id="hate-help" class="form-text text-muted">
                        Elves? Religious people? Corporations? Personality
                        surveys? Himself?
                    </small>
                </li>
                <li class="form-group" id="love-row">
                    <label for="love">What does the character love?</label>
                    <textarea aria-describedby="love-help" class="form-control"
                        id="love" name="love"
                        rows="3">{{ $background['love'] }}</textarea>
                    <small id="love-help" class="form-text text-muted">
                        This could be a person (like a lover or family member);
                        an ideal (justice, freedom, metahuman rights); an item
                        (her favorite gun); a location; or even herself.
                    </small>
                </li>
                <li class="form-group" id="name-row">
                    <label for="name">What is the character's name?</label>
                    <textarea aria-describedby="name-help" class="form-control"
                        id="name" name="name"
                        rows="3">{{ $background['name'] }}</textarea>
                    <small id="name-help" class="form-text text-muted">
                        Names have power in the Sixth World. What was his birth
                        name? Does he like/use it, or does he prefer to go by a
                        street name? If he has a nickname or street name, did he
                        pick it or was it bestowed on him by associates?
                    </small>
                </li>
                <li class="form-group" id="motivation-row">
                    <label for="motivation">
                        What is your motivation for playing?
                    </label>
                    <textarea aria-describedby="motivation-help"
                        class="form-control" id="motivation" name="motivation"
                        rows="3">{{ $background['motivation'] }}</textarea>
                    <small id="motivation-help" class="form-text text-muted">
                        Why are <strong>you</strong> playing this character? Do
                        you want to hang out with some friends and throw some
                        dice to kill some bad guys? Do you want to experience
                        the Sixth World through your character's eyes?
                    </small>
                </li>
            </ol>
        </div>
        <div class="col-3"></div>
    </div>

    @include('shadowrun5e::create-next')
    </form>

    @include('shadowrun5e::create-points')

    <x-slot name="javascript">
        <script src="/js/Shadowrun5e/Points.js"></script>
        <script>
            let character = @json($character);
            let points = new Points(character);
            $(function () {
                updatePointsToSpendDisplay(points);
            });
        </script>
        <script src="/js/Shadowrun5e/create-common.js"></script>
    </x-slot>
</x-app>
