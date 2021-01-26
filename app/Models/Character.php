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
 * @property string $type
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
        'type',
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
}
