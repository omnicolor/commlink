<?php

declare(strict_types=1);

namespace App\Http\Controllers\Fakes;

use App\Http\Controllers\Controller;
use App\Http\Requests\Fakes\NamesRequest;
use Faker\Factory as Faker;
use Illuminate\Http\JsonResponse;

class NamesController extends Controller
{
    public function __invoke(NamesRequest $request): JsonResponse
    {
        $faker = Faker::create();
        $names = [];
        for ($i = (int)$request->query('quantity', '5'); $i > 0; $i--) {
            $names[] = $faker->name;
        }
        return new JsonResponse([
            'data' => $names,
            'links' => [
                'self' => route('fakes.names'),
                'root' => '/',
            ],
        ]);
    }
}
