<?php

declare(strict_types=1);

namespace Modules\Shadowrun6e\Models;

use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Model;
use Modules\Shadowrun6e\Enums\SpecializationLevel;
use Modules\Shadowrun6e\ValueObjects\SkillSpecialization;
use Override;
use RangeException;
use Stringable;
use Sushi\Sushi;

use function array_walk;
use function config;
use function json_decode;

use const JSON_THROW_ON_ERROR;

/**
 * Representation of a Shadowrun sixth edition skill.
 * @property string $attribute
 * @property array<int, string> $attributes_secondary
 * @property string $description
 * @property array<int, string> $example_specializations
 * @property int $level
 * @property string $name
 * @property int $page
 * @property-read array<int, SkillSpecialization> $specializations
 * @property-write array<int, SkillSpecialization|array{name: string, level?: int}> $specializations
 * @property bool $untrained
 */
class ActiveSkill extends Model implements Stringable
{
    use Sushi;

    private int $level = 0;
    /** @var array<int, SkillSpecialization> */
    private array $specializations = [];

    public $incrementing = false;
    protected $keyType = 'string';

    /**
     * @var list<string>
     */
    protected $fillable = [
        'attribute',
        'attributes_secondary',
        'description',
        'id',
        'name',
        'page',
        'untrained',
    ];

    #[Override]
    public function __toString(): string
    {
        return $this->name;
    }

    protected function attributesSecondary(): Attribute
    {
        return Attribute::make(
            get: static function (string|null $attributes): array|null {
                if (null === $attributes) {
                    return null;
                }
                return json_decode(
                    json: $attributes,
                    associative: true,
                    flags: JSON_THROW_ON_ERROR,
                );
            },
        );
    }

    #[Override]
    protected function casts(): array
    {
        return [
            'attribute' => 'string',
            'description' => 'string',
            'id' => 'string',
            'name' => 'string',
            'page' => 'integer',
            'untrained' => 'bool',
        ];
    }

    protected function exampleSpecializations(): Attribute
    {
        return Attribute::make(
            get: static function (string $example_specializations): array {
                return json_decode(
                    json: $example_specializations,
                    associative: true,
                    flags: JSON_THROW_ON_ERROR,
                );
            },
        );
    }

    /**
     * @return array{
     *     attribute: string,
     *     attributes_secondary: string,
     *     description: string,
     *     id: string,
     *     name: string,
     *     page: int,
     *     example_specializations: string,
     *     untrained: bool
     * }
     */
    public function getRows(): array
    {
        $filename = config('shadowrun6e.data_path') . 'skills.php';
        return require $filename;
    }

    protected function level(): Attribute
    {
        return Attribute::make(
            get: function (): int {
                return $this->level;
            },
            set: function (int|null $level): int {
                if (0 >= $level) {
                    throw new RangeException('Level must be greater than zero');
                }
                if (10 <= $level) {
                    throw new RangeException('Level must be less than ten');
                }
                $this->level = $level;
                return $level;
            }
        );
    }

    /**
     * @param array{id: string, level?: int, specializations?: array<int, array{name: string, level?: int}>} $raw_skill
     */
    public static function make(array $raw_skill): self
    {
        /** @var self $skill */
        $skill = self::findOrFail($raw_skill['id']);
        $skill->level = $raw_skill['level'] ?? 0;
        if (!isset($raw_skill['specializations'])) {
            return $skill;
        }
        $specializations = [];
        foreach ($raw_skill['specializations'] as $specialization) {
            $specializations[] = new SkillSpecialization(
                $specialization['name'],
                SpecializationLevel::tryFrom($specialization['level'] ?? 0),
            );
        }
        $skill->specializations = $specializations;
        return $skill;
    }

    protected function specializations(): Attribute
    {
        return Attribute::make(
            get: function (): array {
                return $this->specializations;
            },
            set: function (array $specializations): array {
                array_walk(
                    $specializations,
                    static function (SkillSpecialization|array &$specialization): void {
                        if ($specialization instanceof SkillSpecialization) {
                            return;
                        }
                        $specialization = new SkillSpecialization(
                            $specialization['name'],
                            SpecializationLevel::tryFrom($specialization['level'] ?? 0),
                        );
                    },
                );
                $this->specializations = $specializations;
                return $specializations;
            },
        );
    }
}
