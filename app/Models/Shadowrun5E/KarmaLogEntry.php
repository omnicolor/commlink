<?php

declare(strict_types=1);

namespace App\Models\Shadowrun5E;

/**
 * Karma log stores all karma gain and spent, including during character
 * generation.
 */
class KarmaLogEntry
{
    /**
     * Description of the karma event.
     * @var string
     */
    public string $description;

    /**
     * Amount of karma in the event.
     * @var int
     */
    public int $karma;

    /**
     * Date the karma was gained or spent in the real world.
     * @var ?\DateTimeInterface
     */
    public ?\DateTimeInterface $realDate = null;

    /**
     * Date the karma was gained or spent in the game world.
     * @var ?\DateTimeInterface
     */
    public ?\DateTimeInterface $gameDate = null;

    /**
     * Constructor.
     * @param string $description
     * @param int $karma
     * @param ?\DateTimeInterface $realDate
     * @param ?\DateTimeInterface $gameDate
     */
    public function __construct(
        string $description,
        int $karma,
        ?\DateTimeInterface $realDate = null,
        ?\DateTimeInterface $gameDate = null
    ) {
        $this->description = $description;
        $this->karma = $karma;
        $this->realDate = $realDate;
        $this->gameDate = $gameDate;
    }
}
