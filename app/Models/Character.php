<?php

declare(strict_types=1);

namespace App\Models;

use App\Models\Traits\GameSystem;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use MongoDB\Laravel\Eloquent\Model;
use Nwidart\Modules\Facades\Module;
use Stringable;

use function class_exists;
use function ucfirst;

/**
 * Generic model representing a role playing character.
 * @method ?Campaign campaign()
 * @method static int count()
 * @method ?Character find(string $id)
 * @method string getSystem()
 * @method static Builder where(string $field, mixed $search)
 * @mixin Model
 * @property ?int $campaign_id
 * @property string $created_at
 * @property ?string $handle
 * @property string $id
 * @property ?string $name
 * @property string $owner
 * @property string $system
 * @property string $updated_at
 */
class Character extends Model implements Stringable
{
    use GameSystem;
    use HasFactory;

    /**
     * The database connection that should be used by the model.
     * @var ?string
     */
    protected $connection = 'mongodb';

    /**
     * The attributes that are mass assignable.
     * @var array<int, string>
     */
    protected $fillable = [
        'campaign_id',
        'handle',
        'name',
        'owner',
        'system',
    ];

    public function __toString(): string
    {
        return $this->handle ?? $this->name ?? '';
    }

    /**
     * Returns the campaign the character is playing in (if any).
     *
     * Note: This is effectively a BelongsTo relationship, but that doesn't seem
     * to work between MySQL and MongoDB.
     */
    public function campaign(): ?Campaign
    {
        if (!isset($this->campaign_id)) {
            return null;
        }

        return Campaign::find($this->campaign_id);
    }

    /**
     * Return the user that owns the character.
     * @throws ModelNotFoundException
     */
    public function user(): User
    {
        return User::where('email', $this->owner)->firstOrFail();
    }

    /**
     * Create a new Character, subclassed if available.
     * @param array<int|string, mixed> $attributes
     * @param ?string $connection
     */
    public function newFromBuilder(
        $attributes = [],
        $connection = null,
    ): Character {
        $class = 'Modules\\' . ucfirst($attributes['system'])
            . '\\Models\\Character';
        if (
            null !== Module::find($attributes['system'])
            && Module::isEnabled($attributes['system'])
            && class_exists($class)
        ) {
            /** @var Character */
            $character = new $class($attributes);
        } else {
            $character = new Character($attributes);
        }
        $character->exists = true;
        $character->setRawAttributes($attributes, true);
        $character->setConnection($this->connection);
        $character->fireModelEvent('retrieved', false);
        // @phpstan-ignore return.type
        return $character;
    }
}
