# Commlink

Commlink is a manager for characters and campaigns for various table top role
playing games. Originally written as a character builder for Shadowrun 5E, it
later expanded to a GM tool allowing the game master to keep details about the
campaign together in one place.

Commlink can operate either as a standalone web site for building and
maintaining characters, or it can integrate with chat services to operate as
a dice roller. It can be used in Slack, Discord, and/or IRC. If a campaign is
registered to multiple chat channels, rolls can be broadcast to other channels.
For example, if your Shadowrun campaign is using both Slack and Discord, users
can roll dice in Slack and have the results appear in Discord, and vice versa.

## Supported systems

Commlink is very much a work in progress, and each of the systems has varying
levels of completeness.

* [Alien](https://freeleaguepublishing.com/games/alien/)
* [Avatar Legends](https://magpiegames.com/pages/avatar-legends)
* [Blister Critters](https://stillfleet.com/games/blister_critters/)
* [Capers](https://www.nerdburgergames.com/capers)
* [Cyberpunk Red](https://rtalsoriangames.com/cyberpunk/)
* [Dungeons & Dragons 5th Edition](https://dnd.wizards.com/)
* [The Expanse](https://greenroninstore.com/collections/the-expanse-rpg)
* [Shadowrun 5th Edition](https://www.catalystgamelabs.com/brands/shadowrun)
* [Shadowrun 6th Edition](https://www.catalystgamelabs.com/brands/shadowrun)
* [Star Trek Adventures](https://www.modiphius.net/collections/star-trek-adventures/star-trek_core)
* [Stillfleet](https://stillfleet.com/games/stillfleet/)
* [Subversion](https://www.fraggingunicorns.com/subversion)
* [The Transformers RPG 2nd Edition](https://rpggeek.com/image/3884438/the-transformers-rpg-2nd-edition)

## Building Commlink

Commlink requires [Composer](https://getcomposer.org) and is built on
[Laravel](https://laravel.com). Assuming you have Composer in your path:

```shell
$ composer install
$ ./artisan migrate
```

Much of data powering the API is proprietary and requires a licensing
relationship with the various owners of the intellectual property, so it is not
included with the project. Creating your own data files to return data from the
API involves filling out the various PHP arrays in the `data` directory of the
RPG's module. We've included example data only for supported systems.

Commlink requires both MySQL (for general application data) and MongoDB (for
characters).

## Starting the queue

Passing events between various chat systems and the web requires running
Laravel's queues. Start them with:

```shell
$ ./artisan queue:work
```

## Running the Discord bot

Assuming you've registered the bot with Discord and configured its token:

```shell
$ ./artisan commlink:discord-run
```

## Running the IRC bot

By default, the IRC bot will try to name itself whatever `APP_NAME` is set to
in your .env file. You can pass a new nickname with the `--nickname` parameter.
Similarly, by default it will connect to the standard IRC port 6667, but you
can change it with the `--port` parameter. The server's name is required, and
might be something like chat.freenode.net for Freenode. Finally, you can have
it auto-join channels by passing one or more `--channel` arguments.

```shell
$ ./artisan commlink:irc-run [server]
```

## Running tests and static analysis

All of the automated tests can be run through composer:

```shell
$ composer all
```

If you'd like to run an individual check:
* `coverage` - Build a [PHPUnit](https://phpunit.readthedocs.io/) code coverage
    report that will be available in "public/coverage" or on your site at
    "<host>/coverage/index.html".
* `infection` - Run [Infection](https://infection.github.io/) on the App
    directory.
* `lint` - Run
    [PHP-Parallel-Lint](https://github.com/php-parallel-lint/PHP-Parallel-Lint)
    across the entire codebase.
* `lint-openapi` - Run [Redocly](https://redocly.com/docs/cli/) to lint the
    OpenAPI specification.
* `phpcs` - Run [PHP Code Sniffer](https://github.com/squizlabs/PHP_CodeSniffer)
    on the entire codebase.
* `php-cs-fixer` - Run
    [PHP-CS-Fixer](https://github.com/PHP-CS-Fixer/PHP-CS-Fixer)'s dry run on
    the entire codebase.
* `phpstan` - Run [PHPstan](https://phpstan.org/)'s highest level across the
    entire codebase.
* `psalm` - Run [Psalm](https://psalm.dev/) across the entire codebase.
* `static` - Run lint, PHPStan, and Psalm targets.
* `style` - Run both phpcs and php-cs-fixer.
* `test` - Run PHPUnit tests without generating a code coverage report.

# Credits

* Favicon: https://commons.wikimedia.org/wiki/File:Sixsided_Dice_inJapan.jpg
* All of the game systems supported are trademarked by their respective owners,
  and Commlink is not affiliated with any of them in any way.
