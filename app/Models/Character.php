<?php

declare(strict_types=1);

namespace App\Models;

use App\Models\User;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Jenssegers\Mongodb\Eloquent\Model;

/**
 * Generic model representing a role playing character.
 * @property string $handle
 * @property string $owner
 * @property string $system
 */
class Character extends Model
{
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
        'handle',
        'owner',
        'system',
    ];

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
        switch ($attributes['system']) {
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
