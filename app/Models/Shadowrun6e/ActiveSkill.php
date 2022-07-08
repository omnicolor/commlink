<?php

declare(strict_types=1);

namespace App\Models\Shadowrun6e;

use RuntimeException;

/**
 * Representation of a Shadowrun sixth edition skill.
 */
class ActiveSkill
{
    public string $attribute;
    public string $attribute_secondary;
    public string $description;
    public string $id;
    public string $name;
    public int $page;
    public bool $untrained;

    /**
     * @var array<int, string>
     */
    public array $specialization_examples;

    /**
     * @var array<string, array<string, array<int, string>|bool|int|string>>
     */
    public static array $skills;

    /**
     * Constructor.
     * @param string $id
     * @param int $level
     * @param ?string $specialization
     * @throws RuntimeException
     */
    public function __construct(
        string $id,
        public int $level = 1,
        public ?string $specialization = null
    ) {
        $filename = config('app.data_path.shadowrun6e') . 'skills.php';
        self::$skills ??= require $filename;

        $this->id = \strtolower($id);
        if (!isset(self::$skills[$id])) {
            throw new RuntimeException(\sprintf(
                'Shadowrun 6E skill ID "%s" is invalid',
                $id
            ));
        }

        $skill = self::$skills[$id];
        $this->attribute = $skill['attribute'];
        $this->attribute_secondary = $skill['attributeSecondary'];
        $this->description = $skill['description'];
        $this->name = $skill['name'];
        $this->page = $skill['page'];
        $this->specialization_examples = $skill['specializations'];
        $this->untrained = $skill['untrained'];
    }

    /**
     * Return the name of the skill.
     * @return string
     */
    public function __toString(): string
    {
        return $this->name;
    }
}
