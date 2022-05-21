<?php

declare(strict_types=1);

namespace App\Models\Shadowrun5e;

/**
 * Karma log stores all karma gain and spent, including during character
 * generation.
 */
class KarmaLogEntry
{
    /**
     * Constructor.
     * @param string $description Description of the karma event
     * @param int $karma Amount of karma
     * @param ?\DateTimeInterface $realDate Real world date for karma event
     * @param ?\DateTimeInterface $gameDate Game world date for karma event
     */
    public function __construct(
        public string $description,
        public int $karma,
        public ?\DateTimeInterface $realDate = null,
        public ?\DateTimeInterface $gameDate = null
    ) {
        $this->description = $description;
        $this->karma = $karma;
    }
}
