<?php

declare(strict_types=1);

namespace App\Models\Shadowrun6e;

use RuntimeException;
use Stringable;

use function config;
use function sprintf;
use function strtolower;

/**
 * Representation of a Shadowrun sixth edition skill.
 * @psalm-suppress UnusedClass
 */
class ActiveSkill implements Stringable
{
    public string $attribute;
    public string $attribute_secondary;
    public string $description;
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
     * @throws RuntimeException
     */
    public function __construct(
        public string $id,
        public int $level = 1,
        public ?string $specialization = null
    ) {
        $filename = config('app.data_path.shadowrun6e') . 'skills.php';
        self::$skills ??= require $filename;

        $id = strtolower($id);
        if (!isset(self::$skills[$id])) {
            throw new RuntimeException(sprintf(
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

    public function __toString(): string
    {
        return $this->name;
    }
}
