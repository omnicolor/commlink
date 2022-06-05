<x-app>
    <x-slot name="title">Choose lifepath</x-slot>
    @include('CyberpunkRed.create-navigation')

    <h1>Lifepath</h1>

    <p>
        Lifepath is a flowchart of "plot complications" designed to help you
        give your Cyberpunk Character an authentically Dark Future background.
        Its sections cover your cultural origins, your family, friends, enemies,
        personal habits, and even key life events. It's intended primarily as a
        guide; if you encounter something you don't think fits the Character
        you've envisioned, feel free to change the path as you see fit. And
        remember: Cyberpunk hinges on roleplaying, so make use of the
        information in your Lifepath run. It's a guaranteed adventure generator!
    </p>

    <p>
        Commlink has rolled the appropriate die for each of these to give you a
        random starting point. Assuming it's okay with your GM, you may override
        any or all of them to build the character you want to play.
    </p>

    @if ($errors->any())
        <div class="alert alert-danger">
            <ul>
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <form action="{{ route('cyberpunkred-create-lifepath') }}" method="POST">
    @csrf
    <div class="accordion" id="lifepath">
        <div class="accordion-item">
            <h2 class="accordion-header" id="cultural-origins-heading">
                <button aria-controls="origins" aria-expanded="true"
                    class="accordion-button" data-bs-target="#origins"
                    data-bs-toggle="collapse" type="button">
                    Cultural origins
                </button>
            </h2>
            <div aria-labelledby="cultural-origins-heading"
                class="accordion-collapse collapse show"
                data-bs-parent="#lifepath" id="origins">
                <div class="accordion-body">
                    <p>
                        The Cyberpunk world is multicultural and multinational.
                        You either learn to deal with all kinds of people from
                        all over a fractured and chaotic world, or you die the
                        first time you look side-eye at the wrong person. Where
                        you come from determines your native language.
                    </p>

                    <table class="table table-striped">
                        <thead>
                            <tr>
                                <th scope="col">Roll</th>
                                <th scope="col">Region</th>
                                <th scope="col">Languages</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach (\App\Models\CyberpunkRed\Lifepath::$origins as $key => $value)
                            <tr>
                                <td><div class="form-check">
                                    <input class="form-check-input"
                                        id="origin-{{ $key }}"
                                        name="origin"
                                        @if ($origin === $key) checked @endif
                                        type="radio"
                                        value="{{ $key }}">
                                    <label class="form-check-label"
                                        for="origin-{{ $key }}">
                                        {{ $key }}
                                    </label>
                                </div></td>
                                <td>{{ $value['name'] }}</td>
                                <td>{{ implode(', ', $value['languages']) }}</td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
        <div class="accordion-item">
            <h2 class="accordion-header" id="personality-heading">
                <button aria-controls="personality" aria-expanded="false"
                    class="accordion-button collapsed" data-bs-target="#personality"
                    data-bs-toggle="collapse" type="button">
                    Personality
                </button>
            </h2>
            <div aria-labelledby="personality-heading"
                class="accordion-collapse collapse" data-bs-parent="#lifepath"
                id="personality">
                <div class="accordion-body">
                    <p>
                        This is what you're like as a person. Are you the kind
                        of Character that stands away from the pack, aloof and
                        calculating? A party animal who loves to get messed up?
                        The stable and competent professional who always has a
                        plan?
                    </p>

                    <table class="table table-striped">
                        <thead>
                            <tr>
                                <th scope="col">Roll</th>
                                <th scope="col">Personality traits</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach (\App\Models\CyberpunkRed\Lifepath::$personalities as $key => $value)
                            <tr>
                                <td><div class="form-check">
                                    <input class="form-check-input"
                                        id="personality-{{ $key }}"
                                        name="personality"
                                        @if ($personality === $key) checked @endif
                                        type="radio"
                                        value="{{ $key }}">
                                    <label class="form-check-label"
                                        for="personality-{{ $key }}">
                                        {{ $key }}
                                    </label>
                                </div></td>
                                <td>{{ $value }}</td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
        <div class="accordion-item">
            <h2 class="accordion-header" id="clothing-heading">
                <button aria-controls="clothing" aria-expanded="false"
                    class="accordion-button collapsed" data-bs-target="#clothing"
                    data-bs-toggle="collapse" type="button">
                    Clothing style
                </button>
            </h2>
            <div aria-labelledby="clothing-heading"
                class="accordion-collapse collapse" data-bs-parent="#lifepath"
                id="clothing">
                <div class="accordion-body">
                    <p>
                        In Cyberpunk, what you look like is (to The Street) a
                        snapshot of who you are. Your clothes, hairstyles and
                        even personal touches can determine how people will
                        relate to you, for good or for bad. Remember: an Exec
                        wearing Street Casual, a rainbow mohawk, and ritual
                        scars is probably not going to get that promotion they
                        wanted.
                    </p>

                    <p>
                        Note that your clothing style is more about the style of
                        clothes you favor, not the individual items. You could
                        wear a tailored business suit jacket, a rawhide fringed
                        Nomad jacket, a high-tech light collared urban flash
                        jacket, or even a torn and ripped leather jacket with
                        gang colors all over the back. Each one is the same item
                        of clothing (jacket), but defined by the style of jacket
                        your Character favors.
                    </p>

                    <table class="table table-striped">
                        <thead>
                            <tr>
                                <th scope="col">Roll</th>
                                <th scope="col">Clothing style</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach (\App\Models\CyberpunkRed\Lifepath::$clothes as $key => $value)
                            <tr>
                                <td><div class="form-check">
                                    <input class="form-check-input"
                                        id="clothing-{{ $key }}"
                                        name="clothing"
                                        @if ($clothing === $key) checked @endif
                                        type="radio"
                                        value="{{ $key }}">
                                    <label class="form-check-label"
                                        for="clothing-{{ $key }}">
                                        {{ $key }}
                                    </label>
                                </div></td>
                                <td>{{ $value }}</td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
        <div class="accordion-item">
            <h2 class="accordion-header" id="hairstyle-heading">
                <button aria-controls="hairstyle" aria-expanded="false"
                    class="accordion-button collapsed" data-bs-target="#hairstyle"
                    data-bs-toggle="collapse" type="button">
                    Hairstyle
                </button>
            </h2>
            <div aria-labelledby="hairstyle-heading"
                class="accordion-collapse collapse" data-bs-parent="#lifepath"
                id="hairstyle">
                <div class="accordion-body">
                    <p>
                    </p>

                    <table class="table table-striped">
                        <thead>
                            <tr>
                                <th scope="col">Roll</th>
                                <th scope="col">Hairstyle</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach (\App\Models\CyberpunkRed\Lifepath::$hairStyles as $key => $value)
                            <tr>
                                <td><div class="form-check">
                                    <input class="form-check-input"
                                        id="hair-{{ $key }}" name="hair"
                                        @if ($hair === $key) checked @endif
                                        type="radio" value="{{ $key }}">
                                    <label class="form-check-label"
                                        for="hair-{{ $key }}">
                                        {{ $key }}
                                    </label>
                                </div></td>
                                <td>{{ $value }}</td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
        <div class="accordion-item">
            <h2 class="accordion-header" id="affectation-heading">
                <button aria-controls="affectation" aria-expanded="false"
                    class="accordion-button collapsed" data-bs-target="#affectation"
                    data-bs-toggle="collapse" type="button">
                    Affectation you are never without
                </button>
            </h2>
            <div aria-labelledby="affectation-heading"
                class="accordion-collapse collapse" data-bs-parent="#lifepath"
                id="affectation">
                <div class="accordion-body">
                    <table class="table table-striped">
                        <thead>
                            <tr>
                                <th scope="col">Roll</th>
                                <th scope="col">Affectation</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach (\App\Models\CyberpunkRed\Lifepath::$affectations as $key => $value)
                            <tr>
                                <td><div class="form-check">
                                    <input class="form-check-input"
                                        id="affectation-{{ $key }}" name="affectation"
                                        @if ($affectation === $key) checked @endif
                                        type="radio" value="{{ $key }}">
                                    <label class="form-check-label"
                                        for="affectation-{{ $key }}">
                                        {{ $key }}
                                    </label>
                                </div></td>
                                <td>{{ $value }}</td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
        <div class="accordion-item">
            <h2 class="accordion-header" id="value-heading">
                <button aria-controls="value" aria-expanded="false"
                    class="accordion-button collapsed" data-bs-target="#value"
                    data-bs-toggle="collapse" type="button">
                    What do you value most?
                </button>
            </h2>
            <div aria-labelledby="value-heading"
                class="accordion-collapse collapse" data-bs-parent="#lifepath"
                id="value">
                <div class="accordion-body">
                    <table class="table table-striped">
                        <thead>
                            <tr>
                                <th scope="col">Roll</th>
                                <th scope="col">Value</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach (\App\Models\CyberpunkRed\Lifepath::$values as $key => $value)
                            <tr>
                                <td><div class="form-check">
                                    <input class="form-check-input"
                                        id="value-{{ $key }}" name="value"
                                        @if ($value === $key) checked @endif
                                        type="radio" value="{{ $key }}">
                                    <label class="form-check-label"
                                        for="value-{{ $key }}">
                                        {{ $key }}
                                    </label>
                                </div></td>
                                <td>{{ $value }}</td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
        <div class="accordion-item">
            <h2 class="accordion-header" id="people-heading">
                <button aria-controls="people" aria-expanded="false"
                    class="accordion-button collapsed" data-bs-target="#people"
                    data-bs-toggle="collapse" type="button">
                    How do you feel about most people?
                </button>
            </h2>
            <div aria-labelledby="people-heading"
                class="accordion-collapse collapse" data-bs-parent="#lifepath"
                id="people">
                <div class="accordion-body">
                    <table class="table table-striped">
                        <thead>
                            <tr>
                                <th scope="col">Roll</th>
                                <th scope="col">Feeling</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach (\App\Models\CyberpunkRed\Lifepath::$feelings as $key => $value)
                            <tr>
                                <td><div class="form-check">
                                    <input class="form-check-input"
                                        id="feeling-{{ $key }}" name="feeling"
                                        @if ($feeling === $key) checked @endif
                                        type="radio" value="{{ $key }}">
                                    <label class="form-check-label"
                                        for="feeling-{{ $key }}">
                                        {{ $key }}
                                    </label>
                                </div></td>
                                <td>{{ $value }}</td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
        <div class="accordion-item">
            <h2 class="accordion-header" id="person-heading">
                <button aria-controls="person" aria-expanded="false"
                    class="accordion-button collapsed" data-bs-target="#person"
                    data-bs-toggle="collapse" type="button">
                    Most valued person in your life?
                </button>
            </h2>
            <div aria-labelledby="person-heading"
                class="accordion-collapse collapse" data-bs-parent="#lifepath"
                id="person">
                <div class="accordion-body">
                    <table class="table table-striped">
                        <thead>
                            <tr>
                                <th scope="col">Roll</th>
                                <th scope="col">Person</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach (\App\Models\CyberpunkRed\Lifepath::$persons as $key => $value)
                            <tr>
                                <td><div class="form-check">
                                    <input class="form-check-input"
                                        id="person-{{ $key }}" name="person"
                                        @if ($person === $key) checked @endif
                                        type="radio" value="{{ $key }}">
                                    <label class="form-check-label"
                                        for="person-{{ $key }}">
                                        {{ $key }}
                                    </label>
                                </div></td>
                                <td>{{ $value }}</td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
        <div class="accordion-item">
            <h2 class="accordion-header" id="possession-heading">
                <button aria-controls="possession" aria-expanded="false"
                    class="accordion-button collapsed" data-bs-target="#possession"
                    data-bs-toggle="collapse" type="button">
                    Most valued possession you own?
                </button>
            </h2>
            <div aria-labelledby="possession-heading"
                class="accordion-collapse collapse" data-bs-parent="#lifepath"
                id="possession">
                <div class="accordion-body">
                    <table class="table table-striped">
                        <thead>
                            <tr>
                                <th scope="col">Roll</th>
                                <th scope="col">Possession</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach (\App\Models\CyberpunkRed\Lifepath::$possessions as $key => $value)
                            <tr>
                                <td><div class="form-check">
                                    <input class="form-check-input"
                                        id="possession-{{ $key }}" name="possession"
                                        @if ($possession === $key) checked @endif
                                        type="radio" value="{{ $key }}">
                                    <label class="form-check-label"
                                        for="possession-{{ $key }}">
                                        {{ $key }}
                                    </label>
                                </div></td>
                                <td>{{ $value }}</td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
        <div class="accordion-item">
            <h2 class="accordion-header" id="background-heading">
                <button aria-controls="background" aria-expanded="false"
                    class="accordion-button collapsed" data-bs-target="#background"
                    data-bs-toggle="collapse" type="button">
                    Your original family background
                </button>
            </h2>
            <div aria-labelledby="background-heading"
                class="accordion-collapse collapse" data-bs-parent="#lifepath"
                id="background">
                <div class="accordion-body">
                    <p>
                        Who are you and where did you originally come from? Were
                        you born with a silver spoon in your mouth or were you
                        using it to stab your brother so you could steal that
                        extra bite of dead rat you both found?
                    </p>
                    <table class="table table-striped">
                        <thead>
                            <tr>
                                <th scope="col">Roll</th>
                                <th scope="col">Background</th>
                                <th scope="col">Description</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach (\App\Models\CyberpunkRed\Lifepath::$backgrounds as $key => $value)
                            <tr>
                                <td><div class="form-check">
                                    <input class="form-check-input"
                                        id="background-{{ $key }}" name="background"
                                        @if ($background === $key) checked @endif
                                        type="radio" value="{{ $key }}">
                                    <label class="form-check-label"
                                        for="background-{{ $key }}">
                                        {{ $key }}
                                    </label>
                                </div></td>
                                <td>{{ $value['name'] }}</td>
                                <td>{{ $value['description'] }}</td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
        <div class="accordion-item">
            <h2 class="accordion-header" id="environment-heading">
                <button aria-controls="environment" aria-expanded="false"
                    class="accordion-button collapsed" data-bs-target="#environment"
                    data-bs-toggle="collapse" type="button">
                    Childhood environment
                </button>
            </h2>
            <div aria-labelledby="environment-heading"
                class="accordion-collapse collapse" data-bs-parent="#lifepath"
                id="environment">
                <div class="accordion-body">
                    <p>
                        How did you grow up? What kind of places did you and
                        your sibs hang out in? Safe and calm? Crazy dangerous?
                        Massively oppressive? It's possible that something
                        happened in your background and your environment turns
                        out drastically different from your original family
                        background.
                    </p>

                    <table class="table table-striped">
                        <thead>
                            <tr>
                                <th scope="col">Roll</th>
                                <th scope="col">Environment</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach (\App\Models\CyberpunkRed\Lifepath::$environments as $key => $value)
                            <tr>
                                <td><div class="form-check">
                                    <input class="form-check-input"
                                        id="environment-{{ $key }}" name="environment"
                                        @if ($environment === $key) checked @endif
                                        type="radio" value="{{ $key }}">
                                    <label class="form-check-label"
                                        for="environment-{{ $key }}">
                                        {{ $key }}
                                    </label>
                                </div></td>
                                <td>{{ $value }}</td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
        <div class="accordion-item">
            <h2 class="accordion-header" id="family-crisis-heading">
                <button aria-controls="family-crisis" aria-expanded="false"
                    class="accordion-button collapsed" data-bs-target="#family-crisis"
                    data-bs-toggle="collapse" type="button">
                    Your family crisis
                </button>
            </h2>
            <div aria-labelledby="family-crisis-heading"
                class="accordion-collapse collapse" data-bs-parent="#lifepath"
                id="family-crisis">
                <div class="accordion-body">
                    <p>
                        In the Time of the Red, the world is still recovering
                        from a world war and other disasters. Chances are,
                        something happened to you and your family along the way.
                        What's the story there?
                    </p>

                    <table class="table table-striped">
                        <thead>
                            <tr>
                                <th scope="col">Roll</th>
                                <th scope="col">Background</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach (\App\Models\CyberpunkRed\Lifepath::$familyCrisises as $key => $value)
                            <tr>
                                <td><div class="form-check">
                                    <input class="form-check-input"
                                        id="family-crisis-{{ $key }}" name="family-crisis"
                                        @if ($environment === $key) checked @endif
                                        type="radio" value="{{ $key }}">
                                    <label class="form-check-label"
                                        for="family-crisis-{{ $key }}">
                                        {{ $key }}
                                    </label>
                                </div></td>
                                <td>{{ $value }}</td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

    <div class="my-4 row">
        <div class="col"></div>
        <div class="col">
            <button class="btn btn-primary" type="submit">
                Choose lifepath
            </button>
        </div>
    </div>
    </form>
</x-app>
