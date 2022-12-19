<!doctype html>
<html lang="en-US">
<head>
    <meta charset="utf-8">
    <meta name="description" content="RPG character builder and campaign manager">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <link rel="stylesheet" href="/css/bootstrap.min.css">
    <title>Commlink RPG Manager - About</title>
</head>

<body>
<nav class="navbar navbar-expand-lg navbar-dark bg-dark">
    <div class="container-fluid">
        <a class="navbar-brand" href="/">Commlink</a>
        <div class="collapse navbar-collapse" id="navbarSupportedContent">
            <ul class="navbar-nav me-auto mb-2 mb-lg-0">
                <li class="nav-item">
                    <a class="nav-link active" aria-current="page" href="#">
                        What is Commlink?
                    </a>
                </li>
            </ul>
        </div>
    </div>
</nav>

<div class="container-fluid">
    <div class="row mt-4">
        <div class="col-lg-3"></div>
        <div class="col">
            <h1>What is Commlink?</h1>

            <p>Commlink is a tool for playing role playing games online.</p>

            <h2 id="dice-roller">Dice roller</h2>

            <p>
                With integrations for both Slack and Discord, Commlink allows
                you to roll dice in system-specific ways. For example, if you
                register a channel for Shadowrun 5th Edition, typing
                <code>/roll 5</code> will roll five six-sided dice and calculate
                how many successes you got. It will automatically show glitches
                and critical glitches as well.
            </p>

            <p>
                <img alt="Rolling dice in a Slack channel registered as Shadowrun"
                     src="/images/about-slack.png">
            </p>

            <p>
                In addition, if more than one channel are both registered to the
                same campaign, rolls made in one channel will appear in the
                other channels. The above roll was made in a Slack channel and
                automatically shows up in the linked Discord channel.
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
                If a channel is registered for a particular system, the dice
                roller will change to better target that system. For example,
                registering a channel for Shadowrun 5th Edition allows just
                using a number to mean &ldquo;roll this many six-sided dice&rdquo;:
            </p>

            <p>
                <img alt="Bot showing help information for a channel playing Shadowrun 5th edition"
                     src="/images/about-roller-registered-not-linked.png">
            </p>

            <p>
                Creating a character in Commlink and linking it to a channel
                unlocks character-specific short rolls. For example, linking a
                Shadowrun 5E character to a channel unlocks some short commands
                that use the character's attributes.
            </p>

            <p>
                <img alt="Bot showing help information for a linked Shadowrun 5E character"
                     src="/images/about-roller-linked.png">
            </p>

            <h2 class="mt-4" id="character-manager">Character manager</h2>

            <p>
                Keep all of your characters in one place, with one common
                interface, along with the previously mentioned integrations with
                the dice roller bot.
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
                        data-bs-slide-to="5" aria-label="Avatar RPG"></button>
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
                        <h5>Avatar</h5>
                        <p>Coming soon!</p>
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

            <h2 class="mt-4" id="campaign-manager">Campaign manager</h2>

            <p>
                Keeping tracking of players, characters, and NPCs can be
                overwhelming. Commlink allows game masters to track characters
                in their campaign, see character sheets, and provide rewards
                (karma, experience points, gold, etc). GM screens built for each
                system allow tracking initiative for combat and status monitors
                for character's health.
            </p>

            <div id="campaign-info" class="carousel carousel-dark slide"
                data-bs-ride="carousel" style="height: 620px">
                <div class="carousel-indicators">
                    <button type="button" data-bs-target="#campaign-info"
                        data-bs-slide-to="0" class="active" aria-current="true"
                        aria-label="Campaign information - Shadowrun 5E"></button>
                    <button type="button" data-bs-target="#campaign-info"
                        data-bs-slide-to="1"
                        aria-label="Shadowrun 5E GM screen"></button>
                </div>
                <div class="carousel-inner">
                    <div class="carousel-item active">
                        <h5>Campaign information</h5>
                        <img alt="Campaign information"
                             class="d-block w-100"
                             src="/images/about-campaign.png" width="900">
                    </div>
                    <div class="carousel-item">
                        <h5>Shadowrun 5E GM screen</h5>
                        <img alt="Shadowrun 5th edition GM screen"
                            class="d-block w-100"
                            src="/images/about-gm-screen-sr5e.png" width="900">
                    </div>
                </div>
                <button class="carousel-control-prev" type="button"
                    data-bs-target="#campaign-info" data-bs-slide="prev">
                    <span class="carousel-control-prev-icon"
                        aria-hidden="true"></span>
                    <span class="visually-hidden">Previous</span>
                </button>
                <button class="carousel-control-next" type="button"
                    data-bs-target="#campaign-info" data-bs-slide="next">
                    <span class="carousel-control-next-icon"
                        aria-hidden="true"></span>
                    <span class="visually-hidden">Next</span>
                </button>
            </div>
        </div>
        <div class="col-3"></div>
    </div>
</div>

<script src="/js/jquery.min.js"></script>
<script src="/js/bootstrap.bundle.min.js"></script>
</body>
</html>
