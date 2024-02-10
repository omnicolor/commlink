<?php

declare(strict_types=1);

namespace Database\Factories;

use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory as BaseFactory;

/**
 * @psalm-suppress MissingTemplateParam
 * @template T
 * @extends Factory<T>
 */
abstract class Factory extends BaseFactory
{
    protected function createUser(): User
    {
        /** @var User */
        $user = User::factory()->create();
        return $user;
    }
}
