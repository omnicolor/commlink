@php
use App\Models\Avatar\Era;
@endphp
<div class="mt-3 row">
    <div class="col-2 col-form-label">
        Choose an era <small>(required)</small>
    </div>
    <div class="col">
        <div class="row">
            <div class="col">
                Before you do anything else, you need to decide on which era of
                history your game uses as a backdrop. You, another player, or
                the GM might already have a strong opinion on what era to play
                in—that’s great! Just make sure everyone at the table is just as
                excited to play in that era too. Avatar Legends: The Roleplaying
                Game offers five eras to choose from, each tied to the span of
                an Avatar’s life (except for the Hundred Year War Era) and each
                focuses on distinct themes that define the type of game you
                play.
            </div>
        </div>
        <div class="card-group mt-2">
            <div class="card">
                <div class="card-body">
                    <label class="card-text text-center">
                        <img alt="Picture of the Avatar Kyoshi: a stern-looking Asian woman with a fan-like headdress covering the top of her shoulder-length brown hair and long dangling earrings"
                            height="135" src="/images/Avatar/era-kyoshi.png"
                            width="84">
                        <br>
                        <input name="avatar-era" id="avatar-era-kyoshi"
                            @if (old('avatar-era') === Era::Kyoshi->value) checked @endif
                            type="radio" value="{{ Era::Kyoshi->value }}">
                        <span class="text-start d-block">
                            <strong>The Kyoshi Era</strong> covers the events
                            right after The Shadow of Kyoshi novel. Play in the
                            Kyoshi Era if you want to fight in battles against
                            rogues and bandits and deal with governmental
                            corruption as the nations establish their borders.
                        </span>
                    </label>
                </div>
            </div>
            <div class="card">
                <div class="card-body">
                    <label class="card-text text-center">
                        <img alt="Picture of the Avatar Roku: An angry-looking man with long brown hair with part pulled into a ponytail on the top of his head and a beard but no moustache"
                            height="135" src="/images/Avatar/era-roku.png"
                            width="84">
                        <br>
                        <input name="avatar-era" id="avatar-era-roku"
                            @if (old('avatar-era') === Era::Roku->value) checked @endif
                            type="radio" value="{{ Era::Roku->value }}">
                        <span class="text-start d-block">
                            <strong>The Roku Era</strong> covers the time right
                            after Sozin became Fire Lord and before Roku
                            married. Play in the Roku Era if you want to deal
                            with tensions between different nations and the
                            trials of maintaining an uneasy peace.
                        </span>
                    </label>
                </div>
            </div>
            <div class="card">
                <div class="card-body">
                    <label class="card-text text-center">
                        <img alt="Picture of a disapproving man with long brown hair, with part pulled into a pony tail on the top of his head bound by a gold metal clasp. His chin sports a long goatee."
                            height="135"
                            src="/images/Avatar/era-hundred-year-war.png"
                            width="84">
                        <br>
                        <input class="form-check-input"
                            @if (old('avatar-era') === Era::HundredYearWar->value) checked @endif
                            id="avatar-era-hundred-year-war" name="avatar-era"
                            type="radio"
                            value="{{ Era::HundredYearWar->value }}">
                        <span class="text-start d-block">
                            <strong>The Hundred Year War Era</strong> focuses on
                            the time just before Avatar Aang’s awakening at the
                            beginning of Avatar: The Last Airbender. Play in the
                            Hundred Year War Era if you want to rebel against
                            unjust rule, protect the weak, and stand up to
                            tyranny.
                        </span>
                    </label>
                </div>
            </div>
            <div class="card">
                <div class="card-body">
                    <label class="card-text text-center">
                        <img alt="A picture of Avatar Aang, an earnest-looking boy with a shaved head featuring a prominent blue arrow tattoo."
                            height="135" src="/images/Avatar/era-aang.png"
                            width="84">
                        <br>
                        <input class="form-check-input" id="avatar-era-aang"
                            @if (old('avatar-era') === Era::Aang->value) checked @endif
                            name="avatar-era" type="radio"
                            value="{{ Era::Aang->value }}">
                        <span class="text-start d-block">
                            <strong>The Aang Era</strong> is set after the
                            events of the Imbalance comics trilogy, some time
                            after the end of Avatar: The Last Airbender. Play in
                            the Aang Era if you want to heal the world after
                            tragedy and help push it into a brighter future.
                        </span>
                    </label>
                </div>
            </div>
            <div class="card">
                <div class="card-body">
                    <label class="card-text text-center">
                        <img alt="A picture of the Avatar Korra: A young woman with neck-length brown hair and a blue vest with white piping"
                            height="135" src="/images/Avatar/era-korra.png"
                            width="84">
                        <br>
                        <input class="form-check-input" id="avatar-era-korra"
                            @if (old('avatar-era') === Era::Korra->value) checked @endif
                            name="avatar-era" type="radio"
                            value="{{ Era::Korra->value }}">
                        <span class="text-start d-block">
                            <strong>The Korra Era</strong> covers a period that
                            takes place after the events of the Ruins of the
                            Empire comic trilogy, some time after the end of
                            The Legend of Korra. Play in the Korra Era if you
                            want to deal with the repercussions of imperialism
                            and play in a modernized era.
                        </span>
                    </label>
                </div>
            </div>
        </div>
        <div class="row">
            <div class="col form-text">See Core Rulebook, page 108.</div>
        </div>
    </div>
</div>

<div class="mt-3 row">
    <label class="col-2 col-form-label" for="avatar-scope">
        Choose a scope
    </label>
    <div class="col">
        <p>
            Now that you know when your game takes place, it’s time to decide
            where it takes place! The scope of your game measures how much of
            the Four Nations you expect to explore over the course of play. Are
            you interested in having every episode take place somewhere new or
            would you rather zoom in on just one location and explore its
            residents’ struggles?
        </p>
        <textarea class="form-control" id="avatar-scope" name="avatar-scope"
            placeholder="Describe your game's scope">{{ old('avatar-scope') }}</textarea>
        <div class="form-text">See Core Rulebook, page 109.</div>
    </div>
</div>

<div class="mt-3 row">
    <div class="col-2 col-form-label">
        Choose a group focus
    </div>
    <div class="col">
        <p>
            With your era and scope determined, you need to establish the reason
            your group sticks together with a group focus. Your group focus is
            the purpose which first united your companions to achieve a common
            goal. Your characters might disagree about how to achieve it, but
            they all believe the goal is so important that it’s worth risking
            danger and changing their futures. As a group, choose one of the
            following verbs to be your group focus, then determine the object of
            that verb’s action:
        </p>
        <div class="form-check avatar-foci">
            <div class="row row-cols-auto">
                <div class="col">
                    <input class="form-check-input" id="avatar-focus-defeat"
                        @if (old('avatar-focus') === 'defeat') checked @endif
                        name="avatar-focus" type="radio" value="defeat">
                    <label class="form-check-label" for="avatar-focus-defeat">
                        to <strong>defeat</strong>
                    </label>
                </div>
                <div class="col-10 gx-0">
                    <input class="
                        @if (old('avatar-focus') !== 'defeat')
                            form-control-plaintext
                        @else
                            form-control
                        @endif
                            col-auto pt-0"
                        id="avatar-focus-defeat-object"
                        name="avatar-focus-defeat-object"
                        placeholder="[dangerous foe]" tabindex="-1" type="text"
                        value="{{ old('avatar-focus-defeat-object') }}">
                </div>
            </div>
            <div class="row row-cols-auto">
                <div class="col">
                    <input class="form-check-input" id="avatar-focus-protect"
                        @if (old('avatar-focus') === 'protect') checked @endif
                        name="avatar-focus" type="radio" value="protect">
                    <label class="form-check-label" for="avatar-focus-protect">
                        to <strong>protect</strong>
                    </label>
                </div>
                <div class="col-10 gx-0">
                    <input class="
                        @if (old('avatar-focus') !== 'protect')
                            form-control-plaintext
                        @else
                            form-control
                        @endif
                           col-auto pt-0"
                        name="avatar-focus-protect-object"
                        id="avatar-focus-protect-object"
                        placeholder="[place, idea, culture, person, thing]"
                        tabindex="-1" type="text"
                        value="{{ old('avatar-focus-protect-object') }}">
                </div>
            </div>
            <div class="row row-cols-auto">
                <div class="col">
                    <input class="form-check-input" id="avatar-focus-change"
                        @if (old('avatar-focus') === 'change') checked @endif
                        name="avatar-focus" type="radio" value="change">
                    <label class="form-check-label" for="avatar-focus-change">
                        to <strong>change</strong>
                    </label>
                </div>
                <div class="col-10 gx-0">
                    <input class="
                        @if (old('avatar-focus') !== 'change')
                            form-control-plaintext
                        @else
                            form-control
                        @endif
                           col-auto pt-0"
                        id="avatar-focus-change-object"
                        name="avatar-focus-change-object"
                        placeholder="[culture, society, place, person]"
                        tabindex="-1" type="text"
                        value="{{ old('avatar-focus-change-object') }}">
                </div>
            </div>
            <div class="row row-cols-auto">
                <div class="col">
                    <input class="form-check-input" id="avatar-focus-deliver"
                        @if (old('avatar-focus') === 'deliver') checked @endif
                        name="avatar-focus" type="radio" value="deliver">
                    <label class="form-check-label" for="avatar-focus-deliver">
                        to <strong>deliver</strong>
                    </label>
                </div>
                <div class="col-10 gx-0">
                    <input class="
                        @if (old('avatar-focus') !== 'deliver')
                            form-control-plaintext
                        @else
                            form-control
                        @endif
                           col-auto pt-0"
                        id="avatar-focus-deliver-object"
                        name="avatar-focus-deliver-object"
                        placeholder="[person, thing] to [place, culture, person]"
                        tabindex="-1" type="text"
                        value="{{ old('avatar-focus-deliver-object') }}">
                </div>
            </div>
            <div class="row row-cols-auto">
                <div class="col">
                    <input class="form-check-input" id="avatar-focus-rescue"
                        @if (old('avatar-focus') === 'rescue') checked @endif
                        name="avatar-focus" type="radio" value="rescue">
                    <label class="form-check-label" for="avatar-focus-rescue">
                        to <strong>rescue</strong>
                    </label>
                </div>
                <div class="col-10 gx-0">
                    <input class="
                        @if (old('avatar-focus') !== 'rescue')
                            form-control-plaintext
                        @else
                            form-control
                        @endif
                           col-auto pt-0"
                        id="avatar-focus-rescue-object"
                        name="avatar-focus-rescue-object"
                        placeholder="[person, thing]"
                        tabindex="-1" type="text"
                        value="{{ old('avatar-focus-rescue-object') }}">
                </div>
            </div>
            <div class="row row-cols-auto">
                <div class="col">
                    <input class="form-check-input" id="avatar-focus-learn"
                        @if (old('avatar-focus') === 'learn') checked @endif
                        name="avatar-focus" type="radio" value="learn">
                    <label class="form-check-label" for="avatar-focus-learn">
                        to <strong>learn</strong>
                    </label>
                </div>
                <div class="col-10 gx-0">
                    <input class="
                        @if (old('avatar-focus') !== 'learn')
                            form-control-plaintext
                        @else
                            form-control
                        @endif
                           col-auto pt-0"
                        id="avatar-focus-learn-object"
                        name="avatar-focus-learn-object"
                        placeholder="[idea, culture, training, history]"
                        tabindex="-1" type="text"
                        value="{{ old('avatar-focus-learn-object') }}">
                </div>
            </div>
        </div>

        <p>
            Whatever you choose, the group focus is a problem too complicated to
            overcome in one episode (or game session). It’s the kind of thing
            that takes an entire season of a show or volume of a comic series to
            solve.
        </p>
        <textarea class="form-control" id="avatar-focus-details"
            name="avatar-focus-details"
            placeholder="Detail your group focus">{{ old('avatar-focus-details') }}</textarea>

        <div class="form-text">
            <strong>Note:</strong> You should consult your players to determine
            the group's focus. See Core Rulebook page 110.
        </div>
    </div>
</div>
