<?php

declare(strict_types=1);

namespace App\Models;

use App\Models\Traits\GameSystem;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use MongoDB\Laravel\Eloquent\Model;

/**
 * Generic model representing a role playing character.
 * @method ?Campaign campaign()
 * @method ?Character find()
 * @method string getSystem()
 * @property ?int $campaign_id
 * @property string $created_at
 * @property ?string $handle
 * @property ?string $name
 * @property string $owner
 * @property string $system
 * @property string $updated_at
 */
class Character extends Model
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
     * @psalm-suppress InvalidReturnStatement
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
     * @psalm-suppress PossiblyUnusedMethod
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
     * @psalm-suppress LessSpecificImplementedReturnType
     */
    public function newFromBuilder(
        $attributes = [],
        $connection = null,
    ): Character {
        $character = match ($attributes['system'] ?? null) {
            'avatar' => new Avatar\Character($attributes),
            'capers' => new Capers\Character($attributes),
            'cyberpunkred' => new Cyberpunkred\Character($attributes),
            'dnd5e' => new Dnd5e\Character($attributes),
            'expanse' => new Expanse\Character($attributes),
            'shadowrun5e' => new Shadowrun5e\Character($attributes),
            'shadowrun6e' => new Shadowrun6e\Character($attributes),
            'star-trek-adventures' => new StarTrekAdventures\Character($attributes),
            'stillfleet' => new Stillfleet\Character($attributes),
            default => new Character($attributes),
        };
        $character->exists = true;
        $character->setRawAttributes($attributes, true);
        $character->setConnection($this->connection);
        $character->fireModelEvent('retrieved', false);
        // @phpstan-ignore-next-line
        return $character;
    }
}
