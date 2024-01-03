<?php

declare(strict_types=1);

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\ResourceCollection;
use Illuminate\Support\Collection;

class EventCollection extends ResourceCollection
{
    /**
     * @return array<string, array<string, string>|Collection>>
     */
    public function toArray(Request $request): array
    {
        return [
            'data' => $this->collection,
            'links' => [
                'collection' => '/events',
                'root' => '/',
            ],
        ];
    }
}
