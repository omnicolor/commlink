<!doctype html>
<html lang="en-US">
<head>
    <meta charset="utf-8">
    <meta name="description" content="RPG character builder and campaign manager">
    <meta name="slack-app-id" content="A0A13UFFH">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <link rel="stylesheet" href="/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.9.1/font/bootstrap-icons.css">
    <title>Commlink RPG Manager - About</title>
</head>

<body>
<nav class="navbar navbar-expand-lg navbar-dark bg-dark fixed-top">
    <div class="container-fluid">
        <span class="navbar-brand">Commlink</span>
        <div class="collapse navbar-collapse" id="navbarSupportedContent">
            <ul class="navbar-nav me-auto mb-2 mb-lg-0">
                <li class="nav-item">
                    <a class="nav-link" href="{{ route('dashboard') }}">Home</a>
                </li>
                <li class="nav-item">
                    <span class="nav-link active">About</span>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="{{ route('about.systems') }}">
                        RPG systems
                    </a>
                </li>
            </ul>
        </div>
    </div>
</nav>

<div class="fixed-top" style="top:60px;">
    <div class="col-3">
        <nav id="about-commlink" class="h-100 flex-column align-items-stretch border-end pe-4">
            <nav class="nav nav-pills flex-column">
                <a class="nav-link" href="#chat-bot">Chat bot</a>
                <nav class="mt-1 nav nav-pills flex-column">
                    <a class="ms-3 nav-link" href="#chat-bot-slack">Slack installation</a>
                    <a class="ms-3 nav-link" href="#chat-bot-discord">Discord installation</a>
                    <span class="ms-3 nav-link">IRC installation</span>
                </nav>
                <a class="nav-link" href="#characters">Characters</a>
                <nav class="mt-1 nav nav-pills flex-column">
                    <a class="ms-3 nav-link" href="#sheets">Character sheets</a>
                    <a class="ms-3 nav-link" href="#generation">Char gen</a>
                    <a class="ms-3 nav-link" href="#import">Import</a>
                </nav>
                <a class="nav-link" href="#campaigns">Campaigns</a>
                <nav class="mt-1 nav nav-pills flex-column">
                    <a class="ms-3 nav-link" href="#campaign-manager">Campaign manager</a>
                    <a class="ms-3 nav-link" href="#gm-screen">GM screen</a>
                </nav>
            </nav>
        </nav>
    </div>
</div>

<div class="row" style="top:60px;">
    <div class="col-4"></div>
    <div class="col-6">
        <div style="height:60px"></div>
        <div data-bs-spy="scroll" data-bs-target="#about-commlink" data-bs-smooth-scroll="true" tabindex="0">
            <h1>About {{ config('app.name') }}</h1>

            <p>
                {{ config('app.name') }} is a tool for playing role playing
                games online, and managing all of the complexity of modern RPGs
                and the campaigns that bring them to life.
            </p>

            <hr>

            <div id="chat-bot">
                <h2>Chat bot</h2>

                <p>
                    With integrations for Slack, IRC, and Discord,
                    {{ config('app.name') }} allows you to roll dice in
                    system-specific ways. For example, if you register
                    a channel for Shadowrun 5th Edition, typing <code>/roll
                    5</code> will roll five six-sided dice and calculate how
                    many successes you got. It will automatically show glitches
                    and critical glitches as well.
                </p>

                <p>
                    <img alt="Rolling dice in a Slack channel registered as Shadowrun"
                        src="/images/about-slack.png">
                </p>

                <p>
                    In addition, if more than one channel are both registered
                    to the same campaign, rolls made in one channel will appear
                    in the other channels. The above roll was made in a Slack
                    channel and automatically shows up in the linked Discord
                    channel.
                </p>

                <p>
                    <img alt="Bot proxying a roll from Slack to Discord"
                        src="/images/about-discord.png">
                </p>

                <p>
                    The dice roller can be used on its own without the rest of
                    Commlink to flip a coin or roll some basic dice using
                    <a href="https://en.wikipedia.org/wiki/Dice_notation">standard dice notation</a>.
                </p>

                <p>
                    <img alt="Bot showing help information for an unlinked Slack channel"
                        src="/images/about-roller-unregistered.png">
                </p>

                <p>
                    If a channel is registered for a particular system, the
                    dice roller will change to better target that system. For
                    example, registering a channel for Shadowrun 5th Edition
                    allows just using a number to mean &ldquo;roll this many
                    six-sided dice&rdquo;:
                </p>

                <p>
                    <img alt="Bot showing help information for a channel playing Shadowrun 5th edition"
                        src="/images/about-roller-registered-not-linked.png">
                </p>

                <p>
                    Creating a character in Commlink and linking it to
                    a channel unlocks character-specific short rolls. For
                    example, linking a Shadowrun 5E character to a channel
                    unlocks some short commands that use the character's
                    attributes.
                </p>

                <p>
                    <img alt="Bot showing help information for a linked Shadowrun 5E character"
                        src="/images/about-roller-linked.png">
                </p>

                <div id="chat-bot-slack">
                    <h4 class="mt-4">Slack installation</h4>

                    <p>
                        Slack has made it relatively painless to install
                        {{ config('app.name') }} to your team's workspace.
                        Clicking this fancy button will take you to a page that
                        will allow you to install the app in any workspace you
                        have permission to do so:
                    </p>

                    <p>
                        <a href="https://slack.com/oauth/v2/authorize?client_id=2186724946.10037967527&scope=commands,incoming-webhook,channels:read,groups:read&user_scope=channels:history,channels:read,channels:write,groups:read,groups:write,im:write,team:read,users:read">
                            <img alt="Add to Slack" height="40" width="139"
                                src="https://platform.slack-edge.com/img/add_to_slack.png"
                                srcSet="https://platform.slack-edge.com/img/add_to_slack.png 1x, https://platform.slack-edge.com/img/add_to_slack@2x.png 2x">
                        </a>
                    </p>

                    <p>
                        If you're logged in to multiple workspaces, you may
                        have to choose where you want to install it in the
                        dropdown in the upper right of the page.
                    </p>

                    <p>
                        If you get a message stating that &ldquo;You are not
                        authorized to install Commlink on
                        &lt;workspace-url&gt;&rdquo; you may need to ask
                        whoever is in charge of your workspace to install it
                        for you. You could send them this URL to point them in
                        the right direction:
                    </p>

                    <div class="input-group input-group-sm">
                        <span class="input-group-text user-select-all overflow-hidden">
                            https://slack.com/oauth/v2/authorize?client_i
                        </span>
                        <button class="btn btn-outline-secondary copy-btn" type="button">
                            <i class="bi bi-clipboard"></i>
                        </button>
                    </div>
                </div>

                <div id="chat-bot-discord">
                    <h4 class="mt-4">Discord installation</h4>

                    <p>Discord installation is a bit more involved:</p>

                    <ol>
                        <li>
                            <a href="https://discord.com/api/oauth2/authorize?client_id=473580429438484480&permissions=17740899616832&redirect_uri=https%3A%2F%2Fcommlink.digitaldarkness.com%2Fdiscord%2Fcallback&response_type=code&scope=bot%20guilds.join%20guilds.members.read">Install the app</a>
                        </li>
                        <li>
                            For each channel you want {{ config('app.name') }}
                            to listen to, add the <strong>Commlink</strong>
                            role in the channel settings.
                        </li>
                    </ol>
                </div>
            </div>

            <hr>

            <div class="mt-4" id="characters">
                <h2 class="mt-4">Characters</h2>

                <h4 id="sheets">Character sheets</h4>

                <p>
                    Keep all of your characters in one place, with one common
                    interface, along with the previously mentioned integrations
                    with the dice roller bot.
                </p>

                <div id="character-sheets" class="carousel carousel-dark slide"
                    data-bs-ride="carousel" style="height: 780px">
                    <div class="carousel-indicators">
                        <button type="button" data-bs-target="#character-sheets"
                            data-bs-slide-to="0" class="active" aria-current="true"
                            aria-label="Capers character sheet"></button>
                        <button type="button" data-bs-target="#character-sheets"
                            data-bs-slide-to="1"
                            aria-label="Cyberpunk Red character sheet"></button>
                        <button type="button" data-bs-target="#character-sheets"
                            data-bs-slide-to="2"
                            aria-label="Shadowrun 5E character sheet"></button>
                        <button type="button" data-bs-target="#character-sheets"
                            data-bs-slide-to="3"
                            aria-label="The Expanse character sheet"></button>
                        <button type="button" data-bs-target="#character-sheets"
                            data-bs-slide-to="4"
                            aria-label="Star Trek Adventures"></button>
                        <button type="button" data-bs-target="#character-sheets"
                            data-bs-slide-to="5"
                            aria-label="Subversion"></button>
                        <button type="button" data-bs-target="#character-sheets"
                            data-bs-slide-to="6"
                            aria-label="Alien"></button>
                    </div>
                    <div class="carousel-inner">
                        <div class="carousel-item active">
                            <h5>Capers</h5>
                            <img alt="Capers character sheet"
                                 class="d-block w-100"
                                 src="/images/about-capers.png"
                                 width="900">
                        </div>
                        <div class="carousel-item">
                            <h5>Cyberpunk Red</h5>
                            <img alt="Cyberpunk Red character sheet"
                                 class="d-block w-100"
                                 src="/images/about-cyberpunk-red.png"
                                 width="900">
                        </div>
                        <div class="carousel-item">
                            <h5>Shadowrun 5th Edition</h5>
                            <img alt="Shadowrun 5th edition character sheet"
                                 class="d-block w-100"
                                 src="/images/about-shadowrun-5e.png"
                                 width="900">
                        </div>
                        <div class="carousel-item">
                            <h5>The Expanse</h5>
                            <img alt="Shadowrun 5th edition character sheet"
                                 class="d-block w-100"
                                 src="/images/about-the-expanse.png"
                                 width="900">
                        </div>
                        <div class="carousel-item">
                            <h5>Star Trek Adventures</h5>
                            <img alt="Shadowrun 5th edition character sheet"
                                class="d-block w-100"
                                src="/images/about-star-trek-adventures.png"
                                width="900">
                        </div>
                        <div class="carousel-item">
                            <h5>Subversion</h5>
                            <img alt="Subversion character sheet"
                                class="d-block w-100"
                                src="/images/about-subversion.png"
                                width="900">
                        </div>
                        <div class="carousel-item">
                            <h5>Alien</h5>
                            <img alt="Alien character sheet"
                                class="d-block w-100"
                                src="/images/about-alien.png"
                                width="900">
                        </div>
                    </div>
                    <button class="carousel-control-prev" type="button"
                        data-bs-target="#character-sheets" data-bs-slide="prev">
                        <span class="carousel-control-prev-icon"
                            aria-hidden="true"></span>
                        <span class="visually-hidden">Previous</span>
                    </button>
                    <button class="carousel-control-next" type="button"
                            data-bs-target="#character-sheets" data-bs-slide="next">
                        <span class="carousel-control-next-icon"
                              aria-hidden="true"></span>
                        <span class="visually-hidden">Next</span>
                    </button>
                </div>

                <h4 id="generation">Character generation</h4>

                <p>
                    The rules for character generation differ wildly between
                    systems. Some are so complicated it's almost impossible to
                    do correctly with just a pencil, some paper, and the
                    rulebook. The character generator in
                    {{ config('app.name') }} provides a step-by-step wizard to
                    creating a character and helps to validate that you haven't
                    broken any rules.
                </p>

                <h4 id="import">Character import</h4>

                <p>
                    Have you built a character in another tool?
                    {{ config('app.name') }} supports a few external systems
                    that you can import from:
                </p>

                <ul>
                    <li>Chummer 5 (Shadowrun 5th Edition)</li>
                    <li>Hero Lab (Shadowrun 5th Edition)</li>
                    <li>World Anvil (Cyberpunk Red and The Expanse)</li>
                </ul>
            </div>

            <hr>

            <div id="campaigns">
                <h2 class="mt-4">Campaigns</h2>

                <h4>Campaign overview</h4>

                <p>
                    Keeping tracking of players, characters, and NPCs can be
                    overwhelming. Commlink allows game masters to track
                    characters in their campaign, see character sheets, and
                    provide rewards.
                </p>

                <p>
                    <img alt="Campaign information showing an example Shadowrn 5th Edition campaign called Burning Edge"
                        src="/images/about-campaign.png" width="600">
                </p>

                <p>
                    Scheduling games can be a challenge.
                    {{ config('app.name') }} includes an event scheduler,
                    allowing GMs to schedule sessions for their table. Players
                    can RSVP in the web app or by reacting to chat bot messages
                    in linked channels.
                </p>

                <p>
                    GMs can also manage players allowed at the table, inviting
                    players to join or removing characters from the campaign.
                </p>

                <h4>GM screen</h4>

                <p>
                    GM screens built for each system allow tracking initiative
                    for combat and status monitors for character's health.
                </p>

                <p>
                    <img alt="Shadowrun 5th edition GM screen, showing an initiative tracker, health monitors, and lists of skills"
                        src="/images/about-gm-screen-sr5e.png" width="600">
                </p>
            </div>
        </div>
    </div>
</div>

<script src="/js/jquery.min.js"></script>
<script src="/js/bootstrap.bundle.min.js"></script>
</body>
</html>
