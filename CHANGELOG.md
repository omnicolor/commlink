# Changelog

All notable changes to this project will be documented in this file.

The format is based on [Keep a Changelog](https://keepachangelog.com/en/1.1.0/),
and this project adheres to
[Semantic Versioning](https://semver.org/spec/v2.0.0.html).

## [Unreleased]


## [1.5.0] - 2024-07-24

### Added

- Alien RPG: Character generation, character sheet, initial dice roller, and
    APIs. (#1459)
- Stillfleet RPG. (#1094)
- Subversion RPG. (#1282)
- Improved test coverage. (#1452)
- Add README instructions for running IRC bot. (#1448)
- Add Blister Critters module as a stub. (#1429)
- Add some new static analysis dependencies:
    - phpstan-phpunit (#1426)
    - phpstan-deprecation (#1428)
- World Anvil import for Cyberpunk Red and Expanse characters. (#886)

### Changed

- Lots of Composer and NPM dependencies updated.
- Updated a lot of Psalm annotations. (#1467)
- RPG systems are now implemented as modules instead of alongside each other in
    the app directory. For example instead of having subdirectories for each RPG
    under the Roll directory, each RPG's module now has a Roll directory with
    its dice rollers. (#1429)
- Standardized PHPUnit group annotations.

### Removed

- Building unused artifacts for code coverage in CI pipeline. (#1469)
- Killed a bunch of unused @psalm-suppress annotations. (#1468, #1467)


## [1.4.0] - 2024-06-21

### Added

- Started using Captainhook to automatically run all of the required steps on
    commit as well as `composer install` when `composer.json` or
    `composer.lock` are touched. (#1290)
- Add `apis.json` for API findability. (#1270)
- Add command for importing Shadowrun 5th Edition data files from
    [Chummer 5](https://github.com/chummer5a/chummer5a). (#216)
- Add support for Shadowrun 5E resonance echoes. (#1117)
- Add basic support for rolling dice in IRC channels. (#13)
- A few basic D&D 5E data files. (#1061)
- Added Hero Lab import for Shadowrun 5E portfolios. (#37)

### Fixed

- Hero Lab integration now cleans up the temporary files it creates. (#1285)
- Fixed the campaign date setter. (#1113)
- When a user tries to create an API key, we now actually show it to them
    instead of making them guess. (#1087)
- API documentation cleanup. (#1090, #1089, #1088, #1084)

### Changed

- Lots of Composer and NPM dependencies updated.
- Parallelize PHP-cs-fixer. (#1371)
- Ran rector with default rules and cleaned up a bunch of issues it brought up.
    Probably not going to use it long-term though. (#1369)
- Upgraded PHPUnit to v11. This required changing a ton of work, including
    changing from annotations to attributes, removing redundant annotations,
    changing from `createStub()` to `createMock()` when the test doubles have
    expectations, and removing some psalm plugins that don't play well. (#1363)
- Upgraded Laravel to v11. (#1362)
- Changed to running the test suite in parallel. Drops the build time from 3:31
    to 0:54 on my system. (#1280)
- Upgraded to PHP 8.3. (#1060)

### Removed

- Removed PHP magic number detector. I haven't run it in a long time and have
    gotten better at avoiding magic numbers. In addition, it was blocking the
    upgrade to Laravel v11. (#1362)


## [1.3.0] - 2024-02-10

### Added

- Campaign invitations: A GM can now invite players to their table. (#1055)
- Add install instructions for Slack and Discord. (#996)
- Events: A GM or player may now create an event attached to a campaign and
    handle RSVPs for it in the web app or dice rollers. (#878)
- Add PHP 8.3 to GitHub action runner. (#883)

### Changed

- Lots of Composer and NPM dependencies updated.
- Improved the password reset experience. (#1051)

### Removed

- Unneeded @psalm-suppression annotations. (1057)
- Invalid PHPUnit @group annotations. (#1056)


## [1.2.0] - 2023-10-28

### Added

- Add Shadowrun 5E dice probabilities, showing the percentage chance of that
    many successes for the given number of dice. (#650)
- Add Redocly for OpenAPI documentation. (#567)

### Fixed

- Fixed some flaky tests.

### Changed

- Lots of Composer and NPM dependencies updated.
- Dice rolls are now handled by a Dice Service, which allows us to mock out
    dice rolls for testing and replace the PHPMock package we were using. (#530)
- Update from socialiteproviders/slack to laravel/socialite.

### Removed

- Removed Sentry. (#667)


## [1.1.0] - 2023-08-11

### Added

- Initial support for Subversion RPG. (#123)


## [1.0.0] - 2023-08-11

### Added

- Support for Capers character generation and character sheets
- Support for Cyberpunk Red character sheets
- Support for Expanse character sheets
- Support for Shadowrun 5e character sheets
- Support for Star Trek Adventures character sheets
- Varying levels of support for dice rolling in Slack and Discord:
    * Avatar
    * Capers
    * Cyberpunk Red
    * Expanse
    * Shadowrun 5E
    * Star Trek Adventures
- Campaign support for all supported systems
- GM screen for Shadowrun 5E
