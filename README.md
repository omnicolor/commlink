# Commlink

Commlink is a manager for characters and campaigns for various table top role
playing games. Originally written as a character builder for Shadowrun 5E, it
later expanded to a GM tool allowing the game master to keep details about the
campaign together in one place.

Commlink can operate either as a standalone web site for building and
maintaining characters, or it can integrate with chat services to operate as
a dice roller. It can be used in either Slack or Discord. If a campaign is
registered to multiple chat channels, rolls can be broadcast to other channels.
For example, if your Shadowrun campaign is using both Slack and Discord, users
can roll dice in Slack and have the results appear in Discord, and vice versa.

## Building Commlink

Commlink requires [Composer](https://getcomposer.org) and is built on Laravel.
Assuming you have Composer in your path:

```shell
$ composer install
$ ./artisan migrate
```

Much of data powering the API is proprietary and requires a licensing
relationship with the various owners of the intellectual property, so it is not
included with the project. Creating your own data files to return data from the
API involves filling out the various PHP arrays in the `data` directory. We've
included example data only for supported systems.

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

## Running tests and static analysis

All of the automated tests can be run through composer:

```shell
$ composer all
```

If you'd like to run an individual check:
* coverage - Build a PHPUnit code coverage report that will be available in
    `public/coverage` or on your site at `<host>/coverage/index.html`.
* phpcs - Run PHP Code Sniffer on the entire codebase.
* php-cs-fixer - Run PHP-CS-Fixer's dry run on the entire codebase.
* phpstan - Run PHPstan's highest level across the entire codebase.
* style - Run both phpcs and php-cs-fixer.
* test - Run PHPUnit tests without generating a code coverage report.

# Credits

* Favicon: https://commons.wikimedia.org/wiki/File:Sixsided_Dice_inJapan.jpg
