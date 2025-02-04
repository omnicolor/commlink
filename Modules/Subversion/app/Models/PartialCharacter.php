<?php

declare(strict_types=1);

namespace Modules\Subversion\Models;

use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\Factory;
use Modules\Subversion\Database\Factories\PartialCharacterFactory;
use Override;
use Stringable;

use function count;

/**
 * Representation of a character in character generation.
 * @method static self create(array<mixed, mixed> $attributes)
 * @property-read int $fortune
 * @property-read int $relation_fortune
 * @property-read array<int, Relation> $relations
 * @property-write array<int, array<string, mixed>|Relation> $relations
 */
class PartialCharacter extends Character implements Stringable
{
    public const int STARTING_FORTUNE = 320;
    public const int STARTING_RELATION_FORTUNE = 30;
    public const int CORRUPTED_VALUE_FORTUNE = 5;

    protected $connection = 'mongodb';

    /**
     * @var list<string>
     */
    protected $fillable = [
        'agility',
        'arts',
        'awareness',
        'background',
        'brawn',
        'campaign_id',
        'caste',
        'charisma',
        'corrupted_value',
        'dulled',
        'gear',
        'ideology',
        'impulse',
        'languages',
        'lineage',
        'lineage_option',
        'name',
        'origin',
        'owner',
        'relations',
        'skills',
        'system',
        'values',
        'will',
        'wit',
    ];

    protected $table = 'characters-partial';

    #[Override]
    protected static function newFactory(): Factory
    {
        return PartialCharacterFactory::new();
    }

    #[Override]
    public function newFromBuilder(
        $attributes = [],
        $connection = null,
    ): PartialCharacter {
        $character = new self($attributes);
        $character->exists = true;
        $character->setRawAttributes($attributes, true);
        $character->setConnection($this->connection);
        $character->fireModelEvent('retrieved', false);
        // @phpstan-ignore return.type
        return $character;
    }

    public function fortune(): Attribute
    {
        return Attribute::make(
            get: function (): int {
                $fortune = self::STARTING_FORTUNE;
                if (null !== $this->caste) {
                    $fortune += $this->caste->fortune;
                }
                if (true === $this->corrupted_value) {
                    $fortune += self::CORRUPTED_VALUE_FORTUNE;
                }
                $relationFortune = $this->relation_fortune;
                if (0 > $relationFortune) {
                    $fortune += $relationFortune;
                }
                return $fortune;
            },
        );
    }

    public function relationFortune(): Attribute
    {
        return Attribute::make(
            get: function (): int {
                $fortune = self::STARTING_RELATION_FORTUNE;
                foreach ($this->attributes['relations'] ?? [] as $relation) {
                    $cost = (new RelationLevel($relation['level']))->cost;
                    $cost += ($relation['increase_power'] ?? 0) * 5;
                    $cost += ($relation['increase_regard'] ?? 0) * 2;
                    $fortune -= $cost;
                }
                return $fortune;
            },
        );
    }

    public function relations(): Attribute
    {
        return Attribute::make(
            /**
             * @return array<int, Relation>
             */
            get: function (): array {
                $relations = [];
                foreach ($this->attributes['relations'] ?? [] as $relation) {
                    $relations[] = Relation::fromArray($relation);
                }
                return $relations;
            },
            set: function (array $relations): array {
                if (0 < count($relations) && $relations[0] instanceof Relation) {
                    foreach ($relations as $key => $relation) {
                        $relations[$key] = $relation->toArray();
                    }
                }
                $this->attributes['relations'] = $relations;
                return ['relations' => $relations];
            },
        );
    }
}
