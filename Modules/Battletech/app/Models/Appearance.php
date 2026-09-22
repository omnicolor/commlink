<?php

declare(strict_types=1);

namespace Modules\Battletech\Models;

/**
 * @phpstan-type AppearanceArray array{
 *     extra?: string,
 *     eyes?: string,
 *     hair?: string,
 *     height?: int,
 *     weight?: int,
 * }
 */
class Appearance
{
    public ?string $extra = null;
    public ?string $eyes = null;
    public ?string $hair = null;
    public ?int $height = null;
    public ?int $weight = null;

    /**
     * @param AppearanceArray|null $data
     */
    public static function make(array|null $data): self
    {
        if (null === $data) {
            return new self();
        }

        $appearance = new self();
        $appearance->extra = $data['extra'] ?? null;
        $appearance->eyes = $data['eyes'] ?? null;
        $appearance->hair = $data['hair'] ?? null;
        $appearance->height = $data['height'] ?? null;
        $appearance->weight = $data['weight'] ?? null;

        return $appearance;
    }

    /**
     * @return AppearanceArray
     */
    public function toArray(): array
    {
        return array_filter(
            [
                'extra' => $this->extra,
                'eyes' => $this->eyes,
                'hair' => $this->hair,
                'height' => $this->height,
                'weight' => $this->weight,
            ],
            function (int|string|null $value): bool {
                return null !== $value;
            }
        );
    }
}
