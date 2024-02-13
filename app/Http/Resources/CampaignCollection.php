<?php

declare(strict_types=1);

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\ResourceCollection;
use Illuminate\Support\Collection;

class CampaignCollection extends ResourceCollection
{
    /**
     * Transform the resource collection into an array.
     * @return array<string, array<int|string, mixed>|Collection>
     */
    public function toArray(Request $request): array
    {
        return [
            'data' => $this->collection,
            'links' => [
                'collection' => '/campaigns',
                'root' => '/',
            ],
        ];
    }
}
