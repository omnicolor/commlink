<x-app>
    <x-slot name="title">Create character: Finishing touches</x-slot>
    @include('alien::create-navigation')

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
            <h1>Finishing Touches</h1>

            <form action="" method="POST">
                @csrf
                <div class="mb-3">
                    <label for="appearance" class="form-label">
                        <h3>Appearance</h3>
                    </label>
                    <div class="form-text">
                        Describe your player character’s appearance
                        in a few words. Your career gives you a few
                        suggestions, but you are free to choose any
                        appearance that you think fits your character.
                    </div>
                    <textarea class="form-control" id="appearance"
                        name="appearance" rows="3">{{ $appearance }}</textarea>
                </div>

                <div class="mb-3">
                    <label for="agenda" class="form-label">
                        <h3>Agenda</h3>
                    </label>
                    <div class="form-text">
                        In Campaign play, you can pick one of the
                        suggested Personal Agendas listed with your career,
                        or you can come up with an agenda of your own. At
                        the end of each game session, discuss the agendas of
                        all PCs together. If you have taken some concrete
                        action to further your agenda during the session,
                        despite risk or cost, you gain a bonus Experience
                        Point (see page 35).
                    </div>
                    <textarea class="form-control" id="agenda" name="agenda"
                        rows="3">{{ $agenda }}</textarea>
                </div>

                <h3>Buddies and Rivals</h3>
                <div class="form-text">
                    The ALIEN roleplaying game is about a small group of
                    people facing unknown and horrifying dangers in the cold
                    darkness of space. To survive, you need to find someone
                    to trust, but also be careful who you turn your back to.
                    In game terms, your PC can have one Buddy and one Rival
                    amongst the other PCs. You can only have one of each.
                    Your relationships are important for the GM, as she can
                    use them to create interesting situations in the game.
                    In Campaign play, you can choose one PC to be your Buddy
                    and another to be your Rival.
                </div>

                <div class="mb-3">
                    <label for="buddy" class="form-label">Buddy</label>
                    <input class="form-control" id="buddy" name="buddy"
                        type="text" value="{{ $buddy }}">
                </div>

                <div class="mb-3">
                    <label for="rival" class="form-label">Rival</label>
                    <input class="form-control" id="rival" name="rival"
                        type="text" value="{{ $rival }}">
                </div>

                <button class="btn btn-primary" id="submit" type="submit">
                    Next: Review {{ $character }}
                </button>
            </form>
        </div>
        <div class="col-1"></div>
    </div>
</x-app>
