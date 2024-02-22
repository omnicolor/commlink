<?php

declare(strict_types=1);

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource as BaseResource;
use Illuminate\Support\Str;

use function is_array;

class JsonResource extends BaseResource
{
    /**
     * Given an array meant for output from an API, fix the keys to be snake
     * case.
     * @param array<mixed, mixed> $array
     * @return array<mixed, mixed>
     */
    protected function convertKeys(array &$array): array
    {
        foreach ($array as $key => $item) {
            if (is_array($item)) {
                $array[$key] = $this->convertKeys($item);
            }

            $snake = Str::snake((string)$key);
            if ($snake !== $key) {
                $array[$snake] = $item;
                unset($array[$key]);
            }
        }
        return $array;
    }
}
