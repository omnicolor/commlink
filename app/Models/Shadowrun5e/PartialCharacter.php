<?php

declare(strict_types=1);

namespace App\Models\Shadowrun5e;

/**
 * Representation of a character currently being built.
 * @property array<int, string> $errors
 */
class PartialCharacter extends Character
{
    protected const PRIORITY_STANDARD = 'standard';
    protected const PRIORITY_SUM_TO_TEN = 'sum-to-ten';
    protected const PRIORITY_KARMA = 'karma';

    /**
     * The database connection that should be used by the model.
     * @var ?string
     */
    protected $connection = 'mongodb';

    /**
     * Table to pull from.
     * @var string
     */
    protected $table = 'characters-partial';

    protected string $priority_method;

    // @phpstan-ignore-next-line
    public function newFromBuilder(
        $attributes = [],
        $connection = null
    ): PartialCharacter {
        $character = new self($attributes);
        $character->exists = true;
        $character->setRawAttributes($attributes, true);
        $character->setConnection($this->connection);
        $character->fireModelEvent('retrieved', false);
        $character->fillable[] = 'errors';
        return $character;
    }

    /**
     * Return the starting maximum for a character based on their metatype and
     * qualities.
     * @param string $attribute
     * @return int
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
        $max = $maximums[$this->metatype][$attribute] ?? 6;
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
     * @return bool
     */
    public function isMagicallyActive(): bool
    {
        return isset($this->priorities, $this->priorities['magic'])
            // @phpstan-ignore-next-line
            && null !== $this->priorities['magic']
            && 'technomancer' !== $this->priorities['magic'];
    }

    /**
     * Return whether the character is a technomancer.
     * @return bool
     */
    public function isTechnomancer(): bool
    {
        return isset($this->priorities, $this->priorities['magic'])
            // @phpstan-ignore-next-line
            && null !== $this->priorities['magic']
            && 'technomancer' === $this->priorities['magic'];
    }

    /**
     * Validate the character against Shadowrun 5E's rules.
     *
     * Stores any errors or warnings in the errors property, similar to how
     * HeroLab or Chummer import does.
     */
    public function validate(): void
    {
        $this->errors = array_merge(
            $this->errors ?? [],
            $this->validatePriorities(),
            $this->validateNativeLanguage(),
        );
    }

    /**
     * @return array<int, string>
     */
    protected function validatePriorities(): array
    {
        $errors = [];
        if (!isset($this->priorities['a']) && !isset($this->priorities['metatypePriority'])) {
            $errors[] = 'You must choose <a href="/characters/shadowrun5e/create/priorities">priorities</a>.';
            return $errors;
        }
        if (!isset($this->priorities['metatype'])) {
            $errors[] = 'You must choose a <a href="/characters/shadowrun5e/create/priorities">metatype</a>.';
        }

        if (isset($this->priorities['a'])) {
            $this->priority_method = self::PRIORITY_STANDARD;
            if (
                !isset($this->priorities['b'])
                || !isset($this->priorities['c'])
                || !isset($this->priorities['d'])
                || !isset($this->priorities['e'])
            ) {
                $errors[] = 'You must allocate all <a href="/characters/shadowrun5e/create/priorities">priorities</a>.';
            }
        } else {
            $this->priority_method = self::PRIORITY_SUM_TO_TEN;
            $sumToTen = 10;
            $priorities = [
                'metatypePriority',
                'magicPriority',
                'attributePriority',
                'skillPriority',
                'resourcePriority',
            ];
            foreach ($priorities as $priority) {
                if (!isset($this->priorities[$priority])) {
                    $errors[] = 'You must allocate the '
                        . str_replace('P', ' p', $priority)
                        . ' on the <a href="/characters/shadowrun5e/create/priorities">'
                        . 'priorities page</a>.';
                    continue;
                }
                switch ($this->priorities[$priority]) {
                    case 'E':
                        // E priority is worth zero.
                        break;
                    case 'D':
                        $sumToTen -= 1;
                        break;
                    case 'C':
                        $sumToTen -= 2;
                        break;
                    case 'B':
                        $sumToTen -= 3;
                        break;
                    case 'A':
                        $sumToTen -= 4;
                        break;
                }
            }
            if ($sumToTen > 0) {
                $errors[] = 'You haven\'t allocated all sum-to-ten priority points.';
            } elseif ($sumToTen < 0) {
                $errors[] = 'You have allocated too many sum-to-ten priority points.';
            }
        }
        return $errors;
    }

    /**
     * @return array<int, string>
     */
    protected function validateNativeLanguage(): array
    {
        $nativeLanguages = 0;
        /** @var KnowledgeSkill $knowledge */
        foreach ($this->getKnowledgeSkills() as $knowledge) {
            if ('language' !== $knowledge->category) {
                continue;
            }
            if ('N' !== $knowledge->level) {
                continue;
            }
            $nativeLanguages++;
        }
        $bilingual = false;
        foreach ($this->getQualities() as $quality) {
            if ('bilingual' === $quality->id) {
                $bilingual = true;
                break;
            }
        }

        if ($bilingual && 2 !== $nativeLanguages) {
            return ['You haven\'t chosen two native languages for your bilingual quality'];
        }

        if (0 === $nativeLanguages) {
            return ['You must choose a native language'];
        }

        if (!$bilingual && 1 !== $nativeLanguages) {
            return ['You can only have one native language'];
        }
        return [];
    }

    /**
     * @return array<int, string>
     */
    protected function validateAttributes(): array
    {
        if (!is_array($this->priorities)) {
            return [];
        }
        $errors = [];
        $attributePoints = 0;
        if (self::PRIORITY_STANDARD === $this->priority_method) {
            switch (array_search('attributes', $this->priorities, true)) {
                case 'a':
                    $attributePoints = 24;
                    break;
                case 'b':
                    $attributePoints = 20;
                    break;
                case 'c':
                    $attributePoints = 16;
                    break;
                case 'd':
                    $attributePoints = 14;
                    break;
                case 'e':
                    $attributePoints = 12;
                    break;
            }
        } else {
            switch ($this->priorities['attributePriority']) {
                case 'A':
                    $attributePoints = 24;
                    break;
                case 'B':
                    $attributePoints = 20;
                    break;
                case 'C':
                    $attributePoints = 16;
                    break;
                case 'D':
                    $attributePoints = 14;
                    break;
                case 'E':
                    $attributePoints = 12;
                    break;
            }
        }

        $attributePoints = $attributePoints - $this->body - $this->agility
            - $this->reaction - $this->strength - $this->willpower
            - $this->logic - $this->intuition - $this->charisma;
        if (0 < $attributePoints) {
            $errors[] = 'You have unspent attribute points';
        }
        return $errors;
    }
}
