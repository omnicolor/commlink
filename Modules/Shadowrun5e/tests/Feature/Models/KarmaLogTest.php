<?php

declare(strict_types=1);

namespace Modules\Shadowrun5e\Tests\Feature\Models;

use Modules\Shadowrun5e\Models\ActiveSkill;
use Modules\Shadowrun5e\Models\Augmentation;
use Modules\Shadowrun5e\Models\Character;
use Modules\Shadowrun5e\Models\KarmaLog;
use Modules\Shadowrun5e\Models\KarmaLogEntry;
use Modules\Shadowrun5e\Models\KnowledgeSkill;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\Attributes\Group;
use PHPUnit\Framework\Attributes\Small;
use RuntimeException;
use Tests\TestCase;
use TypeError;
use stdClass;

use function array_filter;
use function array_reduce;
use function json_decode;

#[Group('shadowrun')]
#[Group('shadowrun5e')]
#[Small]
final class KarmaLogTest extends TestCase
{
    /**
     * Subject under test.
     */
    protected KarmaLog $log;

    /**
     * Set up a clean subject under test.
     */
    protected function setUp(): void
    {
        parent::setUp();
        $this->log = new KarmaLog();
    }

    /**
     * Return a boring base character.
     */
    protected function createCharacter(): Character
    {
        return new Character([
            'priorities' => [
                'a' => 'attributes',
                'b' => 'skills',
                'c' => 'resources',
                'd' => 'metatype',
                'e' => 'magic',
                'metatype' => 'human',
                'gameplay' => 'established',
                'magic' => null,
            ],
            'agility' => 3,
            'body' => 3,
            'charisma' => 3,
            'intuition' => 3,
            'logic' => 3,
            'reaction' => 3,
            'strength' => 3,
            'willpower' => 3,
        ]);
    }

    /**
     * Test trying to put a different kind of object into the log throws an
     * exception.
     */
    public function testWrongObjectTypeThrowsException(): void
    {
        self::expectException(TypeError::class);
        self::expectExceptionMessage(
            'KarmaLog only accepts KarmaLogEntry objects'
        );
        // @phpstan-ignore offsetAssign.valueType
        $this->log[] = new stdClass();
    }

    /**
     * Test trying to put a different kind of object into the log doesn't add
     * it.
     */
    public function testWrongObjectTypeDoesntAdd(): void
    {
        try {
            // @phpstan-ignore argument.type
            $this->log->offsetSet(entry: new stdClass());
        } catch (TypeError) {
            // Ignore
        }
        self::assertCount(0, $this->log);
    }

    /**
     * Test count on a new KarmaLog.
     */
    public function testCountNewLog(): void
    {
        self::assertCount(0, $this->log);
    }

    /**
     * Test count on a log with a few entries.
     */
    public function testCountWithEntries(): void
    {
        $this->log[] = new KarmaLogEntry('Test', 5);
        $this->log[] = new KarmaLogEntry('Foo', -5);
        self::assertCount(2, $this->log);
    }

    /**
     * Test iterating across the karma log.
     */
    public function testIterator(): void
    {
        $this->log[] = new KarmaLogEntry('Test', 1);
        $this->log[] = new KarmaLogEntry('Test', 2);
        $this->log[] = new KarmaLogEntry('Test', 3);
        $count = 1;
        foreach ($this->log as $entry) {
            self::assertSame('Test', $entry->description);
            self::assertSame($count, $entry->karma);
            $count++;
        }
    }

    /**
     * Test getKarma on an empty array.
     */
    public function testGetKarmaEmptyArray(): void
    {
        self::assertSame(0, $this->log->getKarma());
    }

    /**
     * Test getKarma() with a non-empty KarmaLog.
     */
    public function testGetKarmaNonEmptyArray(): void
    {
        $this->log[] = new KarmaLogEntry('Test', 1);
        $this->log[] = new KarmaLogEntry('Test', 2);
        $this->log[] = new KarmaLogEntry('Test', 3);
        self::assertSame(6, $this->log->getKarma());
    }

    /**
     * Test countSkillPoints on an empty array.
     */
    public function testCountSkillPointsEmptyArray(): void
    {
        self::assertNull(array_reduce(
            [],
            [KarmaLog::class, 'countSkillPoints']
        ));
    }

    /**
     * Test countSkillPoints with a some Knowledge Skills.
     */
    public function testCountSkillPoints(): void
    {
        $knowledgeSkills = [
            new KnowledgeSkill('Test', 'street', 2),
            new KnowledgeSkill('Foo', 'academic', 3),
        ];
        self::assertSame(
            5,
            array_reduce(
                $knowledgeSkills,
                [KarmaLog::class, 'countSkillPoints']
            )
        );
    }

    /**
     * Test countSkillPoints with a native language skill.
     */
    public function testCountSkillPointsNative(): void
    {
        $knowledgeSkills = [
            new KnowledgeSkill('Test', 'street', 2),
            new KnowledgeSkill('English', 'language', 'N'),
        ];
        self::assertSame(
            2,
            array_reduce(
                $knowledgeSkills,
                [KarmaLog::class, 'countSkillPoints']
            )
        );
    }

    /**
     * Test countSkillPoints with a specialized skill.
     */
    public function testCountSkillPointsSpecialized(): void
    {
        $knowledgeSkills = [
            new KnowledgeSkill('Test', 'street', 2, 'Specialization'),
            new KnowledgeSkill('English', 'language', 'N'),
        ];
        self::assertSame(
            3,
            array_reduce(
                $knowledgeSkills,
                [KarmaLog::class, 'countSkillPoints']
            )
        );
    }

    /**
     * Test filterUnspecialized.
     */
    public function testFilterUnspecialized(): void
    {
        $skills = [
            new ActiveSkill('astral-combat', 6),
            new ActiveSkill('automatics', 4, 'Special'),
            new ActiveSkill('computer', 6),
            new ActiveSkill('hacking', 2),
            new ActiveSkill('hacking', 1, 'Special'),
        ];
        $specializations = array_filter(
            $skills,
            [$this->log, 'filterUnspecialized']
        );
        self::assertCount(2, $specializations);
    }

    /**
     * CompareSkill provider.
     * @return array<int, array<int, callable>>
     */
    public static function skillProvider(): array
    {
        return [
            [
                function (): array {
                    return [
                        new ActiveSkill('hacking', 5),
                        new ActiveSkill('hacking', 4),
                        1,
                    ];
                },
            ],
            [
                function (): array {
                    return [
                        new ActiveSkill('hacking', 1),
                        new ActiveSkill('hacking', 1),
                        0,
                    ];
                },
            ],
            [
                function (): array {
                    return [
                        new ActiveSkill('hacking', 3),
                        new ActiveSkill('hacking', 4),
                        -1,
                    ];
                },
            ],
            [
                function (): array {
                    return [
                        new KnowledgeSkill('foo', 'street', 5),
                        new KnowledgeSkill('foo', 'street', 4),
                        1,
                    ];
                },
            ],
            [
                function (): array {
                    return [
                        new KnowledgeSkill('foo', 'street', 1),
                        new KnowledgeSkill('foo', 'street', 1),
                        0,
                    ];
                },
            ],
            [
                function (): array {
                    return [
                        new KnowledgeSkill('foo', 'street', 4),
                        new KnowledgeSkill('foo', 'street', 5),
                        -1,
                    ];
                },
            ],
            [
                function (): array {
                    return [
                        new KnowledgeSkill('English', 'language', 'N'),
                        new KnowledgeSkill('Spanish', 'language', 'N'),
                        0,
                    ];
                },
            ],
            [
                function (): array {
                    return [
                        new KnowledgeSkill('English', 'language', 5),
                        new KnowledgeSkill('Spanish', 'language', 4),
                        1,
                    ];
                },
            ],
            [
                function (): array {
                    return [
                        new KnowledgeSkill('English', 'language', 1),
                        new KnowledgeSkill('Spanish', 'language', 2),
                        -1,
                    ];
                },
            ],
            [
                function (): array {
                    return [
                        new KnowledgeSkill('English', 'language', 'N'),
                        new KnowledgeSkill('Spanish', 'language', 5),
                        -1,
                    ];
                },
            ],
            [
                function (): array {
                    return [
                        new KnowledgeSkill('English', 'language', 3),
                        new KnowledgeSkill('Spanish', 'language', 'N'),
                        1,
                    ];
                },
            ],
        ];
    }

    /**
     * Test compare with two skills.
     */
    #[DataProvider('skillProvider')]
    public function testCompareSkills(callable $provider): void
    {
        [$skillA, $skillB, $expected] = $provider();
        self::assertSame(
            $expected,
            $this->log->compareSkills($skillA, $skillB)
        );
    }

    /**
     * Test that trying to initialize a Karma Log without priorities being set
     * throws an exception.
     */
    public function testInitializeEmptyCharacter(): void
    {
        self::expectException(RuntimeException::class);
        self::expectExceptionMessage('Priorities not set');
        $this->log->initialize(new Character());
    }

    /**
     * Test trying to initialize a KarmaLog on a character that hasn't gone
     * over in any way.
     */
    public function testInitializeBoringCharacter(): void
    {
        $this->log->initialize($this->createCharacter());
        self::assertSame(25, $this->log->getKarma());
        self::assertCount(1, $this->log);
    }

    /**
     * Test trying to initialize a KarmaLog on a sum-to-ten character that spent
     * too much on attributes.
     */
    public function testInitializeSumToTenCharacter(): void
    {
        $character = $this->createCharacter();
        $character->priorities = [
            'metatype' => 'human',
            'metatypePriority' => 'E',
            'magicPriority' => 'C',
            'attributePriority' => 'E',
            'skillPriority' => 'A',
            'resourcePriority' => 'A',
            'magic' => 'technomancer',
            'gameplay' => 'established',
        ];
        $this->log->initialize($character);
        self::assertSame(-25, $this->log->getKarma());
        self::assertCount(5, $this->log);
        /** @var KarmaLogEntry */
        $entry = $this->log[1];
        self::assertSame('Increase body to 3', $entry->description);
        self::assertSame(-15, $entry->karma);
    }

    /**
     * Test initializing a character that spent chargen karma on attributes.
     */
    public function testAttributesTooHigh(): void
    {
        $character = $this->createCharacter();
        $character->priorities = [
            'a' => 'skills',
            'b' => 'magic',
            'c' => 'resources',
            'd' => 'metatype',
            'e' => 'attributes',
            'metatype' => 'human',
            'gameplay' => 'established',
            'magic' => 'magician',
        ];
        $character->agility = 6;
        $character->body = 5;
        $character->charisma = 5;

        $this->log->initialize($character);
        self::assertSame(-125, $this->log->getKarma());
        self::assertCount(12, $this->log);
        /** @var KarmaLogEntry */
        $entry = $this->log[1];
        self::assertSame('Increase reaction to 3', $entry->description);
        self::assertSame(-15, $entry->karma);
    }

    /**
     * Test initializing a character that spent chargen karma on martial arts.
     */
    public function testMartialArts(): void
    {
        $character = $this->createCharacter();
        $character->martialArts = [
            'styles' => ['aikido'],
            'techniques' => ['called-shot-disarm', 'constrictors-crush'],
        ];
        $this->log->initialize($character);
        self::assertSame(13, $this->log->getKarma());
        self::assertCount(3, $this->log);
        /** @var KarmaLogEntry */
        $entry = $this->log[1];
        self::assertSame('Add martial art Aikido', $entry->description);
        self::assertSame(-7, $entry->karma);
        /** @var KarmaLogEntry */
        $entry = $this->log[2];
        self::assertSame(
            'Add technique Constrictor\'s Crush',
            $entry->description
        );
        self::assertSame(-5, $entry->karma);
    }

    /**
     * Test initializing a log with a character that bought qualities.
     */
    public function testQualities(): void
    {
        $character = $this->createCharacter();
        $character->qualities = [['id' => 'fame-local'], ['id' => 'lucky']];
        $this->log->initialize($character);
        self::assertSame(9, $this->log->getKarma());
        self::assertCount(3, $this->log);
        /** @var KarmaLogEntry */
        $entry = $this->log[1];
        self::assertSame('Add quality Fame (Local)', $entry->description);
        self::assertSame(-4, $entry->karma);
        /** @var KarmaLogEntry */
        $entry = $this->log[2];
        self::assertSame('Add quality Lucky', $entry->description);
        self::assertSame(-12, $entry->karma);
    }

    /**
     * Test initializing a log with a character that didn't overspend on skills.
     */
    public function testSkillsOkay(): void
    {
        $character = $this->createCharacter();
        $character->skills = [['id' => 'computer', 'level' => 6]];
        $this->log->initialize($character);
        self::assertSame(25, $this->log->getKarma());
        self::assertCount(1, $this->log);
    }

    /**
     * Test initializing a log with a character that overspent on skills.
     */
    public function testSkills(): void
    {
        $character = $this->createCharacter();
        // Priority e can spend 18 points on skills.
        $character->priorities = [
            'a' => 'attributes',
            'e' => 'skills',
            'c' => 'resources',
            'd' => 'metatype',
            'b' => 'magic',
            'metatype' => 'human',
            'gameplay' => 'established',
            'magic' => null,
        ];
        $character->skills = [
            ['id' => 'astral-combat', 'level' => 6],
            ['id' => 'automatics', 'level' => 4],
            ['id' => 'computer', 'level' => 6],
            ['id' => 'hacking', 'level' => 2],
            ['id' => 'pistols', 'level' => 1, 'specialization' => 'Special'],
        ];
        $this->log->initialize($character);
        self::assertSame(19, $this->log->getKarma());
        self::assertCount(3, $this->log);
        /** @var KarmaLogEntry */
        $entry = $this->log[1];
        self::assertSame('2₭ for Pistols (1)', $entry->description);
        self::assertSame(-2, $entry->karma);
        /** @var KarmaLogEntry */
        $entry = $this->log[2];
        self::assertSame('4₭ for Hacking (2)', $entry->description);
        self::assertSame(-4, $entry->karma);
    }

    /**
     * Test initializing a log with a character where overspending on skills
     * lead to buying a specialization with karma is the cheapest option.
     */
    public function testSkillsSpecialization(): void
    {
        $character = $this->createCharacter();
        // Priority e can spend 18 points on skills.
        $character->priorities = [
            'a' => 'attributes',
            'e' => 'skills',
            'c' => 'resources',
            'd' => 'metatype',
            'b' => 'magic',
            'metatype' => 'human',
            'gameplay' => 'established',
            'magic' => null,
        ];
        $character->skills = [
            ['id' => 'astral-combat', 'level' => 6],
            ['id' => 'automatics', 'level' => 6],
            ['id' => 'computer', 'level' => 6, 'specialization' => 'Special'],
        ];
        $this->log->initialize($character);
        self::assertSame(18, $this->log->getKarma());
        self::assertCount(2, $this->log);
        /** @var KarmaLogEntry */
        $entry = $this->log[1];
        self::assertSame(
            '7₭ for Computer specialization Special',
            $entry->description
        );
        self::assertSame(-7, $entry->karma);
    }

    /**
     * Test initializing a karma log with a magical character that overspent on
     * skills, including their free ones.
     */
    public function testMagicalSkills(): void
    {
        $character = $this->createCharacter();
        // Priority e can spend 18 points on skills.
        // Priority b magicians get two free rating 4 magical skills.
        $character->priorities = [
            'a' => 'attributes',
            'e' => 'skills',
            'c' => 'resources',
            'd' => 'metatype',
            'b' => 'magic',
            'metatype' => 'human',
            'gameplay' => 'established',
            'magic' => 'magician',
        ];
        $character->skills = [
            // First magical skill (up to level 4) is free.
            ['id' => 'astral-combat', 'level' => 4],
            // First four ranks in second one are free.
            ['id' => 'astral-combat', 'level' => 6],
            // Fill up some more skills that would take them over the limit if
            // they didn't have the free skill ranks.
            ['id' => 'computer', 'level' => 6],
            ['id' => 'automatics', 'level' => 6],
            ['id' => 'hacking', 'level' => 4],
        ];
        $this->log->initialize($character);
        self::assertSame(25, $this->log->getKarma());
        self::assertCount(1, $this->log);
    }

    /**
     * Test a character that bought knowledge, but didn't overspend.
     */
    public function testNotTooMuchKnowledge(): void
    {
        // A character intuition 3 and logic 3 gets 12 points.
        $character = $this->createCharacter();
        $character->knowledgeSkills = [
            ['name' => 'English', 'category' => 'language', 'level' => 'N'],
            ['name' => 'Foo', 'category' => 'street', 'level' => 6],
            ['name' => 'Bar', 'category' => 'street', 'level' => 6],
        ];
        $this->log->initialize($character);
        self::assertSame(25, $this->log->getKarma());
        self::assertCount(1, $this->log);
    }

    /**
     * Test a character that overspent on knowledge skills.
     */
    public function testKnowledge(): void
    {
        // A character intuition 3 and logic 3 gets 12 points.
        $character = $this->createCharacter();
        $character->knowledgeSkills = [
            ['name' => 'English', 'category' => 'language', 'level' => 'N'],
            ['name' => 'Foo', 'category' => 'street', 'level' => 8],
            [
                'name' => 'Bar',
                'category' => 'street',
                'level' => 8,
                'specialization' => 'Special',
            ],
        ];
        $this->log->initialize($character);
        self::assertSame(-8, $this->log->getKarma());
        self::assertCount(6, $this->log);
        /** @var KarmaLogEntry */
        $entry = $this->log[1];
        self::assertSame(
            '7₭ for Bar specialization Special',
            $entry->description
        );
        self::assertSame(-7, $entry->karma);
        /** @var KarmaLogEntry */
        $entry = $this->log[2];
        self::assertSame('8₭ for Foo (8)', $entry->description);
        self::assertSame(-8, $entry->karma);
    }

    /**
     * Test initializing a log with a character that overspent skill groups.
     */
    public function testSkillGroups(): void
    {
        // Character with skill priority B gets 5 points.
        $character = $this->createCharacter();

        $character->skillGroups = [
            // Should use 1 point and 10 karma.
            'firearms' => 2,
            // Should use 4 of the skill points.
            'electronics' => 4,
            // Should cost 5 karma.
            'cracking' => 1,
        ];

        $this->log->initialize($character);
        self::assertSame(10, $this->log->getKarma());
        self::assertCount(3, $this->log);
        /** @var KarmaLogEntry */
        $entry = $this->log[1];
        self::assertSame(
            'Raise skill group Firearms to 2',
            $entry->description
        );
        self::assertSame(-10, $entry->karma);
        /** @var KarmaLogEntry */
        $entry = $this->log[2];
        self::assertSame(
            'Raise skill group Cracking to 1',
            $entry->description
        );
        self::assertSame(-5, $entry->karma);
    }

    /**
     * Test initializing a log with a character that overspent contacts.
     */
    public function testContacts(): void
    {
        $character = $this->createCharacter();
        // A character with 1 charisma can spend 3 points on contacts.
        $character->charisma = 1;
        $character->contacts = [
            [
                'archetype' => 'bartender',
                'connection' => 1,
                'id' => 1,
                'loyalty' => 1,
                'name' => 'Contact 2',
            ],
            [
                'archetype' => 'fixer',
                'connection' => 3,
                'id' => 2,
                'loyalty' => 3,
                'name' => 'Contact 1',
            ],
        ];
        $this->log->initialize($character);
        self::assertSame(20, $this->log->getKarma());
        self::assertCount(2, $this->log);
        /** @var KarmaLogEntry */
        $entry = $this->log[1];
        self::assertSame(
            'Karma for extra contact rating (Contact 1)',
            $entry->description
        );
        self::assertSame(-5, $entry->karma);
    }

    /**
     * Test a magical character with no spells.
     */
    public function testNoSpells(): void
    {
        $character = $this->createCharacter();
        $character->priorities = [
            'a' => 'attributes',
            'b' => 'skills',
            'c' => 'magic',
            'd' => 'metatype',
            'e' => 'resources',
            'metatype' => 'human',
            'gameplay' => 'established',
            'magic' => 'adept',
        ];
        $character->magics = ['powers' => []];
        $this->log->initialize($character);
        self::assertSame(25, $this->log->getKarma());
        self::assertCount(1, $this->log);
    }

    /**
     * Test a magical character with extra spells.
     */
    public function testExtraSpells(): void
    {
        $character = $this->createCharacter();
        $character->priorities = [
            'a' => 'attributes',
            'b' => 'skills',
            'c' => 'magic',
            'd' => 'metatype',
            'e' => 'resources',
            'metatype' => 'human',
            'gameplay' => 'established',
            'magic' => 'magician',
        ];
        $character->magics = [
            'spells' => [
                'control-emotions',
                'control-emotions',
                'control-emotions',
                'control-emotions',
                'control-emotions',
                'control-emotions',
            ],
        ];
        $this->log->initialize($character);
        self::assertSame(20, $this->log->getKarma());
        self::assertCount(2, $this->log);
        /** @var KarmaLogEntry */
        $entry = $this->log[1];
        self::assertSame('Extra spell (Control Emotions)', $entry->description);
        self::assertSame(-5, $entry->karma);
    }

    /**
     * Test a character spending extra on complex forms.
     */
    public function testExtraForms(): void
    {
        $character = $this->createCharacter();
        // Technomancers at priority C get a single complex form.
        $character->priorities = [
            'a' => 'attributes',
            'b' => 'skills',
            'c' => 'magic',
            'd' => 'metatype',
            'e' => 'resources',
            'metatype' => 'human',
            'gameplay' => 'established',
            'magic' => 'technomancer',
        ];
        $character->complexForms = [
            'cleaner',
            'cleaner',
            'cleaner',
        ];
        $this->log->initialize($character);
        self::assertSame(17, $this->log->getKarma());
        self::assertCount(3, $this->log);
        /** @var KarmaLogEntry */
        $entry = $this->log[1];
        self::assertSame(
            'Extra complex form (Cleaner)',
            $entry->description
        );
        self::assertSame(-4, $entry->karma);
    }

    /**
     * Test a character spending too much on gear.
     */
    public function testExtraNuyen(): void
    {
        // Start with 6000 nuyen.
        $character = $this->createCharacter();
        $character->priorities = [
            'a' => 'attributes',
            'b' => 'skills',
            'c' => 'magic',
            'd' => 'metatype',
            'e' => 'resources',
            'metatype' => 'human',
            'gameplay' => 'established',
            'magic' => 'magician',
        ];

        $character->gear = [
            [
                // Add a commlink (100) with both a program (250) and a
                // modification (12000).
                'id' => 'commlink-sony-angel',
                'quantity' => true,
                'modifications' => ['attack-dongle-2'],
                'programs' => ['armor'],
            ],
            [
                // Add some normal gear (100) with a modification (250).
                'id' => 'goggles-2',
                'quantity' => true,
                'modifications' => ['flare-compensation'],
            ],
        ];

        // Add some armor (1000) with both a cost addition (1500) and a cost
        // multiplier (1000).
        $character->armor = [
            [
                'id' => 'armor-jacket',
                'modifications' => ['auto-injector', 'ynt-softweave-armor'],
            ],
        ];

        // Add an augmentation (3000), of a better grade (1500), with a
        // modification (2250 + 1125).
        $character->augmentations = [
            [
                'id' => 'cyberears-1',
                'grade' => Augmentation::GRADE_BETA,
                'modifications' => [
                    'damper',
                ],
            ],
        ];

        // Add an identity with a fake SIN (5000), fake license (400), and a
        // modified lifestyle (8000 + 100).
        $character->identities = [
            [
                'name' => 'Test Ident',
                'id' => 0,
                'sin' => 2,
                'licenses' => [
                    ['rating' => 2, 'license' => 'Drivers'],
                ],
                'lifestyles' => [
                    [
                        'name' => 'commercial',
                        'quantity' => 1,
                        'options' => [
                            'swimming-pool',
                        ],
                    ],
                ],
            ],
        ];

        // Add a vehicle (3000) with some modifications (1500), and a weapon
        // (1250).
        $character->vehicles = [
            [
                'id' => 'dodge-scoot',
                'modifications' => [
                    'manual-control-override',
                    'rigger-interface',
                ],
                'weapons' => [
                    ['id' => 'ak-98'],
                ],
            ],
        ];

        // Add a weapon (950) with an accessory (50) and a cost-multiplier
        // modification (950).
        // TODO Add ammunition.
        $character->weapons = [
            [
                'id' => 'ak-97',
                'accessories' => [
                    'top' => 'bayonet',
                ],
                'modifications' => ['smartlink-internal'],
            ],
        ];

        $this->log->initialize($character);
        self::assertSame(6, $this->log->getKarma());
        self::assertCount(2, $this->log);
        /** @var KarmaLogEntry */
        $entry = $this->log[1];
        self::assertSame('19 karma converted to ¥38,000', $entry->description);
        self::assertSame(-19, $entry->karma);
    }

    /**
     * Test initializing a Karma Log from an array.
     */
    public function testFromArray(): void
    {
        $rawLog = json_decode(
            '[{"description":"Initial karma","karma":25},'
                . '{"description":"Increase body to 3","karma":-15},'
                . '{"description":"Add Gremlins","karma":4},'
                . '{"description":"Add Distinctive Style","karma":5},'
                . '{"description":"Add Codeblock","karma":10},'
                . '{"description":"Add Addiction (Mild)","karma":4},'
                . '{"description":"Add Machinist","karma":-20},'
                . '{"description":"Convert 3 karma to nuyen","karma":-3},'
                . '{"description":"Extra complex form: Resonance Veil","karma":-4},'
                . '{"description":"Extra complex form: Static Veil","karma":-4},'
                . '{"description":"Getting out of the hotel alive","karma":3,'
                . '"realDate":"2019-10-24","gameDate":"2080-01-06"},'
                . '{"description":"Truck hijacking for Slide","karma":4,'
                . '"realDate":"2020-03-13","gameDate":"2080-01-12"}]',
            true
        );
        $this->log->fromArray($rawLog);
        self::assertCount(12, $this->log);
    }
}
