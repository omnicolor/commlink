<?php

declare(strict_types=1);

namespace Modules\Startrekadventures\Models;

/**
 * Disciplines for a Star Trek Adventures character.
 * @psalm-suppress PossiblyUnusedProperty
 */
class Disciplines
{
    public int $command;
    public int $conn;
    public int $engineering;
    public int $medicine;
    public int $science;
    public int $security;

    /**
     * @param array<string, int> $disciplines
     */
    public function __construct(array $disciplines)
    {
        $this->command = (int)$disciplines['command'];
        $this->conn = (int)$disciplines['conn'];
        $this->engineering = (int)$disciplines['engineering'];
        $this->medicine = (int)$disciplines['medicine'];
        $this->science = (int)$disciplines['science'];
        $this->security = (int)$disciplines['security'];
    }
}
