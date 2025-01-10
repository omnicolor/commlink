<?php

declare(strict_types=1);

namespace App\Http\Resources;

use App\Models\Event;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\ResourceCollection;
use Illuminate\Support\Collection;

class EventCollection extends ResourceCollection
{
    /**
     * @return array{
     *     data: Collection<Event>,
     *     links: array{
     *         collection: string
     *     }
     * }
     */
    public function toArray(Request $request): array
    {
        return [
            'data' => $this->collection,
            'links' => [
                'collection' => route('events.index'),
            ],
        ];
    }
}
