<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Jenssegers\Mongodb\Eloquent\Model;

class Character extends Model
{
    use HasFactory;

    /**
     * The database connection that should be used by the model.
     * @var string
     */
    protected $connection = 'mongodb';
}
