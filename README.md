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

The Discord bot currently relies on a legacy Discord library that doesn't play
nice with newer versions of some other libraries. In order to run it:

```shell
$ composer remove pusher/pusher-php-server
$ composer require -W guzzlehttp/guzzle ^6
$ ./artisan discord:run
```

Running it loads all of the important things into memory, so it will continue to
run there until you stop it. In another shell, get your environment back to
normal:

```shell
$ git checkout -- composer.json composer.lock
$ composer install
```

**Note:** If you're using Pusher and have have configured it in your `.env`
file, you'll need to temporarily set that to null before running the Composer
commands to set up for running the Discord bot.

## Running tests

Running unit tests is done through Composer:

```shell
$ composer test
```

# Credits

* Favicon: https://commons.wikimedia.org/wiki/File:Sixsided_Dice_inJapan.jpg
