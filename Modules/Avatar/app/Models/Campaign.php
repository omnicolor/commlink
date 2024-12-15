<?php

declare(strict_types=1);

namespace Modules\Avatar\Models;

use App\Models\Campaign as BaseCampaign;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Modules\Avatar\Database\Factories\CampaignFactory;

class Campaign extends BaseCampaign
{
    use HasFactory;

    public function era(): ?Era
    {
        if (null === $this->options || null === ($this->options['era'] ?? null)) {
            return null;
        }
        return Era::from($this->options['era']);
    }

    protected static function newFactory(): Factory
    {
        return CampaignFactory::new();
    }
}
