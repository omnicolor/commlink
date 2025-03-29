<?php

declare(strict_types=1);

namespace App\Http\Resources;

use App\Models\Campaign;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\ResourceCollection;
use Illuminate\Support\Collection;
use Override;

use function route;

final class CampaignCollection extends ResourceCollection
{
    /**
     * Transform the resource collection into an array.
     * @return array{
     *     data: Collection<Campaign>,
     *     links: array{
     *         self: string
     *     }
     * }
     */
    #[Override]
    public function toArray(Request $request): array
    {
        return [
            'data' => $this->collection,
            'links' => [
                'self' => route('campaigns.index'),
            ],
        ];
    }
}
