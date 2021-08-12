<?php

declare(strict_types=1);

namespace App\Models;

use App\Models\Traits\GameSystem;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Jenssegers\Mongodb\Eloquent\Model;

/**
 * Generic model representing a role playing character.
 * @property int $campaign_id
 * @property string $created_at
 * @property string $handle
 * @property string $name
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
     * @var string
     */
    protected $connection = 'mongodb';

    /**
     * The attributes that are mass assignable.
     * @var string[]
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
     * @return ?Campaign
     */
    public function campaign(): ?Campaign
    {
        if (!$this->campaign_id) {
            return null;
        }
        return Campaign::find($this->campaign_id);
    }

    /**
     * Return the user that owns the character.
     * @throws \Illuminate\Database\Eloquent\ModelNotFoundException
     * @return User
     */
    public function user(): User
    {
        return User::where('email', $this->owner)->firstOrFail();
    }

    /**
     * Create a new Character, subclassed if available.
     * @param array<mixed, mixed> $attributes
     * @param ?string $connection
     * @return static(\Illuminate\Database\Eloquent\Model)
     */
    public function newFromBuilder(
        $attributes = [],
        $connection = null
    ): Character {
        switch ($attributes['system'] ?? null) {
            case 'cyberpunkred':
                $character = new CyberpunkRed\Character($attributes);
                break;
            case 'dnd5e':
                $character = new Dnd5e\Character($attributes);
                break;
            case 'expanse':
                $character = new Expanse\Character($attributes);
                break;
            case 'shadowrun5e':
                $character = new Shadowrun5E\Character($attributes);
                break;
            default:
                $character = new Character($attributes);
                break;
        }
        $character->exists = true;
        $character->setRawAttributes($attributes, true);
        $character->setConnection($connection);
        $character->fireModelEvent('retrieved', false);
        // @phpstan-ignore-next-line
        return $character;
    }
}
