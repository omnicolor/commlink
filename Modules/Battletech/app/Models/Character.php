<?php

declare(strict_types=1);

namespace Modules\Battletech\Models;

use App\Models\Character as BaseCharacter;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Casts\Attribute as EloquentAttribute;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Support\Facades\Log;
use Modules\Battletech\Database\Factories\CharacterFactory;
use Modules\Battletech\Enums\ExperienceItemType;
use Modules\Battletech\ValueObjects\Attribute;
use Modules\Battletech\ValueObjects\Attributes;
use Override;
use RuntimeException;
use Stringable;

/**
 * @phpstan-import-type AppearanceArray from Appearance
 * @phpstan-import-type AttributesArray from Attributes
 * @phpstan-import-type AttributesArray from Attributes
 * @property string $affiliation
 * @property-read Appearance $appearance
 * @property-write Appearance|AppearanceArray $appearance
 * @property-read Attributes $attributes
 * @property-write Attributes|AttributesArray $attributes
 * @property-read int $experience
 * @property-read ExperienceLog $experience_log
 * @property string $name
 * @property-read array<int, Skill> $skills
 * @property-read array<int, Quality> $traits
 */
class Character extends BaseCharacter implements Stringable
{
    use HasFactory;

    /** @var array<string, string> */
    protected $attributes = [
        'system' => 'battletech',
    ];

    /** @var list<string> */
    protected $fillable = [
        'affiliation',
        'appearance',
        'attributes',
        'experience_log',
        'money',
        'name',
        'skills',
        'traits',
    ];

    /** @var list<string> */
    protected $hidden = [
        '_id',
    ];

    #[Override]
    public function __toString(): string
    {
        return $this->name ?? 'Unnamed Mechwarrior';
    }

    protected function appearance(): EloquentAttribute
    {
        return EloquentAttribute::make(
            get: function (array|null $values): Appearance {
                return Appearance::make($values);
            },
            set: function (array|Appearance $appearance): array {
                if ($appearance instanceof Appearance) {
                    return ['appearance' => $appearance->toArray()];
                }
                return ['appearance' => $appearance];
            },
        );
    }

    protected function attributes(): EloquentAttribute
    {
        return EloquentAttribute::make(
            get: function (array|null $values): Attributes {
                if (null === $values) {
                    return new Attributes(
                        new Attribute(1),
                        new Attribute(1),
                        new Attribute(1),
                        new Attribute(1),
                        new Attribute(1),
                        new Attribute(1),
                        new Attribute(1),
                        new Attribute(1),
                    );
                }
                // @phpstan-ignore argument.type
                return Attributes::make($values);
            },
        );
    }

    /**
     * Force this model to only load for Battletech characters.
     */
    #[Override]
    protected static function booted(): void
    {
        static::addGlobalScope(
            'battletech',
            function (Builder $builder): void {
                $builder->where('system', 'battletech');
            }
        );
    }

    protected function experienceLog(): EloquentAttribute
    {
        return EloquentAttribute::make(
            get: function (array|null $values): ExperienceLog {
                $log = ExperienceLog::empty();
                if (null === $values) {
                    return $log;
                }

                foreach ($values as $item) {
                    $log[] = new ExperienceItem(
                        $item['amount'],
                        $item['type'] instanceof ExperienceItemType
                            ? $item['type']
                            : ExperienceItemType::from($item['type']),
                        $item['name'],
                    );
                }
                return $log;
            },
        );
    }

    protected function experience(): EloquentAttribute
    {
        return EloquentAttribute::make(
            get: function (): int {
                return $this->experience_log->total();
            },
        );
    }

    public static function fromPregen(string $id): self
    {
        $filename = config('battletech.data_path') . 'pregens.php';
        $pregens = require $filename;

        if (!isset($pregens[$id])) {
            throw new RuntimeException('Pregen ID not found: ' . $id);
        }

        return new self($pregens[$id]);
    }

    #[Override]
    protected static function newFactory(): Factory
    {
        return CharacterFactory::new();
    }

    protected function skills(): EloquentAttribute
    {
        return EloquentAttribute::make(
            get: function (array|null $skills): array {
                if (null === $skills) {
                    return [];
                }

                foreach ($skills as $key => $raw_skill) {
                    try {
                        /** @var Skill */
                        $skill = Skill::findOrFail($raw_skill['id']);
                    } catch (ModelNotFoundException) {
                        Log::warning(
                            'Battletech character "{name}" ({id}) has invalid skill "{skill}"',
                            [
                                'name' => $this->name,
                                'id' => $this->id,
                                'skill' => $raw_skill,
                            ]
                        );
                        unset($skills[$key]);
                        continue;
                    }
                    $skill->level = $raw_skill['level'] ?? null;
                    $skill->specialty = $raw_skill['specialty'] ?? null;
                    $skills[$key] = $skill;
                }
                return $skills;
            },
        );
    }

    protected function traits(): EloquentAttribute
    {
        return EloquentAttribute::make(
            get: function (array|null $traits): array {
                if (null === $traits) {
                    return [];
                }

                foreach ($traits as $key => $trait) {
                    try {
                        $traits[$key] = Quality::findOrFail($trait);
                    } catch (ModelNotFoundException) {
                        Log::warning(
                            'Battletech character "{name}" ({id}) has invalid trait "{trait}"',
                            [
                                'name' => $this->name,
                                'id' => $this->id,
                                'trait' => $trait,
                            ]
                        );
                        unset($traits[$key]);
                    }
                }
                return $traits;
            },
        );
    }
}
