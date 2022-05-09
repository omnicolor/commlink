<?php

declare(strict_types=1);

namespace App\Models\StarTrekAdventures;

use App\Models\Character as BaseCharacter;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use RuntimeException;

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
    protected Disciplines $disciplines;

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

    public function getAttributesAttribute(): Attributes
    {
        if (!isset($this->attributesObject)) {
            $this->attributesObject = new Attributes(
                $this->attributes['attributes']
            );
        }
        return $this->attributesObject;
    }

    public function getDisciplinesAttribute(): Disciplines
    {
        if (!isset($this->disciplines)) {
            $this->disciplines = new Disciplines(
                $this->attributes['disciplines']
            );
        }
        return $this->disciplines;
    }

    public function getSpeciesAttribute(): Species
    {
        return Species::find($this->attributes['species']);
    }

    public function getStressAttribute(): int
    {
        return $this->getAttributesAttribute()->fitness
            + $this->getDisciplinesAttribute()->security;
    }

    public function getTalentsAttribute(): TalentArray
    {
        $talents = new TalentArray();
        foreach ($this->attributes['talents'] ?? [] as $talent) {
            try {
                $talents[] = new Talent($talent['id'], $talent['extra'] ?? null);
            } catch (RuntimeException $ex) {
                \Log::warning(\sprintf(
                    'Star Wars Adventures character "%s" (%s) has invalid talent "%s"',
                    $this->name,
                    $this->id,
                    $talent['id']
                ));
            }
        }
        return $talents;
    }
}
