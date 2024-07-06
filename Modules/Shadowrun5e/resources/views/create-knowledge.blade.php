<x-app>
    <x-slot name="title">Create character</x-slot>
    <x-slot name="head">
        <link href="/css/datatables.min.css" rel="stylesheet">
        <link href="/css/Shadowrun5e/character-generation.css" rel="stylesheet">
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

    <datalist id="languages">
        <option value="Arabic">
        <option value="Armenian">
        <option value="Chinese">
        <option value="City Speak">
        <option value="English">
        <option value="Esperanto">
        <option value="Farsi">
        <option value="French">
        <option value="German">
        <option value="Hindi">
        <option value="Italian">
        <option value="Japanese">
        <option value="Korean">
        <option value="Latin">
        <option value="Or'zet" label="Or'zet (Orkish)">
        <option value="Upvehu" label="Upvehu (Pixie)">
        <option value="Russian">
        <option value="Salish">
        <option value="Spanish">
        <option value="Sperethiel" label="Sperethiel (Elven)">
    </datalist>

    <datalist id="knowledge-examples">
        <option value="20th Century Comic Books">
        <option value="Action Trids">
        <option value="Alcohol">
        <option value="Anthropology">
        <option value="Application Design">
        <option value="Archaeology">
        <option value="Area Knowledge">
        <option value="Arms Dealers">
        <option value="Architecture">
        <option value="Art">
        <option value="Artificial Intelligence">
        <option value="Astrology">
        <option value="Atlantis Research">
        <option value="Automated Factory Familiarity">
        <option value="Bars (city)">
        <option value="BTL Dealers">
        <option value="BTL Production">
        <option value="Biology">
        <option value="Biotechnology">
        <option value="Black IC Design">
        <option value="Body Armor Fabrication">
        <option value="Botany">
        <option value="Bushido Philosophy">
        <option value="Cellular Network Familiarity">
        <option value="Chat Room Familiarity">
        <option value="Chat Rooms">
        <option value="Cheap Synthahol Guzzling">
        <option value="Chemistry">
        <option value="Chokepoint Familiarity">
        <option value="Chop Shops">
        <option value="Civil Engineering">
        <option value="Combat Biking">
        <option value="Communications Satellite Familiarity">
        <option value="Computer Background">
        <option value="Conspiracy Theories">
        <option value="Cooking">
        <option value="Corporate Culture">
        <option value="Corporate Finances">
        <option value="Corporate Hosts">
        <option value="Corporate Law (corp)">
        <option value="Corporate Politics">
        <option value="Corporate Program Designers">
        <option value="Corporate Rumors">
        <option value="Corporate Security (corp)">
        <option value="Criminal Organizations">
        <option value="Critters">
        <option value="Current Events">
        <option value="Cyberdecks">
        <option value="Cybertechnology">
        <option value="Cyberterminal Code Design">
        <option value="Cyberterminal Design">
        <option value="Data Archive Familiarity">
        <option value="Data Brokerage">
        <option value="Data Havens">
        <option value="Data Tracing">
        <option value="Databases">
        <option value="Decker Tricks">
        <option value="Deckmeisters">
        <option value="Defensive Utility Design">
        <option value="Demolitions Background">
        <option value="Desert Wars">
        <option value="Dive Bars (city)">
        <option value="Dowsing">
        <option value="Dragons">
        <option value="Drugs">
        <option value="Ecology">
        <option value="Economics">
        <option value="Electronics Background">
        <option value="Elven Society">
        <option value="Elven Wine">
        <option value="Engineering">
        <option value="Esoteric Trivia">
        <option value="Farming">
        <option value="Fashion">
        <option value="Fences">
        <option value="Flatvid Movies">
        <option value="Forensics">
        <option value="Frame Core Design">
        <option value="Fringe Cults">
        <option value="Gambling Card Games">
        <option value="Game Host Familiarity">
        <option value="Gang Identification">
        <option value="Gang Turf">
        <option value="Gear Value">
        <option value="Geology">
        <option value="Gray IC Design">
        <option value="Gunsmithing">
        <option value="Hardcore Punk Bands">
        <option value="History">
        <option value="Humanis Policlub">
        <option value="IC Construct Design">
        <option value="IC Profiles">
        <option value="Iconography">
        <option value="Jackpoint Locations">
        <option value="Japanese Culture">
        <option value="Japanese Society">
        <option value="Law Enforcement Procedures (agency)">
        <option value="Legendary Deckers">
        <option value="Legendary Martial Artists">
        <option value="Literature">
        <option value="Local Landmarks">
        <option value="Local Politics">
        <option value="Local Rumor Mill">
        <option value="Lone Star Tactics">
        <option value="Mafia Finances">
        <option value="Mafia Politics">
        <option value="Mafia-Controlled Establishments">
        <option value="Magic Background">
        <option value="Magical Forensics">
        <option value="Magical Goods Value">
        <option value="Magical Groups">
        <option value="Magical Theory">
        <option value="Magical Threats">
        <option value="Matrix Bank Familiarity">
        <option value="Matrix Gangs">
        <option value="Matrix Programs">
        <option value="Matrix Security Procedures">
        <option value="Matrix Theory">
        <option value="Matrix Topography">
        <option value="Mechanical Traps">
        <option value="Medicine">
        <option value="Meditation">
        <option value="Megacorporate Policies">
        <option value="Megacorporate Politics">
        <option value="Megacorporate Research">
        <option value="Megacorporate Security">
        <option value="Megacorporations">
        <option value="Mercenary Groups">
        <option value="Mercenary Hot Spots">
        <option value="Metahumanity">
        <option value="Metallurgy">
        <option value="Metalworking">
        <option value="Microchips">
        <option value="Military Procedure">
        <option value="Military Winged Aircraft">
        <option value="Miltech Manufacturers">
        <option value="Modern Art">
        <option value="Modern Jazz">
        <option value="Music">
        <option value="NAN Border Patrol Tactics">
        <option value="Named Spirits">
        <option value="National Law (nation)">
        <option value="Night Clubs (city)">
        <option value="Offensive Utility Design">
        <option value="Opera">
        <option value="Operational Utility Design">
        <option value="Organized Crime">
        <option value="Parabotony">
        <option value="Parazoology">
        <option value="Philosophy">
        <option value="Physics">
        <option value="Pirate Trid Broadcasters">
        <option value="Poetry">
        <option value="Police Procedures">
        <option value="Politics">
        <option value="Professional Bodyguarding">
        <option value="Programming Suite Design">
        <option value="Prostitution Rings">
        <option value="Psychology">
        <option value="Redmond Barrens">
        <option value="Restaurants (city)">
        <option value="Roleplaying Games of the Late 20th Century">
        <option value="RTG Familiarity">
        <option value="Rumor Mill">
        <option value="Safe Houses (city)">
        <option value="Satellite Networks">
        <option value="Sci-Fi Simchips">
        <option value="Scrounging">
        <option value="Sculpture">
        <option value="Seattle Corporate Hosts">
        <option value="Seattle High Society">
        <option value="Seattle Junkyards">
        <option value="Seattle LTG">
        <option value="Seattle Ork Underground">
        <option value="Security Companies">
        <option value="Security Design">
        <option value="Security Network Familiarity">
        <option value="Security Procedures">
        <option value="Seedy Ork Bars">
        <option value="Shadowrunner Haunts">
        <option value="Shadowrunner Teams">
        <option value="Sim Starlets">
        <option value="Small Unit Tactics">
        <option value="Smuggler Havens">
        <option value="Smuggler Routes">
        <option value="Sociology">
        <option value="Special Utility Design">
        <option value="Spell Design">
        <option value="Sports">
        <option value="Street Docs (city)">
        <option value="Street Gangs (city)">
        <option value="Street Rumors">
        <option value="SWAT Team Tactics">
        <option value="Talismongering">
        <option value="Tir Tairngire Politics">
        <option value="Toxic Hazards">
        <option value="Trace IC Design">
        <option value="Tracking">
        <option value="Troll Thrash Metal Bands">
        <option value="Triad Politics">
        <option value="Tribal Culture (tribe)">
        <option value="Tribal Law (tribe)">
        <option value="Tribal Lore">
        <option value="Tribal Politics">
        <option value="Underworld Finance">
        <option value="Underworld Politics">
        <option value="Upscale Bars (city)">
        <option value="Urban Brawl">
        <option value="Vehicle Chop Shops (city)">
        <option value="Virtual Meeting Spots">
        <option value="Weapon Acquisition">
        <option value="Weapons Manufacturers">
        <option value="Weightlifting">
        <option value="White IC Design">
        <option value="Woodworking">
        <option value="Worm Design">
        <option value="Yakuza Territory">
        <option value="Zoology">
    </datalist>

    <datalist id="language-specializations">
        <option value="'l33tspseak">
        <option value="Corp">
        <option value="Milspec">
        <option value="Orbital">
        <option value="Read/Write">
        <option value="Speak">
        <option value="Street">
    </datalist>

    <form action="" method="POST">
    @csrf

    <div class="row mt-3">
        <div class="col-1"></div>
        <div class="col">
            <h1>Knowledge skills</h1>

            <p>
                Characters receive free Knowledge and Language skills points
                equal to (Intuition rating + Logic rating) x 2. In addition to
                the free points, your character receives one language that they
                know as a native language at no cost.
            </p>
        </div>
        <div class="col-3"></div>
    </div>

    <div class="row">
        <div class="col-1"></div>
        <div class="col">
            <ul class="list-group" id="skills">
                @php $skills = false; @endphp
                @foreach ($character->getKnowledgeSkills(onlyKnowledges: true) as $skill)
                    @php $skills = true; @endphp
                    <li class="list-group-item" data-id="{{ $skill->id }}-{{ $skill->category }}">
                        <div class="row">
                            <label class="col col-form-label text-nowrap name"
                                for="{{ $skill->id }}">
                                {{ $skill }}
                                @if ($skill->specialization)
                                    (+2 {{ $skill->specialization }})
                                @endif
                            </label>
                            <div class="col">
                                <input name="skill-names[]" type="hidden"
                                    value="{{ $skill }}">
                                <input name="skill-categories[]" type="hidden"
                                    value="{{ $skill->category }}">
                                <input class="form-control level text-center"
                                    id="{{ $skill->id }}" min="0" max="6"
                                    name="skill-levels[]" step="1" type="number"
                                    value="{{ $skill->level }}">
                                <input name="skill-specializations[]"
                                    type="hidden"
                                    value="{{ $skill->specialization }}">
                            </div>
                            <div class="col text-right text-nowrap">
                                @if ($skill->specialization)
                                <button class="btn btn-danger btn-sm specialize"
                                    type="button">
                                    <span aria-hidden="true" class="bi bi-dash"></span>
                                    Specialization
                                </button>
                                @else
                                <button class="btn btn-success btn-sm specialize"
                                    data-bs-target="#specialize-modal"
                                    data-bs-toggle="modal" type="button">
                                    <span aria-hidden="true" class="bi bi-plus"></span>
                                    Specialization
                                </button>
                                @endif
                                <button class="btn btn-danger btn-sm skill"
                                    type="button">
                                    <span aria-hidden="true" class="bi bi-dash"></span>
                                    Knowledge
                                </button>
                            </div>
                        </div>
                    </li>
                @endforeach
                <li class="list-group-item" id="no-knowledge"
                    @if ($skills)
                        style="display:none;"
                    @endif
                    >
                    No knowledge skills
                </li>
                @php $skills = false; @endphp
                @foreach ($character->getKnowledgeSkills(onlyLanguages: true) as $skill)
                    @php $skills = true; @endphp
                    <li class="list-group-item language" data-id="{{ $skill->id }}-language">
                        <div class="row">
                            <label class="col col-form-label text-nowrap name"
                                for="{{ $skill->id }}">
                                {{ $skill }}
                                @if ($skill->specialization)
                                    (+2 {{ $skill->specialization }})
                                @endif
                            </label>
                            <div class="col text-center">
                                <input name="skill-names[]" type="hidden"
                                    value="{{ $skill }}">
                                <input name="skill-categories[]" type="hidden"
                                    value="language">
                                <input class="form-control level text-center"
                                    id="{{ $skill->id }}" name="skill-levels[]"
                                    @if ('N' === $skill->level)
                                        readonly type="text" value="N"
                                    @else
                                    min="0" max="6" step="1" type="number"
                                    value="{{ $skill->level }}"
                                    @endif
                                    >
                                <input name="skill-specializations[]"
                                    type="hidden"
                                    value="{{ $skill->specialization }}">
                            </div>
                            <div class="col text-right text-nowrap">
                                @if ($skill->specialization)
                                <button class="btn btn-danger btn-sm specialize"
                                    type="button">
                                    <span aria-hidden="true" class="bi bi-dash"></span>
                                    Specialization
                                </button>
                                @else
                                <button class="btn btn-success btn-sm specialize"
                                    data-bs-target="#specialize-modal"
                                    data-bs-toggle="modal" type="button">
                                    <span aria-hidden="true" class="bi bi-plus"></span>
                                    Specialization
                                </button>
                                @endif
                                <button class="btn btn-danger btn-sm skill"
                                    type="button">
                                    <span aria-hidden="true" class="bi bi-dash"></span>
                                    Language
                                </button>
                            </div>
                        </div>
                    </li>
                @endforeach
                <li class="list-group-item" id="no-languages"
                    @if ($skills)
                        style="display:none;"
                    @endif
                    >No languages</li>
                <li class="list-group-item">
                    <button class="btn btn-success mr-1"
                        data-bs-target="#knowledge-modal" data-bs-toggle="modal"
                        type="button">
                        <span aria-hidden="true" class="bi bi-plus"></span>
                        Knowledge skill
                    </button>
                    <button class="btn btn-success"
                        data-bs-target="#language-modal" data-bs-toggle="modal"
                        type="button">
                        <span aria-hidden="true" class="bi bi-plus"></span>
                        Language
                    </button>
                </li>
            </ul>
        </div>
        <div class="col-3"></div>
    </div>

    @include('shadowrun5e::create-next')
    </form>

    <div class="modal" id="knowledge-modal" tabindex="-1" role="dialog">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Choose knowledge skill</h5>
                    <button aria-label="Close" class="btn-close"
                        data-bs-dismiss="modal" type="button"></button>
                </div>
                <div class="modal-body">
                    <p>
                        Knowledge skills complement your character. They create
                        meaning and history behind the active skills and
                        abilities you choose. Shadowrun gives you some room to
                        play with knowledge skills. You’re free to take
                        practically any knowledge skill you can think up for
                        your character.
                    </p>
                    <div class="form-group row my-1">
                        <label class="col-3 col-form-label"
                            for="choose-knowledge">
                            Name
                        </label>
                        <input class="col form-control" id="choose-knowledge"
                            list="knowledge-examples" type="text">
                    </div>
                    <div class="form-group row my-1">
                        <label class="col-3 col-form-label"
                            for="knowledge-type">
                            Type
                        </label>
                        <select class="col form-control" id="knowledge-type">
                            <option value="">Choose type
                            <option value="academic">Academic (logic)
                            <option value="interests">Interests (intuition)
                            <option value="professional">Professional (logic)
                            <option value="street">Street (intuition)
                        </select>
                    </div>
                    <div class="form-group row my-1">
                        <label class="col-3 col-form-label"
                            for="knowledge-level">
                            Level
                        </label>
                        <input class="col form-control" id="knowledge-level"
                            max="6" min="1" type="number">
                    </div>
                    <div class="row">
                        <div class="col">
                            <button class="btn btn-primary" disabled
                            type="button">
                            Add knowledge
                            </button>&nbsp;
                            <button class="btn btn-secondary"
                                data-bs-dismiss="modal" type="button">
                                Cancel
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="modal" id="specialize-modal" tabindex="-1"
        role="dialog">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Choose specialization</h5>
                    <button aria-label="Close" class="btn-close"
                        data-bs-dismiss="modal" type="button"></button>
                </div>
                <div class="modal-body">
                    <p>
                        Enter a specialization for
                        <span id="knowledge-specialization-skill-name"></span>
                    </p>
                    <p>
                        <input class="form-control"
                            id="knowledge-specialization-entry" type="text">
                    </p>
                    <p>
                        <button class="btn btn-primary mr-1" type="button">
                            Specialize
                        </button>
                        <button class="btn btn-secondary"
                            data-bs-dismiss="modal" type="button">
                            Cancel
                        </button>
                    </p>
                </div>
            </div>
        </div>
    </div>

    <div class="modal" id="language-modal" tabindex="-1" role="dialog">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Choose language</h5>
                    <button aria-label="Close" class="btn-close"
                        data-bs-dismiss="modal" type="button"></button>
                </div>
                <div class="modal-body">
                    <p>
                        Languages give your character some extra flavor in
                        addition to just allowing them to understand and speak a
                        language.
                    </p>

                    <p>
                        Language skills are purchased and have numerical
                        ratings. This rating represents how well the character
                        understands and comprehends that language. As long as
                        the character has at least a rating of 1, the character
                        has a chance to be able to speak and/or write the
                        language and to interpret the gist of what is said or
                        written, even if they don't catch every nuance. The
                        higher the rating, the more fluent the character is in
                        that language. At character creation, no character may
                        possess a knowledge or a language skill higher than
                        rating 6. Language skills use Intuition as their linked
                        Attribute.
                    </p>

                    <div class="form-group row my-1">
                        <label class="col-3 col-form-label"
                            for="choose-language">
                            Language
                        </label>
                        <input class="col form-control"
                            id="choose-language" list="languages" type="text">
                    </div>
                    <div class="form-group row my-1">
                        <label class="col-3 form-check-label"
                            for="native">
                            Native
                        </label>
                        <div class="col form-check">
                            <input class="form-check-input" type="checkbox"
                                value="native" id="native">
                        </div>
                    </div>
                    <div class="form-group row my-1">
                        <label class="col-3 col-form-label"
                            for="language-level">
                            Level
                        </label>
                        <input class="col form-control" id="language-level"
                            max="6" min="1" step="1" type="number">
                    </div>
                    <div class="row">
                        <div class="col">
                            <button class="btn btn-primary mr-1" disabled
                                type="button">
                                Add language
                            </button>
                            <button class="btn btn-secondary"
                                data-bs-dismiss="modal" type="button">
                                Cancel
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <template id="skill-row">
        <li class="list-group-item">
            <div class="row">
                <label class="col col-form-label text-nowrap name"></label>
                <div class="col">
                    <input name="skill-names[]" type="hidden">
                    <input name="skill-categories[]" type="hidden">
                    <input class="form-control level text-center" min="0"
                        max="6" name="skill-levels[]" step="1" type="number">
                    <input name="skill-specializations[]" type="hidden">
                </div>
                <div class="col text-right text-nowrap">
                    <button class="btn btn-success btn-sm specialize"
                        data-bs-target="#specialize-modal" data-bs-toggle="modal"
                        type="button">
                        <span aria-hidden="true" class="bi bi-plus"></span>
                        Specialization
                    </button>
                    <button class="btn btn-danger btn-sm skill" type="button">
                        <span aria-hidden="true" class="bi bi-dash"></span>
                        Skill
                    </button>
                </div>
            </div>
        </li>
    </template>

    <template id="language-row">
        <li class="list-group-item language">
            <div class="row">
                <label class="col col-form-label text-nowrap name"></label>
                <div class="col text-center">
                    <input name="skill-names[]" type="hidden">
                    <input name="skill-categories[]" type="hidden"
                        value="language">
                    <input class="form-control level text-center" min="0"
                        max="6" name="skill-levels[]" step="1" type="number">
                    <input name="skill-specializations[]" type="hidden">
                </div>
                <div class="col text-right text-nowrap">
                    <button class="btn btn-success btn-sm specialize"
                        data-bs-target="#specialize-modal" data-bs-toggle="modal"
                        type="button">
                        <span aria-hidden="true" class="bi bi-plus"></span>
                        Specialization
                    </button>
                    <button class="btn btn-danger btn-sm skill" type="button">
                        <span aria-hidden="true" class="bi bi-dash"></span>
                        Language
                    </button>
                </div>
            </div>
        </li>
    </template>

    @include('shadowrun5e::create-points')

    <x-slot name="javascript">
        <script>
            let character = @json($character);
            character.knowledgeSkills = character.knowledgeSkills || [];
        </script>
        <script src="/js/Shadowrun5e/create-common.js"></script>
        <script src="/js/Shadowrun5e/Points.js"></script>
        <script src="/js/Shadowrun5e/create-knowledge.js"></script>
    </x-slot>
</x-app>
