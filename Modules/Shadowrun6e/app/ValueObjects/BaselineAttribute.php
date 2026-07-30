<?php

declare(strict_types=1);

namespace Modules\Shadowrun6e\ValueObjects;

use Modules\Shadowrun6e\Models\Character;

readonly class BaselineAttribute
{
    public function __construct(
        public int $minimum,
        public int $maximum,
        private string $attribute,
    ) {
    }

    public function getMaximum(Character $character): int
    {
        $maximum = $this->maximum;
        foreach ($character->qualities as $quality) {
            foreach ($quality->effects ?? [] as $effect => $amount) {
                if ($effect !== 'maximum-' . $this->attribute) {
                    continue;
                }
                $maximum += $amount;
            }
        }
        return $maximum;
    }

    public function getMinimum(): int
    {
        return $this->minimum;
    }
}
