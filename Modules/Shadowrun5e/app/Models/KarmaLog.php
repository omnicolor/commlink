<?php

declare(strict_types=1);

namespace Modules\Shadowrun5e\Models;

use ArrayObject;
use DateTimeImmutable;
use RuntimeException;
use TypeError;

use function array_filter;
use function array_merge;
use function array_reduce;
use function array_search;
use function array_shift;
use function array_sum;
use function asort;
use function ceil;
use function count;
use function current;
use function in_array;
use function key;
use function number_format;
use function sprintf;
use function ucfirst;
use function usort;

/**
 * Collection of karma log entries.
 * @extends ArrayObject<int, KarmaLogEntry>
 */
class KarmaLog extends ArrayObject
{
    protected const int KARMA_SKILL = 2;
    protected const int KARMA_KNOWLEDGE = 1;
    protected const int KARMA_SPECIALIZATION = 7;

    /**
     * Number of points for attributes from priorities.
     */
    protected int $attributePoints = 0;

    /**
     * Character we're creating a KarmaLog for.
     */
    protected Character $character;

    /**
     * Number of free complex forms a technomancer can start with.
     */
    protected int $complexForms = 0;

    /**
     * Magical skills collection [number of skills, rating of skills].
     * @var array<string, int>
     */
    protected array $magicSkills;

    /**
     * Amount of nuyen from priorities.
     */
    protected int $resources = 0;

    /**
     * Number of skill points from priorities.
     */
    protected int $skillPoints = 0;

    /**
     * Number of skill group points from priorities.
     */
    protected int $skillGroupPoints = 0;

    /**
     * Number of special points from priorities for edge, magic, etc.
     * @psalm-suppress PossiblyUnusedProperty
     */
    protected int $specialPoints = 0;

    /**
     * Number of spells for free from priorities.
     */
    protected int $spells = 0;

    /**
     * Add an entry to the array.
     * @param KarmaLogEntry $entry
     * @psalm-suppress ParamNameMismatch
     * @throws TypeError
     */
    public function offsetSet(mixed $index = null, $entry = null): void
    {
        if ($entry instanceof KarmaLogEntry) {
            parent::offsetSet($index, $entry);
            return;
        }
        throw new TypeError('KarmaLog only accepts KarmaLogEntry objects');
    }

    /**
     * Return the current amount of karma contained in the Karma Log.
     * @psalm-suppress PossiblyUnusedMethod
     */
    public function getKarma(): int
    {
        $karma = 0;
        foreach ($this as $entry) {
            $karma += $entry->karma;
        }
        return $karma;
    }

    /**
     * Count up skill points spent.
     */
    public static function countSkillPoints(?int $carry, Skill $item): int
    {
        // A character should only have one native language, or two with the
        // bilingual quality, which are free.
        if ('N' == $item->level) {
            return $carry ?? 0;
        }
        $carry += (int)$item->level;
        if (isset($item->specialization)) {
            $carry++;
        }
        return $carry;
    }

    /**
     * Filter out skills that are unspecialized.
     */
    public function filterUnspecialized(Skill $skill): bool
    {
        return isset($skill->specialization);
    }

    /**
     * Compare two skills for organizing in reverse level order.
     * @psalm-suppress PossiblyUnusedReturnValue
     */
    public function compareSkills(Skill $a, Skill $b): int
    {
        if ('N' === $a->level && 'N' === $b->level) {
            // Both are native languages.
            return 0;
        }
        if ('N' === $a->level) {
            return -1;
        }
        if ('N' === $b->level) {
            return 1;
        }
        return (int)$a->level - (int)$b->level;
    }

    /**
     * Set the number of points for each class from the chosen priority.
     * @param array<string, bool|null|string> $priorities
     */
    protected function setPointsFromSumToTen(array $priorities): void
    {
        // Sum to ten
        $priorityMap = [
            'A' => [
                'dwarf' => 7,
                'elf' => 8,
                'human' => 9,
                'ork' => 7,
                'troll' => 5,
                'attributes' => 24,
                'activeSkills' => 46,
                'skillGroups' => 10,
                'resources' => [
                    'established' => 450000,
                    'prime' => 500000,
                    'street' => 75000,
                ],
                'magic' => [
                    'magician' => 10,
                ],
            ],
            'B' => [
                'dwarf' => 4,
                'elf' => 6,
                'human' => 7,
                'ork' => 4,
                'troll' => 0,
                'attributes' => 20,
                'activeSkills' => 36,
                'skillGroups' => 5,
                'resources' => [
                    'established' => 275000,
                    'prime' => 325000,
                    'street' => 50000,
                ],
                'magic' => [
                    'magician' => 7,
                ],
            ],
            'C' => [
                'dwarf' => 1,
                'elf' => 3,
                'human' => 5,
                'ork' => 0,
                'attributes' => 16,
                'activeSkills' => 28,
                'skillGroups' => 2,
                'resources' => [
                    'established' => 140000,
                    'prime' => 210000,
                    'street' => 25000,
                ],
                'magic' => [
                    'magician' => 5,
                ],
            ],
            'D' => [
                'elf' => 0,
                'human' => 3,
                'attributes' => 14,
                'activeSkills' => 22,
                'skillGroups' => 0,
                'resources' => [
                    'established' => 50000,
                    'prime' => 150000,
                    'street' => 15000,
                ],
                'magic' => [
                    'magician' => 0,
                ],
            ],
            'E' => [
                'human' => 1,
                'attributes' => 12,
                'activeSkills' => 18,
                'skillGroups' => 0,
                'resources' => [
                    'established' => 6000,
                    'prime' => 100000,
                    'street' => 6000,
                ],
                'magic' => [
                    'magician' => 0,
                ],
            ],
        ];
        $this->attributePoints
            = $priorityMap[$priorities['attributePriority']]['attributes'];
        $this->skillPoints
            = $priorityMap[$priorities['skillPriority']]['activeSkills'];
        $this->skillGroupPoints
            = $priorityMap[$priorities['skillPriority']]['skillGroups'];
        // @phpstan-ignore-next-line
        $this->specialPoints
            = $priorityMap[$priorities['metatypePriority']][$priorities['metatype']];
        $this->resources
            = $priorityMap[$priorities['resourcePriority']]['resources'][$priorities['gameplay']];
        $spells = $priorityMap[$priorities['magicPriority']]['magic'];
        $this->spells = $spells[$priorities['magic']] ?? 0;
        // TODO: Handle free magic/resonance skills
    }

    /**
     * Set the number of points for each class from the chosen priority.
     * @param array<string, bool|null|string> $priorities
     */
    protected function setPointsFromStandardPriority(array $priorities): void
    {
        // Standard priority build.
        $priorityMap = [
            'a' => [
                'attributes' => 24,
                'magic' => [
                    'magician' => [
                        'magic' => 6,
                        'spells' => 10,
                        'skills' => [
                            'number' => 2,
                            'rating' => 5,
                        ],
                    ],
                    'mystic-adept' => [
                        'magic' => 6,
                        'spells' => 10,
                        'skills' => [
                            'number' => 2,
                            'rating' => 5,
                        ],
                    ],
                    'technomancer' => [
                        'complex-forms' => 5,
                        'resonance' => 6,
                        'skills' => [
                            'number' => 2,
                            'rating' => 5,
                        ],
                    ],
                ],
                'metatype' => [
                    'dwarf' => 7,
                    'elf' => 8,
                    'human' => 9,
                    'ork' => 7,
                    'troll' => 5,
                ],
                'resources' => [
                    'established' => 450000,
                    'prime' => 500000,
                    'street' => 75000,
                ],
                'skills' => 46,
                'skillGroups' => 10,
            ],
            'b' => [
                'attributes' => 20,
                'magic' => [
                    'adept' => [
                        'magic' => 6,
                        'skills' => [
                            'number' => 1,
                            'rating' => 4,
                        ],
                    ],
                    'aspected-magician' => [
                        'magic' => 5,
                        'skill-group' => [
                            'number' => 1,
                            'rating' => 4,
                        ],
                    ],
                    'magician' => [
                        'spells' => 7,
                        'skills' => [
                            'number' => 2,
                            'rating' => 4,
                        ],
                        'magic' => 4,
                    ],
                    'mystic-adept' => [
                        'spells' => 7,
                        'skills' => [
                            'number' => 2,
                            'rating' => 4,
                        ],
                        'magic' => 4,
                    ],
                    'technomancer' => [
                        'complex-forms' => 2,
                        'resonance' => 4,
                        'skills' => [
                            'number' => 2,
                            'rating' => 4,
                        ],
                    ],
                ],
                'metatype' => [
                    'dwarf' => 4,
                    'elf' => 6,
                    'human' => 7,
                    'ork' => 4,
                    'troll' => 0,
                ],
                'resources' => [
                    'established' => 275000,
                    'prime' => 325000,
                    'street' => 50000,
                ],
                'skills' => 36,
                'skillGroups' => 5,
            ],
            'c' => [
                'attributes' => 16,
                'magic' => [
                    'adept' => [
                        'magic' => 4,
                        'skills' => [
                            'number' => 1,
                            'rating' => 2,
                        ],
                    ],
                    'aspected-magician' => [
                        'magic' => 3,
                        'skill-groups' => [
                            'number' => 1,
                            'rating' => 2,
                        ],
                    ],
                    'magician' => [
                        'spells' => 5,
                        'magic' => 3,
                    ],
                    'mystic-adept' => [
                        'spells' => 5,
                        'magic' => 3,
                    ],
                    'technomancer' => [
                        'complex-forms' => 1,
                        'resonance' => 3,
                    ],
                ],
                'metatype' => [
                    'dwarf' => 1,
                    'elf' => 3,
                    'human' => 5,
                    'ork' => 0,
                ],
                'resources' => [
                    'established' => 140000,
                    'prime' => 210000,
                    'street' => 25000,
                ],
                'skills' => 28,
                'skillGroups' => 2,
            ],
            'd' => [
                'attributes' => 14,
                'magic' => [
                    'adept' => [
                        'magic' => 2,
                    ],
                    'aspected-magician' => [
                        'magic' => 2,
                    ],
                ],
                'metatype' => [
                    'elf' => 0,
                    'human' => 3,
                ],
                'resources' => [
                    'established' => 50000,
                    'prime' => 150000,
                    'street' => 15000,
                ],
                'skills' => 22,
                'skillGroups' => 0,
            ],
            'e' => [
                'attributes' => 12,
                'magic' => [],
                'metatype' => [
                    'human' => 1,
                ],
                'resources' => [
                    'established' => 6000,
                    'prime' => 100000,
                    'street' => 6000,
                ],
                'skills' => 18,
                'skillGroups' => 0,
            ],
        ];
        $this->attributePoints
            = $priorityMap[array_search('attributes', $priorities, true)]['attributes'];
        $this->resources
            = $priorityMap[array_search('resources', $priorities, true)]['resources'][$priorities['gameplay']];
        $this->skillPoints
            = $priorityMap[array_search('skills', $priorities, true)]['skills'];
        $this->skillGroupPoints
            = $priorityMap[array_search('skills', $priorities, true)]['skillGroups'];
        $this->specialPoints
            = $priorityMap[array_search('metatype', $priorities, true)]['metatype'][$priorities['metatype']];

        $magic = $priorityMap[array_search('magic', $priorities, true)]['magic'];
        $this->spells = $magic[$priorities['magic']]['spells'] ?? 0;
        $this->magicSkills = $magic[$priorities['magic']]['skills'] ?? [];
        $this->complexForms = $magic[$priorities['magic']]['complex-forms'] ?? 0;
    }

    /**
     * Process the user's attributes, charging minimum karma for overspending.
     */
    protected function processAttributes(): void
    {
        $attributeList = [
            'body' => $this->character->body,
            'agility' => $this->character->agility,
            'reaction' => $this->character->reaction,
            'strength' => $this->character->strength,
            'willpower' => $this->character->willpower,
            'logic' => $this->character->logic,
            'intuition' => $this->character->intuition,
            'charisma' => $this->character->charisma,
        ];
        for ($i = array_sum($attributeList) - 8 - $this->attributePoints; $i > 0; $i--) {
            asort($attributeList);
            $value = (int)current($attributeList);
            $key = key($attributeList);
            $this[] = new KarmaLogEntry(
                sprintf('Increase %s to %d', $key, $value),
                $value * -5,
            );
            if (2 == $value) {
                // The first one's free, so this attribute can't be used
                // anymore.
                unset($attributeList[$key]);
            } else {
                $attributeList[$key]--;
            }
        }
    }

    /**
     * Process the user's martial arts, charging karma for techniques past the
     * first.
     */
    protected function processMartialArts(): void
    {
        $styles = $this->character->getMartialArtsStyles();
        if (0 === count($styles)) {
            return;
        }

        foreach ($styles as $style) {
            $this[] = new KarmaLogEntry(
                sprintf('Add martial art %s', $style->name),
                -7,
            );
        }

        $techniques = (array)$this->character->getMartialArtsTechniques();
        // Only techniques past the first cost karma.
        array_shift($techniques);
        foreach ($techniques as $technique) {
            $this[] = new KarmaLogEntry(
                sprintf('Add technique %s', $technique->name),
                -5,
            );
        }
    }

    /**
     * Process the user's qualities, adding an entry for each.
     */
    protected function processQualities(): void
    {
        foreach ($this->character->getQualities() as $quality) {
            $this[] = new KarmaLogEntry(
                sprintf('Add quality %s', $quality->name),
                $quality->karma,
            );
        }
    }

    /**
     * Process the user's skill groups, charging karma for any over their
     * priorities.
     */
    protected function processSkillGroups(): void
    {
        $groups = $this->character->getSkillGroups();
        usort($groups, function (SkillGroup $a, SkillGroup $b): int {
            return $b->level - $a->level;
        });
        foreach ($groups as $group) {
            if ($group->level <= $this->skillGroupPoints) {
                $this->skillGroupPoints -= $group->level;
                continue;
            }
            for ($i = $this->skillGroupPoints; $i < $group->level; $i++) {
                $this[] = new KarmaLogEntry(
                    sprintf(
                        'Raise skill group %s to %d',
                        ucfirst($group->name),
                        $i + 1
                    ),
                    ($i + 1) * -5
                );
                if ($this->skillGroupPoints > 0) {
                    $this->skillGroupPoints--;
                }
            }
        }
    }

    /**
     * Reduce the level for relevant magical skills, assuming the character is
     * awakened and gets some free skills.
     * @param SkillArray $skills
     * @return SkillArray
     */
    protected function processMagicalSkills(SkillArray $skills): SkillArray
    {
        if ([] === $this->magicSkills) {
            return $skills;
        }
        $validSkills = [
            'alchemy',
            'assensing',
            'astral-combat',
            'banishing',
            'binding',
            'counterspelling',
            'spellcasting',
            'summoning',
        ];
        while ($this->magicSkills['number']--) {
            /** @var ActiveSkill $skill */
            foreach ($skills as $key => $skill) {
                if (!in_array($skill->id, $validSkills, true)) {
                    continue;
                }
                if ($skill->level < $this->magicSkills['rating']) {
                    continue;
                }
                // The user can get free points in this skill.
                // @phpstan-ignore-next-line
                $skill->level -= $this->magicSkills['rating'];
                $skills[$key] = $skill;
            }
        }
        return $skills;
    }

    /**
     * Process the user's active skills.
     */
    protected function processSkills(): void
    {
        if (0 === count($this->character->getSkills())) {
            // Character has no skills.
            return;
        }
        $skills = $this->processMagicalSkills($this->character->getSkills());
        $skills = (array)$skills;
        $deficit = (int)array_reduce($skills, [$this, 'countSkillPoints'])
            - $this->skillPoints;
        if (0 >= $deficit) {
            // If they didn't spend too much, don't worry about the rest.
            return;
        }
        $specializations = array_filter(
            $skills,
            [$this, 'filterUnspecialized']
        );
        usort($skills, [$this, 'compareSkills']);

        /** @var ActiveSkill */
        $skill = array_shift($skills);
        while (0 < $deficit && null !== $skill) {
            if (0 === $skill->level) {
                $skill = array_shift($skills);
                continue;
            }
            $skill->level = $skill->level;

            if (
                (int)$skill->level * self::KARMA_SKILL > self::KARMA_SPECIALIZATION
                && 0 !== count($specializations)
            ) {
                // The next cheapest skill, karma-wise, is a specialization.
                /** @var ActiveSkill */
                $tmp = array_shift($specializations);
                $this[] = new KarmaLogEntry(
                    sprintf(
                        '%d₭ for %s specialization %s',
                        self::KARMA_SPECIALIZATION,
                        $tmp->name,
                        $tmp->specialization
                    ),
                    -1 * self::KARMA_SPECIALIZATION
                );
                $deficit--;
                continue;
            }

            // Charge karma for the next lowest skill level.
            $this[] = new KarmaLogEntry(
                sprintf(
                    '%d₭ for %s (%d)',
                    (int)$skill->level * self::KARMA_SKILL,
                    $skill->name,
                    $skill->level
                ),
                (int)$skill->level * self::KARMA_SKILL * -1,
            );
            $skill->level--;
            $deficit--;
        }
    }

    /**
     * Process the character's knowledge skills.
     */
    protected function processKnowledgeSkills(): void
    {
        if (0 === count($this->character->getKnowledgeSkills())) {
            // They have no knowledge.
            return;
        }
        $points = ($this->character->intuition + $this->character->logic) * 2;
        $skills = (array)$this->character->getKnowledgeSkills();
        $deficit = (int)array_reduce($skills, [$this, 'countSkillPoints']) - $points;
        if (0 >= $deficit) {
            // They didn't overspend.
            return;
        }
        $specializations = array_filter(
            $skills,
            [$this, 'filterUnspecialized']
        );
        usort($skills, [$this, 'compareSkills']);

        /** @var Skill */
        $skill = array_shift($skills);
        while (0 < $deficit && null !== $skill) {
            if ('N' === $skill->level) {
                $skill = array_shift($skills);
                continue;
            }
            $skill->level = (int)$skill->level;

            if (
                $skill->level * self::KARMA_KNOWLEDGE > self::KARMA_SPECIALIZATION
                && 0 !== count($specializations)
            ) {
                // The next cheapest skill, karma-wise, is a specialization.
                /** @var Skill */
                $tmp = array_shift($specializations);
                $this[] = new KarmaLogEntry(
                    sprintf(
                        '%d₭ for %s specialization %s',
                        self::KARMA_SPECIALIZATION,
                        $tmp->name,
                        $tmp->specialization
                    ),
                    -1 * self::KARMA_SPECIALIZATION
                );
                $deficit--;
                continue;
            }

            // Charge karma for the next lowest skill level.
            $this[] = new KarmaLogEntry(
                sprintf(
                    '%d₭ for %s (%d)',
                    $skill->level * self::KARMA_KNOWLEDGE,
                    $skill->name,
                    $skill->level
                ),
                $skill->level * self::KARMA_KNOWLEDGE * -1,
            );
            $skill->level--;
            $deficit--;
        }
    }

    /**
     * Process the user's contacts, charging for overspend.
     */
    protected function processContacts(): void
    {
        $contactPoints = 3 * $this->character->charisma;
        foreach ($this->character->getContacts() as $contact) {
            $points = (int)$contact->loyalty + (int)$contact->connection;
            if ($points > $contactPoints) {
                $this[] = new KarmaLogEntry(
                    sprintf(
                        'Karma for extra contact rating (%s)',
                        $contact->name
                    ),
                    (-1 * ($points - $contactPoints))
                );
                $contactPoints = 0;
            } else {
                $contactPoints -= $points;
            }
        }
    }

    /**
     * Process the user's spells, charging karma for extras.
     */
    protected function processSpells(): void
    {
        if (
            0 === count($this->character->getSpells())
            || $this->spells >= count($this->character->getSpells())
        ) {
            return;
        }
        foreach ($this->character->getSpells() as $spell) {
            $this->spells--;
            if ($this->spells >= 0) {
                continue;
            }
            $this[] = new KarmaLogEntry(
                sprintf('Extra spell (%s)', $spell->name),
                -5
            );
        }
    }

    /**
     * Process the user's gear, vehicles, weapons, etc, charging karma for
     * every 2000 over.
     */
    protected function processNuyen(): void
    {
        $spent = 0;
        $items = array_merge(
            (array)$this->character->getAugmentations(),
            (array)$this->character->getArmor(),
            (array)$this->character->getGear(),
            (array)$this->character->getIdentities(),
            (array)$this->character->getVehicles(),
            (array)$this->character->getWeapons()
        );

        foreach ($items as $item) {
            $spent += $item->getCost();
        }

        if ($spent <= $this->resources) {
            return;
        }
        $karma = (int)ceil(($spent - $this->resources) / 2000);
        $this[] = new KarmaLogEntry(
            sprintf(
                '%d karma converted to ¥%s',
                $karma,
                number_format($karma * 2000)
            ),
            $karma * -1
        );
    }

    /**
     * Process the user's complex forms, charging 4 karma per additional
     * complex form.
     */
    protected function processComplexForms(): void
    {
        // Character has no complex forms, no need to charge Karma.
        if (0 === count($this->character->getComplexForms())) {
            return;
        }

        foreach ($this->character->getComplexForms() as $form) {
            if ($this->complexForms > 0) {
                $this->complexForms--;
                continue;
            }
            $this[] = new KarmaLogEntry(
                sprintf('Extra complex form (%s)', $form->name),
                -4
            );
        }
    }

    /**
     * Build a new Karma Log.
     * @throws RuntimeException
     */
    public function initialize(Character $character): KarmaLog
    {
        if (!isset($character->priorities)) {
            throw new RuntimeException('Priorities not set');
        }

        $this->character = $character;
        $this[] = new KarmaLogEntry('Initial karma', 25);

        if (isset($character->priorities['attributePriority'])) {
            $this->setPointsFromSumToTen($character->priorities);
        } else {
            $this->setPointsFromStandardPriority($character->priorities);
        }
        $this->processAttributes();
        $this->processMartialArts();
        $this->processQualities();
        $this->processSkillGroups();
        $this->processSkills();
        $this->processKnowledgeSkills();
        $this->processContacts();
        $this->processSpells();
        $this->processNuyen();
        $this->processComplexForms();

        return $this;
    }

    /**
     * Populate a karma log from an array of log entries (from Mongo).
     * @param array<int, array<string, mixed>> $log
     */
    public function fromArray(array $log): KarmaLog
    {
        foreach ($log as $entry) {
            $realDate = $gameDate = null;
            if (isset($entry['realDate'])) {
                $realDate = new DateTimeImmutable($entry['realDate']);
            }
            if (isset($entry['gameDate'])) {
                $gameDate = new DateTimeImmutable($entry['gameDate']);
            }
            $this[] = new KarmaLogEntry(
                $entry['description'],
                (int)$entry['karma'],
                $realDate,
                $gameDate
            );
        }
        return $this;
    }
}
