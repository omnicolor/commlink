# Commlink

Commlink is a manager for characters and campaigns for various table top role
playing games. Originally written as a character builder for Shadowrun 5E, it
later expanded to a GM tool allowing the game master to keep details about the
campaign together in one place.

## Building Commlink

Commlink requires [Composer](https://getcomposer.org) and is built on Laravel.
Assuming you have composer in your path:

```shell
$ composer install
$ ./artisan migrate
$ ./artisan serve
```

Much of data powering the API is proprietary and requires a licensing
relationship with the various owners of the intellectual property, so it is not
included with the project. Creating your own data files to return data from the
API involves filling out the various PHP arrays in the `data` directory.

## Running tests

Running unit tests is done through Composer:

```shell
$ composer test
```

## Favicon credit

https://commons.wikimedia.org/wiki/File:Sixsided_Dice_inJapan.jpg
