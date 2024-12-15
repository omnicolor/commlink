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
 */
class Skill implements Stringable
{
    /**
     * Attribute attached to the skill.
     */
    public string $attribute;

    /**
     * Category for the skill.
     */
    public string $category;

    /**
     * Description of the skill.
     */
    public string $description;

    /**
     * Longer example of the skill.
     */
    public string $examples;

    /**
     * Name of the skill.
     */
    public string $name;

    /**
     * Page the skill was introduced in.
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
     */
    public function getBase(Character $character): int
    {
        return (int)($this->level + match ($this->attribute) {
            'body' => $character->body, // @codeCoverageIgnore
            'cool' => $character->cool, // @codeCoverageIgnore
            'dexterity' => $character->dexterity, // @codeCoverageIgnore
            'empathy' => $character->empathy, // @codeCoverageIgnore
            'intelligence' => $character->intelligence,
            'luck' => $character->luck, // @codeCoverageIgnore
            'movement' => $character->movement, // @codeCoverageIgnore
            'reflexes' => $character->reflexes, // @codeCoverageIgnore
            'technique' => $character->technique, // @codeCoverageIgnore
            'willpower' => $character->willpower,
            default => throw new RuntimeException('Invalid attribute for skill'),
        });
    }

    /**
     * Return the shortened version of a skill's attribute.
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
