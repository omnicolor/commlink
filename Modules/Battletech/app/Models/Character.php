<?php

declare(strict_types=1);

namespace Modules\Battletech\Models;

use App\Models\Character as BaseCharacter;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Casts\Attribute as EloquentAttribute;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Modules\Battletech\Database\Factories\CharacterFactory;
use Modules\Battletech\ValueObjects\Attribute;
use Modules\Battletech\ValueObjects\Attributes;
use Override;
use Stringable;

/**
 * @phpstan-import-type AppearanceArray from Appearance
 * @phpstan-import-type AttributesArray from Attributes
 * @property string $affiliation
 * @property-read Appearance $appearance
 * @property-write Appearance|AppearanceArray $appearance
 * @property-read Attributes $attributes
 * @property-write Attributes|AttributesArray $attributes
 * @property string $name
 */
class Character extends BaseCharacter implements Stringable
{
    use HasFactory;

    /** @var array<string, mixed> */
    protected $attributes = [
        'system' => 'battletech',
    ];

    /** @var list<string> */
    protected $fillable = [
        'affiliation',
        'appearance',
        'attributes',
        'name',
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

    #[Override]
    protected static function newFactory(): Factory
    {
        return CharacterFactory::new();
    }
}
