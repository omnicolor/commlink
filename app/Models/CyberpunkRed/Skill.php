<?php

declare(strict_types=1);

namespace App\Models\CyberpunkRed;

/**
 * Class representing a Cyberpunk Red skill.
 */
class Skill
{
    /**
     * Attribute attached to the skill.
     * @var string
     */
    public string $attribute;

    /**
     * Category for the skill.
     * @var string
     */
    public string $category;

    /**
     * Description of the skill.
     * @var string
     */
    public string $description;

    /**
     * Longer example of the skill.
     * @var string
     */
    public string $examples;

    /**
     * Unique ID for the skill.
     * @var string
     */
    public string $id;

    /**
     * Character's level in the skill.
     * @var int
     */
    public int $level;

    /**
     * Name of the skill.
     * @var string
     */
    public string $name;

    /**
     * Page the skill was introduced in.
     * @var int
     */
    public int $page;

    /**
     * List of all skills.
     * @var ?array<mixed>
     */
    public static ?array $skills;

    /**
     * Constructor.
     * @param string $id
     * @param int $level
     * @throws \RuntimeException If the skill isn't valid
     */
    public function __construct(string $id, int $level = 0)
    {
        $filename = config('app.data_path.cyberpunkred') . 'skills.php';
        self::$skills ??= require $filename;

        $id = strtolower($id);
        if (!isset(self::$skills[$id])) {
            throw new \RuntimeException(sprintf(
                'Skill ID "%s" is invalid',
                $id
            ));
        }

        $skill = self::$skills[$id];
        $this->attribute = $skill['attribute'];
        $this->category = $skill['category'];
        $this->description = $skill['description'];
        $this->examples = $skill['examples'];
        $this->level = $level;
        $this->id = $id;
        $this->name = $skill['name'];
        $this->page = $skill['page'];
    }

    /**
     * Return the skill's name.
     * @return string
     */
    public function __toString(): string
    {
        return $this->name;
    }

    /**
     * Return the number of dice the character rolls for the skill.
     * @param Character $character
     * @return int
     */
    public function getBase(Character $character): int
    {
        // @phpstan-ignore-next-line
        return (int)($this->level + $character->{$this->attribute});
    }
}
