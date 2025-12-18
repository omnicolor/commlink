# Changelog

All notable changes to this project will be documented in this file.

The format is based on [Keep a Changelog](https://keepachangelog.com/en/1.1.0/),
and this project adheres to
[Semantic Versioning](https://semver.org/spec/v2.0.0.html).

## [1.13.2] - 2025-11-29

Mostly just developer quality-of-life improvements and updating dependencies.

### Changed

- Updated lots of Composer and NPM dependencies.
- Improved Rector level.

## [1.13.1] - 2025-08-24

### Added

- Added Rector for even more static analysis.
- Added a [security policy](SECURITY.md).

### Changed

- Updated lots of Composer and NPM dependencies.
- Upgraded Docker container to PHP8.4. ([#2379](https://github.com/omnicolor/commlink/pull/2379))
- Set permissions on GitHub actions.

## [1.13.0] - 2025-06-23

### Added

- Added initial timer roll for Slack and Discord. Typing `/roll timer create 10` will set a 10 minute timer, and Commlink will notify you when the timer is complete. ([#2177](https://github.com/omnicolor/commlink/pull/2177), [#2024](https://github.com/omnicolor/commlink/pull/2024))
- Added initial species data for Stillfleet. ([#2179](https://github.com/omnicolor/commlink/pull/2179))
- Added a toString() method to the User model. ([#2306](https://github.com/omnicolor/commlink/pull/2306))
- Added Rector to the Composer and CI, cleaned up a bunch of code based on its output. ([#2371](https://github.com/omnicolor/commlink/pull/2371))

### Changed

- Lots of dependencies updated.
- Updated example .env file to better capture what needs to be set. ([#2125](https://github.com/omnicolor/commlink/pull/2125))
- Cleaned up the [OpenAPI documentation](https://commlink.digitaldarkness.com/openapi/), split it into module files, and fixed some controller and data issues that were making responses inconsistent. ([#2270](https://github.com/omnicolor/commlink/pull/2270))
- Updated to PHP8.4. ([#2199](https://github.com/omnicolor/commlink/pull/2199))
- Lots of PHP cleanups. ([#2307](https://github.com/omnicolor/commlink/pull/2307) - campaign invitation statuses)
- Broke out some value objects and enums. ([#2294](https://github.com/omnicolor/commlink/pull/2294))
- Linkified this changelog.

### Fixed

- Added some missing view directories that Laravel was complaining about. ([#2126](https://github.com/omnicolor/commlink/pull/2126))
- Added Predis as a Composer dependency. ([#2127](https://github.com/omnicolor/commlink/pull/2127))
- Fixed Discord install link on the about page. ([#2164](https://github.com/omnicolor/commlink/pull/2164))
- Verified the OpenAPI document against all GET methods.

### Removed

- Removed Sentry.

## [1.12.0] - 2025-03-08

### Changed

- Lots of dependencies updated.

### Fixed

- Cleaned up some Laravel "issues" brought up by Shift's Laravel Linter: reverted config files to be more like the defaults, removed the $model property for base database factories. ([#2080](https://github.com/omnicolor/commlink/pull/2080))

### Removed

- Removed Slack code from Commlink. ([#2106](https://github.com/omnicolor/commlink/pull/2106)) Code is now its own project at [omnicolor/slack](https://github.com/omnicolor/slack).
- Removed Paratest dependency since it wasn't working correctly some of the time (creating a bunch of numbered files in the cwd and failing tests randomly). ([#2123](https://github.com/omnicolor/commlink/pull/2123))


## [1.11.0] - 2025-02-20

### Added

- Added middleware to default API requests to application/json. ([#2052](https://github.com/omnicolor/commlink/pull/2052))
- Added a generic characters route. ([#2063](https://github.com/omnicolor/commlink/pull/2063))

### Changed

- Finally updated the Mongo dependency. ([#2062](https://github.com/omnicolor/commlink/pull/2062))
- Lots of dependencies updated, including at least one security vulnerability.


## [1.10.0] - 2025-02-11

### Added

- Added initial Avatar Legends character sheet. ([#1881](https://github.com/omnicolor/commlink/pull/1881))

### Fixed

- Fixed a missing semicolon in the migration stub. ([#2023](https://github.com/omnicolor/commlink/pull/2023))
- Added some missing system images for user admin page. ([#2040](https://github.com/omnicolor/commlink/pull/2040))
- Auto-expire API tokens when sent to insecure resources. ([#2045](https://github.com/omnicolor/commlink/pull/2045))

### Changed

- Lots of dependencies updated.
- Most enums are now in their own directory instead of being in the models directory. ([#2037](https://github.com/omnicolor/commlink/pull/2037))
- Factored the ForceTrait out from Shadowrun 5E to be a reusable math component since it's also used in dice rolls for other systems. ([#2038](https://github.com/omnicolor/commlink/pull/2038))


## [1.9.0] - 2025-02-03

### Added

- Created an initial [Bruno](https://www.usebruno.com/) collection for interacting with Commlink's API. ([#1918](https://github.com/omnicolor/commlink/pull/1918))
- Added Override attribute where it's appropriate. ([#1943](https://github.com/omnicolor/commlink/pull/1943))
- Added ability to delete a chat user. ([#2015](https://github.com/omnicolor/commlink/pull/2015))
- Added menu option for user administration. ([#2015](https://github.com/omnicolor/commlink/pull/2015))

### Fixed

- Cleaned up console commands. ([#1939](https://github.com/omnicolor/commlink/pull/1939))
- Cleaned up system configuration. ([#1919](https://github.com/omnicolor/commlink/pull/1919))
- Stopped some tests from trying to make network connections. ([#1940](https://github.com/omnicolor/commlink/pull/1940))
- Fixed redirect after creating a new campaign. It used to go to the dashboard,now it goes to the page for the newly created campaign. ([#2011](https://github.com/omnicolor/commlink/pull/2011) Closes issue [#1995](https://github.com/omnicolor/commlink/pull/1995). Thanks to new contributor[developerluanramos](https://github.com/developerluanramos)!
- Import all global functions. ([#2021](https://github.com/omnicolor/commlink/pull/2021, [#1802](https://github.com/omnicolor/commlink/pull/1802))

### Changed

- Lots of dependencies updated.
- Changed from using [jerodev/php-irc-client](https://github.com/jerodev/php-irc-client) to using my fork at [omnicolor/php-irc-client](https://github.com/omnicolor/php-irc-client). ([#1962](https://github.com/omnicolor/commlink/pull/1962))
- Refactored the settings page from the two or three accordion layout (depending on your permissions) to different pages. ([#2015](https://github.com/omnicolor/commlink/pull/2015))


## [1.8.0] - 2024-12-27

Mostly backend updates, specifically cleaning up PHPStan ignores and removing
Psalm. Fixed up a lot of the resource doc blocks to make them more explicit.

### Added

- Initial support for Root RPG. ([#1757](https://github.com/omnicolor/commlink/pull/1757))
- Add support for Stoplight Prism. ([#1814](https://github.com/omnicolor/commlink/pull/1814))

### Fixed

- Fixed link to OpenAPI documentation. ([#1746](https://github.com/omnicolor/commlink/pull/1746))
- Downgraded and pinned [nwidart/laravel-modules](https://laravelmodules.com/) since the new version wasn't working. ([#1756](https://github.com/omnicolor/commlink/pull/1756))

### Changed

- Updated so many dependencies.
- Cleaned up a whole lot of PHPStan ignores, getting ready for [PHPStan 2.0](https://phpstan.org). ([#1816](https://github.com/omnicolor/commlink/pull/1816)) Upgraded to PHPStan 2.0. ([#1899](https://github.com/omnicolor/commlink/pull/1899))
- Cleaned up a bunch of the docblocks for API resources and added a few new ones. ([#1916](https://github.com/omnicolor/commlink/pull/1916))

### Removed

- Removed support for Psalm. ([#1896](https://github.com/omnicolor/commlink/pull/1896))


## [1.7.0] - 2024-10-29

Completely inconsequential update from the user's perspective, with only
upstream dependencies changed. From an admin or developer of the Commlink
system, the addition of [Laravel Pulse](https://laravel.com/docs/11.x/pulse)
and [Laravel Telescope](https://laravel.com/docs/11.x/telescope) should give
more insights into how the system is running. Adding Docker support is a pretty
big win for running Commlink on my non-production hardware.

### Added

- Added Docker setup for Commlink so developers can run things on laptops. ([#1565](https://github.com/omnicolor/commlink/pull/1565), [#1724](https://github.com/omnicolor/commlink/pull/1724))
- Added option to add a role to a user created via the CreateUser script. ([#1566](https://github.com/omnicolor/commlink/pull/1566))
- Added command to create a roll for a specific module via module:make-roll. ([#1567](https://github.com/omnicolor/commlink/pull/1567))
- Added Laravel Pulse dashboard. ([#1571](https://github.com/omnicolor/commlink/pull/1571))
- Added Laravel Telescope. ([#1592](https://github.com/omnicolor/commlink/pull/1592))

### Fixed

- Updated the stub file for creating rolls through make:roll. ([#1567](https://github.com/omnicolor/commlink/pull/1567))
- Restore the GM screen. ([#1572](https://github.com/omnicolor/commlink/pull/1572), [#1515](https://github.com/omnicolor/commlink/pull/1515))
- Fixed editorconfig to have correct indentation for YAML files. ([#1608](https://github.com/omnicolor/commlink/pull/1608))

### Changed

- Updated so many dependencies.
- Move data file for Chummer5 paths into the import command. ([#1561](https://github.com/omnicolor/commlink/pull/1561))


## [1.6.0] - 2024-08-02

### Added

- Re-added Sentry monitoring. ([#1514](https://github.com/omnicolor/commlink/pull/1514))
- Initial support for rolling dice from a web interface. Skills on the Alien character sheet are now clickable, causing a roll event to happen. ([#1521](https://github.com/omnicolor/commlink/pull/1521))
- Add dice rollers for some RPG systems:
    - Blister Critters ([#1522](https://github.com/omnicolor/commlink/pull/1522))
    - Shadowrun Anarchy ([#1527](https://github.com/omnicolor/commlink/pull/1527))
    - Avatar Legends ([#1532](https://github.com/omnicolor/commlink/pull/1532))
    - Legend of the Five Rings ([#1531](https://github.com/omnicolor/commlink/pull/1531))

### Fixed

- Fixed Slack configuration. ([#1517](https://github.com/omnicolor/commlink/pull/1517))
- Fixed rendering of partial Avatar campaigns, giving default experience. ([#1520](https://github.com/omnicolor/commlink/pull/1520))
- Fix trying to create a character for an RPG that hasn't (yet) defined a character class. This basically only happens in factories generating a random character, but was causing tests to fail. ([#1530](https://github.com/omnicolor/commlink/pull/1530))

### Changed

- Lots of dependencies updated.
- Finished modularizing. All of the remaining resources, rules, etc are now in their module's namespace instead of the root namespace. ([#1512](https://github.com/omnicolor/commlink/pull/1512), [#1368](https://github.com/omnicolor/commlink/pull/1368))
- Added php-cs-fixer rule to bar PHPdoc tags that are redundant according to type hints. ([#1525](https://github.com/omnicolor/commlink/pull/1525))
- Improved Psalm support to level 4. ([#1526](https://github.com/omnicolor/commlink/pull/1526))


## [1.5.0] - 2024-07-24

### Added

- Alien RPG: Character generation, character sheet, initial dice roller, and APIs. ([#1459](https://github.com/omnicolor/commlink/pull/1459))
- Stillfleet RPG. ([#1094](https://github.com/omnicolor/commlink/pull/1094))
- Subversion RPG. ([#1282](https://github.com/omnicolor/commlink/pull/1282))
- Improved test coverage. ([#1452](https://github.com/omnicolor/commlink/pull/1452))
- Add README instructions for [running the IRC bot](https://github.com/omnicolor/commlink?tab=readme-ov-file#running-the-irc-bot). ([#1448](https://github.com/omnicolor/commlink/pull/1448))
- Add Blister Critters module as a stub. ([#1429](https://github.com/omnicolor/commlink/pull/1429))
- Add some new static analysis dependencies:
    - phpstan-phpunit ([#1426](https://github.com/omnicolor/commlink/pull/1426))
    - phpstan-deprecation ([#1428](https://github.com/omnicolor/commlink/pull/1428))
- World Anvil import for Cyberpunk Red and Expanse characters. ([#886](https://github.com/omnicolor/commlink/pull/886))

### Changed

- Lots of Composer and NPM dependencies updated.
- Updated a lot of Psalm annotations. ([#1467](https://github.com/omnicolor/commlink/pull/1467))
- RPG systems are now implemented as modules instead of alongside each other in the app directory. For example instead of having subdirectories for each RPG under the Roll directory, each RPG's module now has a Roll directory with its dice rollers. ([#1429](https://github.com/omnicolor/commlink/pull/1429))
- Standardized PHPUnit group annotations.

### Removed

- Building unused artifacts for code coverage in CI pipeline. ([#1469](https://github.com/omnicolor/commlink/pull/1469))
- Killed a bunch of unused @psalm-suppress annotations. ([#1468](https://github.com/omnicolor/commlink/pull/1468), [#1467](https://github.com/omnicolor/commlink/pull/1467))


## [1.4.0] - 2024-06-21

### Added

- Started using Captainhook to automatically run all of the required steps on commit as well as `composer install` when `composer.json` or `composer.lock` are touched. ([#1290](https://github.com/omnicolor/commlink/pull/1290))
- Add `apis.json` for API findability. ([#1270](https://github.com/omnicolor/commlink/pull/1270))
- Add command for importing Shadowrun 5th Edition data files from [Chummer 5](https://github.com/chummer5a/chummer5a). ([#216](https://github.com/omnicolor/commlink/pull/216))
- Add support for Shadowrun 5E resonance echoes. ([#1117](https://github.com/omnicolor/commlink/pull/1117))
- Add basic support for rolling dice in IRC channels. ([#13](https://github.com/omnicolor/commlink/pull/13))
- A few basic D&D 5E data files. ([#1061](https://github.com/omnicolor/commlink/pull/1061))
- Added Hero Lab import for Shadowrun 5E portfolios. ([#37](https://github.com/omnicolor/commlink/pull/37))

### Fixed

- Hero Lab integration now cleans up the temporary files it creates. ([#1285](https://github.com/omnicolor/commlink/pull/1285))
- Fixed the campaign date setter. ([#1113](https://github.com/omnicolor/commlink/pull/1113))
- When a user tries to create an API key, we now actually show it to theminstead of making them guess. ([#1087(https://github.com/omnicolor/commlink/pull/1087)])
- API documentation cleanup. ([#1090](https://github.com/omnicolor/commlink/pull/1090), [#1089](https://github.com/omnicolor/commlink/pull/1089), [#1088](https://github.com/omnicolor/commlink/pull/1088), [#1084](https://github.com/omnicolor/commlink/pull/1084))

### Changed

- Lots of Composer and NPM dependencies updated.
- Parallelize PHP-cs-fixer. ([#1371](https://github.com/omnicolor/commlink/pull/1371))
- Ran rector with default rules and cleaned up a bunch of issues it brought up. Probably not going to use it long-term though. ([#1369](https://github.com/omnicolor/commlink/pull/1369))
- Upgraded PHPUnit to v11. This required changing a ton of work, including changing from annotations to attributes, removing redundant annotations, changing from `createStub()` to `createMock()` when the test doubles have expectations, and removing some psalm plugins that don't play well. ([#1363](https://github.com/omnicolor/commlink/pull/1363))
- Upgraded Laravel to v11. ([#1362](https://github.com/omnicolor/commlink/pull/1362))
- Changed to running the test suite in parallel. Drops the build time from 3:31 to 0:54 on my system. ([#1280](https://github.com/omnicolor/commlink/pull/1280))
- Upgraded to PHP 8.3. ([#1060](https://github.com/omnicolor/commlink/pull/1060))

### Removed

- Removed PHP magic number detector. I haven't run it in a long time and have gotten better at avoiding magic numbers. In addition, it was blocking the upgrade to Laravel v11. ([#1362](https://github.com/omnicolor/commlink/pull/1362))


## [1.3.0] - 2024-02-10

### Added

- Campaign invitations: A GM can now invite players to their table. ([#1055](https://github.com/omnicolor/commlink/pull/1055))
- Add install instructions for Slack and Discord. ([#996](https://github.com/omnicolor/commlink/pull/996))
- Events: A GM or player may now create an event attached to a campaign and handle RSVPs for it in the web app or dice rollers. ([#878](https://github.com/omnicolor/commlink/pull/878))
- Add PHP 8.3 to GitHub action runner. ([#883](https://github.com/omnicolor/commlink/pull/883))

### Changed

- Lots of Composer and NPM dependencies updated.
- Improved the password reset experience. ([#1051](https://github.com/omnicolor/commlink/pull/1051))

### Removed

- Unneeded @psalm-suppression annotations. ([#1057](https://github.com/omnicolor/commlink/pull/1057))
- Invalid PHPUnit @group annotations. ([#1056](https://github.com/omnicolor/commlink/pull/1056))


## [1.2.0] - 2023-10-28

### Added

- Add Shadowrun 5E dice probabilities, showing the percentage chance of that many successes for the given number of dice. ([#650](https://github.com/omnicolor/commlink/pull/650))
- Add [Redocly](https://redocly.com/) for OpenAPI documentation. ([#567](https://github.com/omnicolor/commlink/pull/567))

### Fixed

- Fixed some flaky tests.

### Changed

- Lots of Composer and NPM dependencies updated.
- Dice rolls are now handled by a Dice Service, which allows us to mock out dice rolls for testing and replace the PHPMock package we were using. ([#530](https://github.com/omnicolor/commlink/pull/530))
- Update from [socialiteproviders/slack](https://github.com/socialiteproviders/slack) to [laravel/socialite](https://github.com/laravel/socialite).

### Removed

- Removed Sentry. ([#667](https://github.com/omnicolor/commlink/pull/667))


## [1.1.0] - 2023-08-11

### Added

- Initial support for Subversion RPG. ([#123](https://github.com/omnicolor/commlink/pull/123))


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
