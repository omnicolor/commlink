<?php

declare(strict_types=1);

namespace Modules\Cyberpunkred\Models;

use RuntimeException;
use Stringable;

use function array_key_exists;
use function config;
use function sprintf;
use function strtolower;

/**
 * Class representing a Cyberpunk Red skill.
 * @property string $id
 * @psalm-suppress PossiblyUnusedProperty
 */
class Skill implements Stringable
{
    /**
     * Attribute attached to the skill.
     */
    public string $attribute;

    /**
     * Category for the skill.
     * @psalm-suppress PossiblyUnusedProperty
     */
    public string $category;

    /**
     * Description of the skill.
     * @psalm-suppress PossiblyUnusedProperty
     */
    public string $description;

    /**
     * Longer example of the skill.
     * @psalm-suppress PossiblyUnusedProperty
     */
    public string $examples;

    /**
     * Name of the skill.
     */
    public string $name;

    /**
     * Page the skill was introduced in.
     * @psalm-suppress PossiblyUnusedProperty
     */
    public int $page;

    /**
     * List of all skills.
     * @var ?array<mixed>
     */
    public static ?array $skills = null;

    /**
     * @throws RuntimeException If the skill isn't valid
     */
    public function __construct(public string $id, public int $level = 0)
    {
        $filename = config('cyberpunkred.data_path') . 'skills.php';
        /** @psalm-suppress UnresolvableInclude */
        self::$skills ??= require $filename;

        $id = strtolower($id);
        if (!isset(self::$skills[$id])) {
            throw new RuntimeException(sprintf(
                'Skill ID "%s" is invalid',
                $id
            ));
        }

        $skill = self::$skills[$id];
        $this->attribute = $skill['attribute'];
        $this->category = $skill['category'];
        $this->description = $skill['description'];
        $this->examples = $skill['examples'];
        $this->name = $skill['name'];
        $this->page = $skill['page'];
    }

    public function __toString(): string
    {
        return $this->name;
    }

    /**
     * Return the number of dice the character rolls for the skill.
     * @psalm-suppress PossiblyUnusedMethod
     */
    public function getBase(Character $character): int
    {
        // @phpstan-ignore-next-line
        return (int)($this->level + $character->{$this->attribute});
    }

    /**
     * Return the shortened version of a skill's attribute.
     * @psalm-suppress PossiblyUnusedMethod
     */
    public function getShortAttribute(): string
    {
        $attributes = [
            'body' => 'BOD',
            'cool' => 'COOL',
            'dexterity' => 'DEX',
            'empathy' => 'EMP',
            'intelligence' => 'INT',
            'reflexes' => 'REF',
            'technique' => 'TECH',
            'willpower' => 'WILL',
        ];
        if (!array_key_exists($this->attribute, $attributes)) {
            return $this->attribute;
        }
        return $attributes[$this->attribute];
    }
}
