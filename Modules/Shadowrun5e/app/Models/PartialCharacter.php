<?php

declare(strict_types=1);

namespace Modules\Shadowrun5e\Models;

use Illuminate\Database\Eloquent\Factories\Factory;
use Modules\Shadowrun5e\Database\Factories\PartialCharacterFactory;
use Modules\Shadowrun5e\Events\KarmaGained;
use Modules\Shadowrun5e\Events\KarmaSpent;
use Override;
use RuntimeException;
use Stringable;

use function abs;
use function array_search;
use function event;
use function sprintf;
use function substr;
use function usort;

/**
 * Representation of a character currently being built.
 * @method static self create(array<string, mixed> $attributes)
 * @phpstan-type StandardPriority array{
 *     a: string,
 *     b: string,
 *     c: string,
 *     d: string,
 *     e: string,
 *     gameplay: string,
 *     magic: string|null,
 *     metatype: string,
 *     rulebooks: string,
 *     startDate?: string,
 *     system: string,
 * }
 * @phpstan-type SumToTenPriority array{
 *     attributePriority: string,
 *     gameplay: string,
 *     magicPriority: string,
 *     metatypePriority: string,
 *     magic?: string,
 *     metatype: string,
 *     resourcePriority: string,
 *     rulebooks: string,
 *     skillPriority: string,
 *     startDate?: string,
 *     system: string,
 * }
 */
class PartialCharacter extends Character implements Stringable
{
    protected const string PRIORITY_STANDARD = 'standard';
    protected const string PRIORITY_SUM_TO_TEN = 'sum-to-ten';
    protected const string PRIORITY_KARMA = 'karma';
    protected const int DEFAULT_MAX_ATTRIBUTE = 6;

    /** @var string */
    protected $table = 'characters-partial';
    /** @var array<string, array<int, string>> */
    public array $errors = [];

    /**
     * Return the starting maximum for a character based on their metatype and
     * qualities.
     */
    public function getStartingMaximumAttribute(string $attribute): int
    {
        $maximums = [
            'dwarf' => [
                'body' => 8,
                'reaction' => 5,
                'strength' => 8,
                'willpower' => 7,
            ],
            'elf' => [
                'agility' => 7,
                'charisma' => 8,
            ],
            'human' => [
                'edge' => 7,
            ],
            'ork' => [
                'body' => 9,
                'charisma' => 5,
                'logic' => 5,
                'strength' => 8,
            ],
            'troll' => [
                'agility' => 5,
                'body' => 10,
                'charisma' => 4,
                'intuition' => 5,
                'logic' => 5,
                'strength' => 10,
            ],
        ];
        $max = $maximums[$this->metatype][$attribute] ?? self::DEFAULT_MAX_ATTRIBUTE;
        foreach ($this->getQualities() as $quality) {
            if (!isset($quality->effects['maximum-' . $attribute])) {
                continue;
            }
            $max += $quality->effects['maximum-' . $attribute];
        }
        return $max;
    }

    /**
     * Return whether the given character is awakened and not a techno.
     */
    public function isMagicallyActive(): bool
    {
        return isset($this->priorities, $this->priorities['magic'])
            && 'technomancer' !== $this->priorities['magic'];
    }

    /**
     * Return whether the character is a technomancer.
     */
    public function isTechnomancer(): bool
    {
        return isset($this->priorities, $this->priorities['magic'])
            && 'technomancer' === $this->priorities['magic'];
    }

    #[Override]
    protected static function newFactory(): Factory
    {
        return PartialCharacterFactory::new();
    }

    #[Override]
    public function newFromBuilder(
        // @phpstan-ignore parameter.defaultValue
        $attributes = [],
        $connection = null,
    ): PartialCharacter {
        $character = new self((array)$attributes);
        $character->exists = true;
        $character->setRawAttributes((array)$attributes, true);
        $character->setConnection($this->connection);
        $character->fireModelEvent('retrieved', false);
        $character->fillable[] = 'errors';
        // @phpstan-ignore return.type
        return $character;
    }

    public function toCharacter(): Character
    {
        /** @var StandardPriority|SumToTenPriority $priorities */
        $priorities = $this->priorities ?? [];
        $character = new Character();
        $character->background = $this->background;
        $character->priorities = $priorities;
        //$character->save();
        //event(new KarmaGained($character, 25, 'Initial karma'));
        /** @var Race $race */
        $race = Race::findOrFail($priorities['metatype']);

        $priority_method = match (true) {
            isset($priorities['a']) => self::PRIORITY_STANDARD,
            isset($priorities['attributePriority']) => self::PRIORITY_SUM_TO_TEN,
            // TODO: Add karma build.
            default => throw new RuntimeException('Invalid priority method'),
        };

        $this->chargeForAttributes($character, $priority_method, $priorities, $race);
        $this->chargeForSpecialAttributes($character, $priority_method, $priorities, $race);
        $this->chargeForQualities($character);
        $this->chargeForSkills($character, $priority_method, $priorities);
        //dd($character);
        return $character;
    }

    /**
     * @param StandardPriority|SumToTenPriority $priorities
     */
    private function chargeForAttributes(
        Character $character,
        string $priority_method,
        array $priorities,
        Race $race,
    ): void {
        $attribute_points = match ($priority_method) {
            self::PRIORITY_STANDARD => match (array_search('attributes', $priorities, true)) {
                'a' => 24,
                'b' => 20,
                'c' => 16,
                'd' => 14,
                'e' => 12,
                default => throw new RuntimeException('Invalid attribute priority'),
            },
            self::PRIORITY_SUM_TO_TEN => match ($priorities['attributePriority'] ?? false) {
                'A' => 24,
                'B' => 20,
                'C' => 16,
                'D' => 14,
                'E' => 12,
                default => throw new RuntimeException('Invalid attribute priority'),
            },
            default => throw new RuntimeException('Invalid attribute priority'),
        };

        // Characters get their race's minimums for each attribute for free.
        $attribute_points = $attribute_points + $race->agi_min
            + $race->bod_min + $race->cha_min + $race->int_min
            + $race->log_min + $race->rea_min + $race->str_min
            + $race->wil_min - $this->body - $this->agility
            - $this->reaction - $this->strength - $this->willpower
            - $this->logic - $this->intuition - $this->charisma;
        $attributes = [
            'agility' => $this->agility,
            'body' => $this->body,
            'charisma' => $this->charisma,
            'intuition' => $this->intuition,
            'logic' => $this->logic,
            'reaction' => $this->reaction,
            'strength' => $this->strength,
            'willpower' => $this->willpower,
        ];
        while (0 > $attribute_points) {
            $min_attribute = null;
            $min_attribute_value = 99;
            foreach ($attributes as $attribute => $value) {
                $tmp = substr($attribute, 0, 3) . '_min';
                // @phpstan-ignore property.dynamicName, property.notFound
                if ($value < $min_attribute_value && $race->$tmp !== $value) {
                    $min_attribute = $attribute;
                    $min_attribute_value = $value;
                }
            }
            ++$attribute_points;
            --$attributes[$min_attribute];
            event(new KarmaSpent(
                $character,
                $min_attribute_value * 5,
                sprintf(
                    'Spent %d to raise %s',
                    $min_attribute_value * 5,
                    $min_attribute,
                ),
            ));
        }
        $character->agility = $this->agility;
        $character->body = $this->body;
        $character->charisma = $this->charisma;
        $character->intuition = $this->intuition;
        $character->logic = $this->logic;
        $character->reaction = $this->reaction;
        $character->strength = $this->strength;
        $character->willpower = $this->willpower;
    }

    /**
     * @param StandardPriority|SumToTenPriority $priorities
     */
    private function chargeForSpecialAttributes(
        Character $character,
        string $priority_method,
        array $priorities,
        Race $race,
    ): void {
        $metatype_priority = match ($priority_method) {
            self::PRIORITY_STANDARD => array_search('metatype', $priorities, true),
            self::PRIORITY_SUM_TO_TEN => $priorities['metatypePriority'] ?? false,
            default => throw new RuntimeException('Invalid priority method'),
        };
        if (false === $metatype_priority) {
            throw new RuntimeException('Invalid metatype priority');
        }
        $points = $race->getSpecialPointsForPriority($metatype_priority);

        if ($this->edge > $race->edg_min + $points) {
            for ($i = $race->edg_min + $points + 1; $i <= $this->edge; ++$i) {
                event(new KarmaSpent(
                    $character,
                    $i * 5,
                    sprintf(
                        'Spent %d to raise edge from %d to %d',
                        $i * 5,
                        $i - 1,
                        $i,
                    ),
                ));
                --$points;
            }
        }
        $character->edge = $this->edge;

        // TODO: Magic and resonance
    }

    private function chargeForQualities(Character $character): void
    {
        $qualities = [];
        foreach ($this->getQualities() as $quality) {
            if (null === $quality->severity) {
                $qualities[] = [
                    'id' => $quality->id,
                ];
            } else {
                $qualities[] = [
                    'id' => $quality->id,
                    'severity' => $quality->severity,
                ];
            }
            if (0 > $quality->karma) {
                event(new KarmaSpent(
                    $character,
                    abs($quality->karma),
                    sprintf(
                        'Spent %d for positive quality "%s"',
                        abs($quality->karma),
                        (string)$quality,
                    ),
                ));
                continue;
            }

            event(new KarmaGained(
                $character,
                $quality->karma,
                sprintf(
                    'Spent %d for negative quality "%s"',
                    $quality->karma,
                    (string)$quality,
                ),
            ));
        }
        $character->qualities = $qualities;
    }

    /**
     * @param StandardPriority|SumToTenPriority $priorities
     */
    private function chargeForSkills(
        Character $character,
        string $priority_method,
        array $priorities,
    ): void {
        // TODO: Add free magic and resonance skills.
        $skill_points = match ($priority_method) {
            self::PRIORITY_STANDARD => match (array_search('skills', $priorities, true)) {
                'a' => 46,
                'b' => 36,
                'c' => 28,
                'd' => 22,
                'e' => 18,
                default => throw new RuntimeException('Invalid skills priority'),
            },
            self::PRIORITY_SUM_TO_TEN => match ($priorities['skillPriority'] ?? null) {
                'A' => 46,
                'B' => 36,
                'C' => 28,
                'D' => 22,
                'E' => 18,
                default => throw new RuntimeException('Invalid skill priority'),
            },
            default => throw new RuntimeException('Invalid priorities'),
        };
        $skills = (array)$this->getSkills();
        foreach ($skills as $key => $skill) {
            if (0 === $skill->level) {
                unset($skills[$key]);
                continue;
            }
            if (null !== $skill->specialization) {
                --$skill_points;
            }
            $skill_points -= $skill->level;
        }

        if ($skill_points >= 0) {
            return;
        }

        usort($skills, [$this, 'sortSkills']);
        dd($skills);
    }

    public static function sortSkills(ActiveSkill $a, ActiveSkill $b): int
    {
        if (3 <= $a->level && 3 <= $b->level) {
            if (null === $a->specialization && null === $b->specialization) {
                // If neither have specializations, just compare their levels.
                return $b->level - $a->level;
            }
            if (null !== $a->specialization && null !== $b->specialization) {
                // If both have specializations, compare their levels.
                return $b->level - $a->level;
            }
            if (null !== $b->specialization) {
                // If the second has a specialization, use it first.
                return -1;
            }
            return 1;
        }
        return $b->level - $a->level;
    }
}
