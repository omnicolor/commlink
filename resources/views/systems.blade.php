<x-app>
    <x-slot name="title">Supported systems</x-slot>
    <x-slot name="navbar">
        <li class="nav-item">
            <a class="nav-link" href="{{ route('dashboard') }}">Home</a>
        </li>
        <li class="nav-item">
            <a class="nav-link" href="{{ route('about') }}">About</a>
        </li>
        <li class="nav-item">
            <span class="nav-link active">RPG systems</span>
        </li>
    </x-slot>
    <div class="mx-4 px-4">
        <h1 class="mt-4">Supported systems</h1>

        <p>
            {{ config('app.name') }} is a work in progress and is expanding
            both the number of supported RPG systems as well as the depth of
            support for already supported systems. We consider partial support
            for a system to mean at a minimum that you can register a chat
            channel for the system and roll dice appropriate to the system in
            that channel. For a system to be fully supported, it needs
            a variety of features, shown in order of importance:
        </p>

        <ul>
            <li>
                Basic dice roller &mdash; Different RPGs use different
                mechanics for handling the randomness that makes RPGs fun.
                A basic dice roller means if you have your pencil and paper
                character sheets but want to play online you can interact with
                the chat bot to roll dice as appropriate for the system. Dice,
                in this case, doesn't always mean dice. For example, the Capers
                RPG uses a deck of cards. For dice systems, it will roll the
                correct size of dice and calculate exploding dice or sums as
                appropriate. See an individual system's documentation for how
                the system's mechanics work.
            </li>
            <li>
                Character sheet &mdash; Every RPG out there has a different
                looking character sheet. We can't really claim to support
                a system if you can't view a character from the system.
            </li>
            <li>
                Character generator &mdash; A character sheet is pretty neat,
                but how do you get a character into the system? Different role
                playing systems have radically different complexities in how
                characters are created.
            </li>
            <li>
                Campaigns &mdash; A campaign is a generic name for the
                collection of player characters, non-player characters,
                players, and metadata about a game that is meant for more than
                a one-shot.
            </li>
            <li>
                Initiative tracker &mdash; RPGs use different methods to track
                who goes when in combat or other time-sensitive events. The
                initiative tracker simplifies this chore for GMs.
            </li>
            <li>
                Advanced dice roller &mdash; If you've got a character stored
                in the system, why should you have to look up the attributes to
                add together and type <code>/roll 11</code> for your Shadowrun
                5E character's memory test? The advanced dice roller lets the
                bot know about your character's statistics so you can just type
                <code>/roll memory</code> and it will add your character's
                logic and willpower together and roll that many dice for you.
            </li>
            <li>
                Advanced character sheet &mdash; Improve on both the dice
                roller and character sheet by making rolls happen by clicking
                things on your character sheet.
            </li>
            <li>
                GM screen &mdash; Keeping track of everything can be difficult
                for a seasoned GM. A system's GM screen is a set of
                customizable widgets to keep player data, initiative, NPCs, and
                the calendar front and center.
            </li>
        </ul>

        <table class="table">
            <thead>
                <tr>
                    <th scope="col">System name</th>
                    <th scope="col" class="text-center">Roller</th>
                    <th scope="col" class="text-center">Sheet</th>
                    <th scope="col" class="text-center">Char.Gen</th>
                    <th scope="col" class="text-center">Initiative</th>
                    <th scope="col" class="text-center">Adv.Roller</th>
                    <th scope="col" class="text-center">Adv.Sheet</th>
                    <th scope="col" class="text-center">GM screen</th>
                </tr>
            </thead>
            <tbody>
                <tr>
                    <td>
                        Alien
                        <a href="https://freeleaguepublishing.com/games/alien/"><i class="bi bi-box-arrow-up-right"></i></a>
                    </td>
                    <td class="text-center"><span class="badge rounded-pill text-bg-success"><i class="bi bi-check"></i></span></td>
                    <td class="text-center"><span class="badge rounded-pill text-bg-success"><i class="bi bi-check"></i></span></td>
                    <td class="text-center"><span class="badge rounded-pill text-bg-success"><i class="bi bi-check"></i></span></td>
                    <td class="text-center"><span class="badge rounded-pill text-bg-warning"><i class="bi bi-question"></i></span></td>
                    <td class="text-center"><span class="badge rounded-pill text-bg-danger"><i class="bi bi-x"></i></span></td>
                    <td class="text-center"><span class="badge rounded-pill text-bg-warning"><i class="bi bi-question"></i></span></td>
                    <td class="text-center"><span class="badge rounded-pill text-bg-danger"><i class="bi bi-x"></i></span></td>
                </tr>
                <tr>
                    <td>
                        Avatar Legends
                        <a href="https://magpiegames.com/pages/avatar-legends"><i class="bi bi-box-arrow-up-right"></i></a>
                    </td>
                    <td class="text-center"><span class="badge rounded-pill text-bg-success"><i class="bi bi-check"></i></span></td>
                    <td class="text-center"><span class="badge rounded-pill text-bg-danger"><i class="bi bi-x"></i></span></td>
                    <td class="text-center"><span class="badge rounded-pill text-bg-danger"><i class="bi bi-x"></i></span></td>
                    <td class="text-center"><span class="badge rounded-pill text-bg-danger"><i class="bi bi-x"></i></span></td>
                    <td class="text-center"><span class="badge rounded-pill text-bg-danger"><i class="bi bi-x"></i></span></td>
                    <td class="text-center"><span class="badge rounded-pill text-bg-danger"><i class="bi bi-x"></i></span></td>
                    <td class="text-center"><span class="badge rounded-pill text-bg-danger"><i class="bi bi-x"></i></span></td>
                </tr>
                <tr>
                    <td>
                        Blister Critters
                        <a href="https://stillfleet.com/games/blister_critters/"><i class="bi bi-box-arrow-up-right"></i></a>
                    </td>
                    <td class="text-center"><span class="badge rounded-pill text-bg-success"><i class="bi bi-check"></i></span></td>
                    <td class="text-center"><span class="badge rounded-pill text-bg-danger"><i class="bi bi-x"></i></span></td>
                    <td class="text-center"><span class="badge rounded-pill text-bg-danger"><i class="bi bi-x"></i></span></td>
                    <td class="text-center"><span class="badge rounded-pill text-bg-danger"><i class="bi bi-x"></i></span></td>
                    <td class="text-center"><span class="badge rounded-pill text-bg-danger"><i class="bi bi-x"></i></span></td>
                    <td class="text-center"><span class="badge rounded-pill text-bg-danger"><i class="bi bi-x"></i></span></td>
                    <td class="text-center"><span class="badge rounded-pill text-bg-danger"><i class="bi bi-x"></i></span></td>
                </tr>
                <tr>
                    <td>
                        Capers
                        <a href="https://www.nerdburgergames.com/capers"><i class="bi bi-box-arrow-up-right"></i></a>
                    </td>
                    <td class="text-center"><span class="badge rounded-pill text-bg-success"><i class="bi bi-check"></i></span></td>
                    <td class="text-center"><span class="badge rounded-pill text-bg-success"><i class="bi bi-check"></i></span></td>
                    <td class="text-center"><span class="badge rounded-pill text-bg-success"><i class="bi bi-check"></i></span></td>
                    <td class="text-center"><span class="badge rounded-pill text-bg-success"><i class="bi bi-check"></i></span></td>
                    <td class="text-center"><span class="badge rounded-pill text-bg-danger"><i class="bi bi-x"></i></span></td>
                    <td class="text-center"><span class="badge rounded-pill text-bg-danger"><i class="bi bi-x"></i></span></td>
                    <td class="text-center"><span class="badge rounded-pill text-bg-danger"><i class="bi bi-x"></i></span></td>
                </tr>
                <tr>
                    <td>
                        Cyberpunk Red
                        <a href="https://rtalsoriangames.com/cyberpunk/"><i class="bi bi-box-arrow-up-right"></i></a>
                    </td>
                    <td class="text-center"><span class="badge rounded-pill text-bg-success"><i class="bi bi-check"></i></span></td>
                    <td class="text-center"><span class="badge rounded-pill text-bg-success"><i class="bi bi-check"></i></span></td>
                    <td class="text-center"><span class="badge rounded-pill text-bg-warning"><i class="bi bi-question"></i></span></td>
                    <td class="text-center"><span class="badge rounded-pill text-bg-danger"><i class="bi bi-x"></i></span></td>
                    <td class="text-center"><span class="badge rounded-pill text-bg-danger"><i class="bi bi-x"></i></span></td>
                    <td class="text-center"><span class="badge rounded-pill text-bg-danger"><i class="bi bi-x"></i></span></td>
                    <td class="text-center"><span class="badge rounded-pill text-bg-danger"><i class="bi bi-x"></i></span></td>
                </tr>
                <tr>
                    <td>
                        The Expanse
                        <a href="https://greenroninstore.com/collections/the-expanse-rpg"><i class="bi bi-box-arrow-up-right"></i></a>
                    </td>
                    <td class="text-center"><span class="badge rounded-pill text-bg-success"><i class="bi bi-check"></i></span></td>
                    <td class="text-center"><span class="badge rounded-pill text-bg-success"><i class="bi bi-check"></i></span></td>
                    <td class="text-center"><span class="badge rounded-pill text-bg-warning"><i class="bi bi-question"></i></span></td>
                    <td class="text-center"><span class="badge rounded-pill text-bg-danger"><i class="bi bi-x"></i></span></td>
                    <td class="text-center"><span class="badge rounded-pill text-bg-danger"><i class="bi bi-x"></i></span></td>
                    <td class="text-center"><span class="badge rounded-pill text-bg-danger"><i class="bi bi-x"></i></span></td>
                    <td class="text-center"><span class="badge rounded-pill text-bg-danger"><i class="bi bi-x"></i></span></td>
                </tr>
                <tr>
                    <td>
                        Shadowrun Anarchy
                        <a href="https://www.catalystgamelabs.com/brands/shadowrun"><i class="bi bi-box-arrow-up-right"></i></a>
                    </td>
                    <td class="text-center"><span class="badge rounded-pill text-bg-success"><i class="bi bi-check"></i></span></td>
                    <td class="text-center"><span class="badge rounded-pill text-bg-danger"><i class="bi bi-x"></i></span></td>
                    <td class="text-center"><span class="badge rounded-pill text-bg-danger"><i class="bi bi-x"></i></span></td>
                    <td class="text-center"><span class="badge rounded-pill text-bg-danger"><i class="bi bi-x"></i></span></td>
                    <td class="text-center"><span class="badge rounded-pill text-bg-danger"><i class="bi bi-x"></i></span></td>
                    <td class="text-center"><span class="badge rounded-pill text-bg-danger"><i class="bi bi-x"></i></span></td>
                    <td class="text-center"><span class="badge rounded-pill text-bg-danger"><i class="bi bi-x"></i></span></td>
                </tr>
                <tr>
                    <td>
                        Shadowrun 5th Edition
                        <a href="https://www.catalystgamelabs.com/brands/shadowrun"><i class="bi bi-box-arrow-up-right"></i></a>
                    </td>
                    <td class="text-center"><span class="badge rounded-pill text-bg-success"><i class="bi bi-check"></i></span></td>
                    <td class="text-center"><span class="badge rounded-pill text-bg-success"><i class="bi bi-check"></i></span></td>
                    <td class="text-center"><span class="badge rounded-pill text-bg-warning"><i class="bi bi-question"></i></span></td>
                    <td class="text-center"><span class="badge rounded-pill text-bg-warning"><i class="bi bi-question"></i></span></td>
                    <td class="text-center"><span class="badge rounded-pill text-bg-warning"><i class="bi bi-question"></i></span></td>
                    <td class="text-center"><span class="badge rounded-pill text-bg-danger"><i class="bi bi-x"></i></span></td>
                    <td class="text-center"><span class="badge rounded-pill text-bg-warning"><i class="bi bi-question"></i></span></td>
                </tr>
                <tr>
                    <td>
                        Star Trek Adventures
                        <a href="https://www.modiphius.net/collections/star-trek-adventures/star-trek_core"><i class="bi bi-box-arrow-up-right"></i></a>
                    </td>
                    <td class="text-center"><span class="badge rounded-pill text-bg-success"><i class="bi bi-check"></i></span></td>
                    <td class="text-center"><span class="badge rounded-pill text-bg-success"><i class="bi bi-check"></i></span></td>
                    <td class="text-center"><span class="badge rounded-pill text-bg-danger"><i class="bi bi-x"></i></span></td>
                    <td class="text-center"><span class="badge rounded-pill text-bg-danger"><i class="bi bi-x"></i></span></td>
                    <td class="text-center"><span class="badge rounded-pill text-bg-danger"><i class="bi bi-x"></i></span></td>
                    <td class="text-center"><span class="badge rounded-pill text-bg-danger"><i class="bi bi-x"></i></span></td>
                    <td class="text-center"><span class="badge rounded-pill text-bg-danger"><i class="bi bi-x"></i></span></td>
                </tr>
                <tr>
                    <td>
                        Stillfleet
                        <a href="https://stillfleet.com/games/stillfleet/"><i class="bi bi-box-arrow-up-right"></i></a>
                    </td>
                    <td class="text-center"><span class="badge rounded-pill text-bg-success"><i class="bi bi-check"></i></span></td>
                    <td class="text-center"><span class="badge rounded-pill text-bg-warning"><i class="bi bi-question"></i></span></td>
                    <td class="text-center"><span class="badge rounded-pill text-bg-warning"><i class="bi bi-question"></i></span></td>
                    <td class="text-center"><span class="badge rounded-pill text-bg-danger"><i class="bi bi-x"></i></span></td>
                    <td class="text-center"><span class="badge rounded-pill text-bg-danger"><i class="bi bi-x"></i></span></td>
                    <td class="text-center"><span class="badge rounded-pill text-bg-danger"><i class="bi bi-x"></i></span></td>
                    <td class="text-center"><span class="badge rounded-pill text-bg-danger"><i class="bi bi-x"></i></span></td>
                </tr>
                <tr>
                    <td>
                        Subversion
                        <a href="https://www.fraggingunicorns.com/subversion"><i class="bi bi-box-arrow-up-right"></i></a>
                    </td>
                    <td class="text-center"><span class="badge rounded-pill text-bg-success"><i class="bi bi-check"></i></span></td>
                    <td class="text-center"><span class="badge rounded-pill text-bg-warning"><i class="bi bi-question"></i></span></td>
                    <td class="text-center"><span class="badge rounded-pill text-bg-warning"><i class="bi bi-question"></i></span></td>
                    <td class="text-center"><span class="badge rounded-pill text-bg-danger"><i class="bi bi-x"></i></span></td>
                    <td class="text-center"><span class="badge rounded-pill text-bg-danger"><i class="bi bi-x"></i></span></td>
                    <td class="text-center"><span class="badge rounded-pill text-bg-danger"><i class="bi bi-x"></i></span></td>
                    <td class="text-center"><span class="badge rounded-pill text-bg-danger"><i class="bi bi-x"></i></span></td>
                </tr>
                <tr>
                    <td>
                        The Transformers RPG 2nd Edition
                        <a href="https://rpggeek.com/image/3884438/the-transformers-rpg-2nd-edition"><i class="bi bi-box-arrow-up-right"></i></a>
                    </td>
                    <td class="text-center"><span class="badge rounded-pill text-bg-success"><i class="bi bi-check"></i></span></td>
                    <td class="text-center"><span class="badge rounded-pill text-bg-warning"><i class="bi bi-question"></i></span></td>
                    <td class="text-center"><span class="badge rounded-pill text-bg-warning"><i class="bi bi-question"></i></span></td>
                    <td class="text-center"><span class="badge rounded-pill text-bg-danger"><i class="bi bi-x"></i></span></td>
                    <td class="text-center"><span class="badge rounded-pill text-bg-danger"><i class="bi bi-x"></i></span></td>
                    <td class="text-center"><span class="badge rounded-pill text-bg-danger"><i class="bi bi-x"></i></span></td>
                    <td class="text-center"><span class="badge rounded-pill text-bg-danger"><i class="bi bi-x"></i></span></td>
                </tr>
            </tbody>
            <tfoot>
                <tr>
                    <td class="text-muted" colspan=8>All role playing systems are property of their copyright owners.</td>
                </tr>
            </tfoot>
        </table>
    </div>
</x-app>
