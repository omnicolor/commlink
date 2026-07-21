<?php

declare(strict_types=1);

namespace Modules\Stillfleet\Models;

use function array_filter;

/**
 * @phpstan-type CharacterDetailsArray array{
 *      appearance?: string,
 *      company?: string,
 *      crew_nickname?: string,
 *      family?: string,
 *      motivation?: string,
 *      origin?: string,
 *      others?: string,
 *      refactor?: string,
 *      team?: string
 *  }
 */
class CharacterDetails
{
    public ?string $appearance = null;
    public ?string $company = null;
    public ?string $crew_nickname = null;
    public ?string $family = null;
    public ?string $motivation = null;
    public ?string $origin = null;
    public ?string $others = null;
    public ?string $refactor = null;
    public ?string $team = null;

    /**
     * @param CharacterDetailsArray|null $data
     */
    public static function make(array|null $data): self
    {
        if (null === $data) {
            return new self();
        }
        $details = new self();
        $details->appearance = $data['appearance'] ?? null;
        $details->company = $data['company'] ?? null;
        $details->crew_nickname = $data['crew_nickname'] ?? null;
        $details->family = $data['family'] ?? null;
        $details->motivation = $data['motivation'] ?? null;
        $details->origin = $data['origin'] ?? null;
        $details->others = $data['others'] ?? null;
        $details->refactor = $data['refactor'] ?? null;
        $details->team = $data['team'] ?? null;

        return $details;
    }

    /**
     * @return array{
     *     appearance?: string,
     *     company?: string,
     *     crew_nickname?: string,
     *     family?: string,
     *     motivation?: string,
     *     origin?: string,
     *     others?: string,
     *     refactor?: string,
     *     team?: string
     * }
     */
    public function toArray(): array
    {
        return array_filter(
            [
                'appearance' => $this->appearance,
                'company' => $this->company,
                'crew_nickname' => $this->crew_nickname,
                'family' => $this->family,
                'motivation' => $this->motivation,
                'origin' => $this->origin,
                'others' => $this->others,
                'refactor' => $this->refactor,
                'team' => $this->team,
            ],
            function (?string $value): bool {
                return null !== $value;
            },
        );
    }
}
