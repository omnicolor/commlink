<?php

declare(strict_types=1);

namespace Modules\Startrekadventures\Models;

use App\Casts\AsEmail;
use App\Models\Character as BaseCharacter;
use App\ValueObjects\Email;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Support\Facades\Log;
use Modules\Startrekadventures\Database\Factories\CharacterFactory;
use Override;
use RuntimeException;
use Stringable;

use function is_array;

/**
 * @property string $assignment
 * @property-read Disciplines $disciplines
 * @property string $environment
 * @property array<int, string> $focuses
 * @property-read string $id
 * @property Email $owner
 * @property string $rank
 * @property-read Species $species
 * @property-read Attributes $stats
 * @property-read int $stress
 * @property-read TalentArray $talents
 * @property-read Traits $trait
 * @property string $upbringing
 * @property array<int, string> $values
 */
class Character extends BaseCharacter implements Stringable
{
    use HasFactory;

    /** @var array<string, mixed> */
    protected $attributes = [
        'system' => 'startrekadventures',
    ];

    /** @var array<string, string> */
    protected $casts = [
        'owner' => AsEmail::class,
    ];

    protected Attributes $attributesObject;
    protected Disciplines $disciplinesObject;

    /** @var list<string> */
    protected $fillable = [
        'assignment',
        'attributes',
        'disciplines',
        'environment',
        'focuses',
        'name',
        'owner',
        'rank',
        'species',
        'talents',
        'traits',
        'upbringing',
        'values',
    ];

    /** @var list<string> */
    protected $hidden = [
        '_id',
    ];

    #[Override]
    public function __toString(): string
    {
        return $this->name ?? 'Unnamed Character';
    }

    /**
     * Force this model to only load for Star Trek Adventure characters.
     */
    #[Override]
    protected static function booted(): void
    {
        static::addGlobalScope(
            'startrekadventures',
            function (Builder $builder): void {
                $builder->where('system', 'startrekadventures');
            }
        );
    }

    public function disciplines(): Attribute
    {
        return Attribute::make(
            get: function (): Disciplines {
                if (!isset($this->disciplines)) {
                    $this->disciplinesObject = new Disciplines(
                        $this->attributes['disciplines']
                    );
                }
                return $this->disciplinesObject;
            },
        );
    }

    public function focuses(): Attribute
    {
        return Attribute::make(
            get: function (): array {
                if (
                    !isset($this->attributes['focuses'])
                    || !is_array($this->attributes['focuses'])
                ) {
                    return [];
                }
                return $this->attributes['focuses'];
            },
        );
    }

    #[Override]
    protected static function newFactory(): Factory
    {
        return CharacterFactory::new();
    }

    public function species(): Attribute
    {
        return Attribute::make(
            get: function (): Species {
                return Species::find($this->attributes['species']);
            },
        );
    }

    public function stats(): Attribute
    {
        return Attribute::make(
            get: function (): Attributes {
                if (!isset($this->attributesObject)) {
                    $this->attributesObject = new Attributes(
                        $this->attributes['attributes']
                    );
                }
                return $this->attributesObject;
            },
        );
    }

    public function stress(): Attribute
    {
        return Attribute::make(
            get: function (): int {
                if (!isset($this->attributesObject)) {
                    $this->attributesObject = new Attributes(
                        $this->attributes['attributes']
                    );
                }
                if (!isset($this->disciplinesObject)) {
                    $this->disciplinesObject = new Disciplines(
                        $this->attributes['disciplines']
                    );
                }
                return $this->attributesObject->fitness
                    + $this->disciplinesObject->security;
            },
        );
    }

    public function talents(): Attribute
    {
        return Attribute::make(
            get: function (): TalentArray {
                $talents = new TalentArray();
                foreach ($this->attributes['talents'] ?? [] as $talent) {
                    try {
                        $talents[] = new Talent(
                            $talent['id'],
                            $talent['extra'] ?? null
                        );
                    } catch (RuntimeException) {
                        Log::warning(
                            'Star Wars Adventures character "{name}" ({id}) '
                                . 'has invalid talent "{talent}"',
                            [
                                'name' => $this->name,
                                'id' => $this->id,
                                'talent' => $talent['id'],
                            ]
                        );
                    }
                }
                return $talents;
            },
        );
    }

    public function trait(): Attribute
    {
        return Attribute::make(
            get: function (): Traits {
                return new Traits($this->attributes['traits']);
            },
        );
    }
}
