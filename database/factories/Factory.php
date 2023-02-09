<?php

declare(strict_types=1);

namespace Database\Factories;

use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory as BaseFactory;
use Illuminate\Database\QueryException;

/**
 * @psalm-suppress MissingTemplateParam
 * @template T
 * @extends Factory<T>
 */
abstract class Factory extends BaseFactory
{
    protected function createUser(): User
    {
        while (true) {
            try {
                /** @var User */
                return User::factory()->create();
            } catch (QueryException) {
            }
        }
    }
}
