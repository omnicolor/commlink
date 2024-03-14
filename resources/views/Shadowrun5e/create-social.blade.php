<x-app>
    <x-slot name="title">Create character</x-slot>
    <x-slot name="head">
        <link href="/css/Shadowrun5e/character-generation.css" rel="stylesheet">
    </x-slot>
    @include('Shadowrun5e.create-navigation')

    @if ($errors->any())
        <div class="alert alert-danger mt-4">
            <ul>
                @foreach ($errors->all() as $error)
                <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <datalist id="license-values">
        <option value="Concealed Carry">
        <option value="Doctor">
        <option value="Drivers">
        <option value="Private Detective">
    </datalist>

    <datalist id="archetypes">
        @foreach ($archetypes as $archetype)
        <option value="{{ $archetype }}">
        @endforeach
    </datalist>

    <form action="{{ route('shadowrun5e.create-social') }}" method="POST">
    @csrf

    <div class="row mt-3">
        <div class="col-1"></div>
        <div class="col">
            <h1>Social</h1>
        </div>
    </div>

    <div class="row mt-4">
        <div class="col-1"></div>
        <div class="col">
            <h2>Contacts</h2>

            <p>
                Your contacts are the people that you know (other than your
                runner team) that you can contact for goods, information, and
                services. You may spend up to your charisma &times; 3
                ({{ $character->charisma * 3 }}) in connection and loyalty
                across your contacts. A connection can have a maximum of 12
                connection and 6 loyalty.
            </p>

            @if ($friendsInHighPlaces)
            <p>
                Your character has the &ldquo;Friends in High Places&rdquo;
                quality, so they have an additional charisma &times; 4
                ({{ $character->charisma * 4 }}) karma to spend. Contacts from
                this pool of karma have a <strong>minimum</strong> connection
                rating of 8.
            </p>
            @endif

            <ul class="list-group" id="contacts-list">
                @foreach ($character->getContacts() as $key => $contact)
                <li class="list-group-item" data-id="{{ $key }}">
                    {{ $contact }} (C: {{ $contact->connection }}
                    L: {{ $contact->loyalty }}
                    {{ $contact->archetype }})
                    <div class="float-end">
                        <button class="btn btn-danger btn-sm" type="button">
                            <span aria-hidden="true" class="bi bi-dash"></span>
                            Remove
                        </button>
                    </div>
                    <input name="contact-names[]" type="hidden"
                        value="{{ $contact }}">
                    <input name="contact-archetypes[]" type="hidden"
                        value="{{ $contact->archetype }}">
                    <input name="contact-connections[]" type="hidden"
                        value="{{ $contact->connection }}">
                    <input name="contact-loyalties[]" type="hidden"
                        value="{{ $contact->loyalty }}">
                    <input name="contact-notes[]" type="hidden"
                        value="{{ $contact->notes }}">
                </li>
                @endforeach
                <li class="list-group-item" id="no-contacts"
                    @if (0 !== count($character->getContacts()))
                        style="display:none"
                    @endif
                    >No contacts</li>
                <li class="list-group-item">
                    <button class="btn btn-success"
                        data-bs-target="#contacts-modal" data-bs-toggle="modal"
                        type="button">Add contact</button>
                </li>
            </ul>
        </div>
        <div class="col-3"></div>
    </div>

    <div class="row mt-4">
        <div class="col-1"></div>
        <div class="col">
            <h2>Identities</h2>

            <p>
                Shadowrunners frequently have multiple identities, using fake
                SINs to represent the appropriate registered identifiers, names,
                shopping histories, advertising preferences, food allergies,
                credit history, and everything else that corporations and
                governments want to track about their citizens. Not having and
                broadcasting a SIN is illegal. Most Shadowrunners do not have a
                legal SIN, so you'll need to acquire one or more fakes in order
                to operate in society.
            </p>

            <ul class="list-group" id="identities-list">
                @foreach ($character->getIdentities() as $key => $identity)
                <li class="list-group-item" data-id="{{ $key }}">
                    <strong>{{ $identity }}</strong>
                    <div class="float-end">
                        @if (isset($identity->sin) || isset($identity->sinner))
                            <div class="btn-group">
                                <button aria-haspopup="true"
                                    aria-expanded="false"
                                    class="btn btn-success btn-sm dropdown-toggle"
                                    data-bs-toggle="dropdown"
                                    id="identity-dropdown-{{ $key }}"
                                    type="button">
                                    <span aria-hidden="true" class="bi bi-plus"></span>
                                    Add to SIN
                                </button>
                                <div class="dropdown-menu"
                                    aria-labelledby="identity-dropdown-{{ $key }}">
                                    <button class="dropdown-item"
                                        data-bs-target="#licenses-modal"
                                        data-bs-toggle="modal"
                                        type="button">Add license</button>
                                    <button class="dropdown-item"
                                        data-bs-target="#lifestyles-modal"
                                        data-bs-toggle="modal"
                                        type="button">
                                        Add lifestyle</button>
                                    <button class="dropdown-item"
                                        data-bs-target="#subscriptions-modal"
                                        data-bs-toggle="modal"
                                        type="button">
                                        Add subscription</a>
                                </div>
                            </div>
                            @if (isset($identity->sinner))
                            <button class="btn btn-primary btn-sm mx-1"
                                data-id="{{ $key }}" data-bs-target="#sin-modal"
                                data-bs-toggle="modal" disabled type="button">
                                SIN - {{ $identity->sinner }}
                            </button>
                            @else
                            <button class="btn btn-primary btn-sm mx-1"
                                data-id="{{ $key }}" data-bs-target="#sin-modal"
                                data-bs-toggle="modal" type="button">
                                <span aria-hidden="true" class="bi bi-pencil"></span>
                                Change SIN - {{ $identity->sin }}
                            </button>
                            @endif
                        @else
                            <button class="btn btn-success btn-sm mx-1"
                                data-id="{{ $key }}" data-bs-target="#sin-modal"
                                data-bs-toggle="modal" type="button">
                                <span aria-hidden="true" class="bi bi-plus"></span>
                                Add SIN
                                </button>
                        @endif
                        <button class="btn btn-danger btn-sm identity"
                            type="button">
                            <span aria-hidden="true" class="bi bi-dash"></span>
                            Remove
                        </button>
                    </div>
                    <ul class="list-group list-group-flush mt-3 ms-3 me-0">
                        @foreach ($identity->licenses as $index => $license)
                            <li class="list-group-item pe-0">
                                License: {{ $license }}
                                <div class="float-end">
                                    <button class="btn btn-danger btn-sm license"
                                        data-license-index="{{ $index }}"
                                        type="button">
                                        <span aria-hidden="true" class="bi bi-dash"></span>
                                        Remove
                                    </button>
                                </div>
                            </li>
                        @endforeach
                        @foreach ($identity->lifestyles as $lifestyle)
                            <li class="list-group-item pe-0">
                                <div class="float-end">
                                    <button class="btn btn-danger btn-sm lifestyle"
                                        data-lifestyle="{{ $lifestyle->id }}"
                                        data-identity="{{ $key }}"
                                        type="button">
                                        <span aria-hidden="true" class="bi bi-dash"></span>
                                        Remove
                                    </button>
                                </div>
                                Lifestyle: {{ $lifestyle }}
                                - {{ $lifestyle->quantity }}
                                {{ \Str::of('month')->plural($lifestyle->quantity) }}
                                @if (0 !== count($lifestyle->options))
                                    <br><small class="text-muted ms-3">
                                    @foreach ($lifestyle->options as $option)
                                        {{ $option }}@if (!$loop->last),@endif
                                    @endforeach
                                    </small>
                                @endif
                            </li>
                        @endforeach
                    </ul>
                </li>
                @endforeach
                <li class="list-group-item" id="no-identities"
                    @if (0 !== count($character->getIdentities()))
                        style="display:none"
                    @endif
                    >No identities</li>
                <li class="list-group-item">
                    <button class="btn btn-success"
                        data-bs-target="#identities-modal"
                        data-bs-toggle="modal"
                        type="button">Add identity</button>
                </li>
            </ul>
        </div>
        <div class="col-3"></div>
    </div>

    @include('Shadowrun5e.create-next')
    </form>

    @include('Shadowrun5e.create-points')

    <div class="modal" id="contacts-modal" tabindex="-1" role="dialog">
        <div class="modal-dialog modal-xl" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Add contact</h5>
                    <button aria-label="Close" class="btn-close"
                        data-bs-dismiss="modal" type="button"></button>
                </div>
                <div class="modal-body row"><div class="col">
                    <p>
                        Contacts are a vital part of a shadowrunner's life.
                        Contacts sell illegal or hard-to-obtain items, alert
                        runners to potential employment, know someone who knows
                        someone who knows something the runner wants to know, or
                        are knowledgeable about the layout of a heavily guarded
                        corporate compound. Some contacts can supply substances
                        runners are addicted too, fence stolen goods, and maybe,
                        if they're loyal enough, bail the shadowrunner out of
                        the Lone Star holding cell. Having a wide variety of
                        Contacts can be a valuable investment. Every character
                        receives free Karma to spend on their initial contacts.
                        This Karma is equal to a Character's Charisma rating
                        &times; 3.
                    </p>

                    <p>
                        Each Contact has a Connection and a Loyalty rating. Any
                        Contacts a player buys must have a minimum rating of 1
                        in Connection and a minimum rating of 1 in Loyalty.
                        Connection represents how much reach and influence a
                        Contact has, both within the shadows and in the world at
                        large, to get things done or to make things happen.
                        Loyalty reflects how loyal the contact is to the runner
                        and how much they'll endure without shattering whatever
                        bond the two have. At Loyalty 1 or 2, the Contact has
                        only a business relationship with the character. Any
                        qualms they have about turning the runner in are tied to
                        profits they may lose if the runner isn't around, not so
                        much because of any close personal feelings. With a
                        higher Loyalty rating, the Contact has a stronger and
                        more personal relationship (i.e., friendship) with the
                        character, and is more likely to take some risk or go
                        out of his way to help the character. For specific rules
                        on the use of Contacts, see p. 386.
                    </p>

                    <form class="needs-validation" novalidate>
                        <div class="form-group">
                            <label for="contact-name">Name</label>
                            <div class="input-group">
                                <input aria-describedby="contact-name-help"
                                    class="form-control" id="contact-name"
                                    required type="text">
                                <button class="btn btn-outline-secondary suggest-name"
                                    type="button">Suggest</button>
                            </div>
                            <small class="form-text text-muted"
                                id="contact-name-help">
                                Your contact's handle is what they go by on the
                                street or what your character knows them as.
                            </small>
                            <div class="invalid-feedback">
                                You must give your contact a name.
                            </div>
                        </div>
                        <div class="form-group">
                            <label for="contact-archetype">Archetype</label>
                            <input aria-describedby="contact-archetype-help"
                                class="form-control" id="contact-archetype"
                                list="archetypes" required type="text">
                            <small class="form-text text-muted"
                                id="contact-archetype-help">
                                Gives a general idea of what the contact's role
                                is in the world.
                            </small>
                            <div class="invalid-feedback">
                                You must give your contact an archetype.
                            </div>
                        </div>
                        <div class="form-group">
                            <label for="contact-connection">Connection</label>
                            <input aria-describedby="contact-connection-help"
                                class="form-control" id="contact-connection"
                                max="12" min="1" required type="number">
                            <small class="form-text text-muted"
                                id="contact-connection-help">
                                Connection represents how much reach and
                                influence a Contact has, both within the shadows
                                and in the world at large, to get things done or
                                to make things happen.
                            </small>
                            <div class="invalid-feedback">
                                You must give your contact a connection rating
                                between 1 and 12.
                            </div>
                        </div>
                        <div class="form-group">
                            <label for="contact-loyalty">Loyalty</label>
                            <input aria-describedby="contact-loyalty-help"
                                class="form-control" id="contact-loyalty"
                                max="6" min="1" required type="number">
                            <small class="form-text text-muted"
                                id="contact-loyalty-help">
                                Loyalty reflects how loyal the contact is to the
                                runner and how much they'll endure without
                                shattering whatever bond the two have.
                            </small>
                            <div class="invalid-feedback">
                                You must give your contact a loyalty rating
                                between 1 and 6.
                            </div>
                        </div>
                        <div class="form-group">
                            <label for="contact-notes">Notes</label>
                            <textarea aria-describedby="contact-notes-help"
                                class="form-control" id="contact-notes"></textarea>
                            <small class="form-text text-muted"
                                id="contact-notes-help">
                                Notes give the contact life and help the GM both
                                to roleplay them and to involve them in the
                                universe. How do they know your character? What
                                nationality are they?
                            </small>
                        </div>
                        <div>
                            <button class="btn btn-primary mr-1" type="submit">
                                Add contact
                            </button>
                            <button class="btn btn-secondary"
                                data-bs-dismiss="modal" type="button">
                                Cancel
                            </button>
                        </div>
                    </form>
                </div></div>
            </div>
        </div>
    </div>

    <div class="modal" id="identities-modal" tabindex="-1" role="dialog">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Choose identity</h5>
                    <button aria-label="Close" class="btn-close"
                        data-bs-dismiss="modal" type="button"></button>
                </div>
                <div class="modal-body row"><div class="col">
                    <p>
                        A character's identity is who they claim to be. A
                        shadowrunner may have any number of identities for
                        different purposes (one for their legal activities,
                        another for their illegal matrix activities, another for
                        their illegal meatspace activities, etc).
                    </p>

                    <p>
                        Each identity may have a Fake SIN attached to it.
                        Without a SIN an identity is relatively useless, since
                        it can't be used to buy anything or show that the person
                        is who they say they are.
                    </p>

                    <p>
                        A SIN may have licenses attached to it. A license gives
                        the runner a legal right to own restricted gear,
                        practice magic, or otherwise not get thrown in jail or
                        fined.
                    </p>

                    <p>
                        An identity may have any number of lifestyles, DocWagon
                        contracts, custom food subscriptions, or other expenses.
                    </p>

                    <form class="needs-validation" novalidate>
                        <div class="form-group">
                            <label for="identity-name">Name</label>
                            <div class="input-group">
                                <input aria-describedby="identity-name-help"
                                    class="form-control" id="identity-name"
                                    required type="text">
                                <button class="btn btn-outline-secondary suggest-name"
                                    type="button">Suggest</button>
                            </div>
                            <small class="form-text text-muted"
                                id="identity-name-help">
                                Name the 'Runner uses for this identity, which
                                may or may not match the one on their SIN.
                            </small>
                            <div class="invalid-feedback">
                                Identity requires a name.
                            </div>
                        </div>
                        <div class="form-group">
                            <label for="identity-notes">Notes</label>
                            <textarea class="form-control"
                                id="identity-notes"></textarea>
                            <small class="form-text text-muted"
                                id="identity-notes-help">
                                Anything you'd like to note about this identity,
                                like &quot;Matrix only&quot; or &quot;Mostly
                                legal&quot;.
                            </small>
                        </div>
                        <div>
                            <button class="btn btn-primary mr-1" type="submit">
                                Add identity
                            </button>
                            <button class="btn btn-secondary"
                                data-bs-dismiss="modal" type="button">
                                Cancel
                            </button>
                        </div>
                    </form>
                </div></div>
            </div>
        </div>
    </div>

    <div class="modal" id="sin-modal" tabindex="-1" role="dialog">
        <div class="modal-dialog modal-xl" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Choose SIN rating</h5>
                    <button aria-label="Close" class="btn-close"
                        data-bs-dismiss="modal" type="button"></button>
                </div>
                <div class="modal-body row"><div class="col">
                    <p>
                        For those without the privilege (or curse) of being born
                        with a SIN, there is very little choice in how they can
                        live. You either get issued a real SIN after birth
                        (which requires an act of government—good luck with
                        that!), live your life on the outside (which most choose
                        to License examples do), or get yourself a fake SIN.
                        Although SIN registry databases are incredibly secure,
                        they are still accessible online (they have to be) which
                        makes them vulnerable. Demand and value for fake SINs is
                        such that many of the shadier organizations in the
                        world, including all major criminal syndicates, make a
                        business of creating fake SINs. Getting a fake SIN
                        created and registered with all of the proper
                        authorities is a long and involved process of hacking
                        and data fraud that exploits loopholes and other
                        identified flaws in the system. Generally speaking, the
                        more time that is taken in crafting a false identity,
                        the more believable (or "real") it becomes. Hastily
                        created identities may work if someone just wants to be
                        able to buy a Nuke 'em Burger at the Stuffer Shack, but
                        it won't hold up to any sort of scrutiny.
                    </p>

                    <p>
                        The amount of time and care taken in creating a fake SIN
                        is represented by its Rating. A low Rating SIN consists
                        of only the most basic information—such as the SIN
                        number itself. Related information such as biometric
                        data will likely be missing or obviously false if
                        checked ("Hey, this is the DNA of a chicken …"). Other
                        issues may be the consistency, or fit, of the identity
                        to the individual. If a runner just needs an
                        identity—any identity—right now, they may end up
                        purchasing a SIN for a ten-year-old Nigerian girl.
                        Higher Rating, and thus more expensive, fake SINs have
                        been lovingly crafted over time with a great deal of
                        attention to detail. An identity will be chosen that
                        matches the age and nationality of the person purchasing
                        it, and it will have plausible supporting information
                        such as travel and purchasing history. Biometric data
                        associated with a high-Rating SIN will be from a real
                        person with the same sex and nationality as the
                        purchaser with (if the extra fee is paid) matching
                        organic samples available (blood, skin cells, hair—just
                        don't ask where they came from). For availability and
                        prices of purchasing Fake SINs, see p. 367.
                    </p>

                    <table class="table">
                        <thead>
                            <tr>
                                <th scope="col">Rating</th>
                                <th scope="col">Avail</th>
                                <th class="text-right" scope="col">Cost</th>
                                <th scope="col">Description</th>
                                <th>&nbsp;</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr>
                                <td>1</td>
                                <td>3F</td>
                                <td class="text-right">¥2,500</td>
                                <td>
                                    Random anybody, age, nationality, and sex
                                    may not match; no supporting data
                                </td>
                                <td style="white-space:nowrap;">
                                    <button class="btn btn-success" data-rating="1"
                                        type="button">
                                        <span aria-hidden="true" class="bi bi-plus">
                                        </span> Buy
                                    </button>
                                </td>
                            </tr>
                            <tr>
                                <td>2</td>
                                <td>6F</td>
                                <td class="text-right">¥5,000</td>
                                <td>
                                    Rough match; sex matches, age and
                                    nationality &quot;pretty close,&quot; no
                                    supporting data
                                </td>
                                <td style="white-space:nowrap;">
                                    <button class="btn btn-success" data-rating="2"
                                        type="button">
                                        <span aria-hidden="true" class="bi bi-plus">
                                        </span> Buy
                                    </button>
                                </td>
                            </tr>
                            <tr>
                                <td>3</td>
                                <td>9F</td>
                                <td class="text-right">¥7,500</td>
                                <td>
                                    Good match; sex, age, and nationality match;
                                    supporting data, but obviously fake
                                </td>
                                <td style="white-space:nowrap;">
                                    <button class="btn btn-success" data-rating="3"
                                        type="button">
                                        <span aria-hidden="true" class="bi bi-plus">
                                        </span> Buy
                                    </button>
                                </td>
                            </tr>
                            <tr>
                                <td>4</td>
                                <td>12F</td>
                                <td class="text-right">¥10,000</td>
                                <td>
                                    Casually plausible; sex, age, and
                                    nationality match; supporting data appears
                                    valid only on cursory checks
                                </td>
                                <td style="white-space:nowrap;">
                                    <button class="btn btn-success" data-rating="4"
                                        type="button">
                                        <span aria-hidden="true" class="bi bi-plus">
                                        </span> Buy
                                    </button>
                                </td>
                            </tr>
                            <tr>
                                <td>5</td>
                                <td>15F</td>
                                <td class="text-right">¥12,500</td>
                                <td>
                                    Good fit; all statistics match; valid
                                    biometrics for another person (with
                                    samples); some supporting data and history)
                                </td>
                                <td style="white-space:nowrap;">
                                    <button class="btn btn-success" disabled
                                        type="button">
                                        <span aria-hidden="true" class="bi bi-plus">
                                        </span> Buy
                                    </button>
                                </td>
                            </tr>
                            <tr>
                                <td>6</td>
                                <td>18F</td>
                                <td class="text-right">¥15,000</td>
                                <td>
                                    Alternate life; all statistics match; valid
                                    biometrics with samples; complete and
                                    entirely believable history
                                </td>
                                <td style="white-space:nowrap;">
                                    <button class="btn btn-success" disabled
                                        type="button">
                                        <span aria-hidden="true" class="bi bi-plus">
                                        </span> Buy
                                    </button>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div></div>
            </div>
        </div>
    </div>

    <x-slot name="javascript">
        <script>
            let character = @json($character);
            character.contacts = character.contacts || [];
            character.identities = character.identities || [];
        </script>
        <script src="/js/Shadowrun5e/create-common.js"></script>
        <script src="/js/Shadowrun5e/Points.js"></script>
        <script src="/js/Shadowrun5e/create-social.js"></script>
    </x-slot>
</x-app>
