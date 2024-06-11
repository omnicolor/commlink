<?php

declare(strict_types=1);

namespace App\Models\StarTrekAdventures;

use App\Models\Character as BaseCharacter;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Support\Facades\Log;
use RuntimeException;

/**
 * @property-read string $id
 * @property-read Attributes $attributes
 * @property-read Disciplines $disciplines
 * @property array<int, string> $focuses
 * @property-read Species $species
 * @property-read int $stress
 * @property-read TalentArray $talents
 */
class Character extends BaseCharacter
{
    use HasFactory;

    /**
     * @var array<string, mixed>
     */
    protected $attributes = [
        'system' => 'star-trek-adventures',
    ];

    protected Attributes $attributesObject;
    protected Disciplines $disciplinesObject;

    /**
     * @var array<int, string>
     */
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

    /**
     * @var array<int, string>
     */
    protected $hidden = [
        '_id',
    ];

    public function __toString(): string
    {
        return $this->name ?? 'Unnamed Character';
    }

    /**
     * Force this model to only load for Star Trek Adventure characters.
     */
    protected static function booted(): void
    {
        static::addGlobalScope(
            'star-trek-adventures',
            function (Builder $builder): void {
                $builder->where('system', 'star-trek-adventures');
            }
        );
    }

    public function attributes(): Attribute
    {
        return Attribute::make(
            get: function (): Attributes {
                if (!isset($this->attributesObject)) {
                    $this->attributesObject = new Attributes(
                        // @phpstan-ignore-next-line
                        $this->attributes['attributes']
                    );
                }
                return $this->attributesObject;
            },
        );
    }

    public function disciplines(): Attribute
    {
        return Attribute::make(
            get: function (): Disciplines {
                if (!isset($this->disciplines)) {
                    $this->disciplinesObject = new Disciplines(
                        // @phpstan-ignore-next-line
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
                    // @phpstan-ignore-next-line
                    !isset($this->attributes['focuses'])
                    || !is_array($this->attributes['focuses'])
                ) {
                    return [];
                }
                return $this->attributes['focuses'];
            },
        );
    }

    public function species(): Attribute
    {
        return Attribute::make(
            get: function (): Species {
                // @phpstan-ignore-next-line
                return Species::find($this->attributes['species']);
            },
        );
    }

    public function stress(): Attribute
    {
        return Attribute::make(
            get: function (): int {
                if (!isset($this->attributesObject)) {
                    $this->attributesObject = new Attributes(
                        // @phpstan-ignore-next-line
                        $this->attributes['attributes']
                    );
                }
                if (!isset($this->disciplinesObject)) {
                    $this->disciplinesObject = new Disciplines(
                        // @phpstan-ignore-next-line
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
                // @phpstan-ignore-next-line
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
}
