<?php

declare(strict_types=1);

namespace App\Models\CyberpunkRed\Role;

use App\Models\CyberpunkRed\Role;

class Rockerboy extends Role
{
    public const ACT_GROUP = 1;
    public const ACT_SOLO = 2;

    public const GUNNING_OLD_GROUP_MEMBER = 1;

    public const PERFORM_CAFE = 1;

    public const TYPE_MUSICIAN = 1;
    public const TYPE_SLAM_POET = 2;
    public const TYPE_STREET_ARTIST = 3;

    /**
     * Whether the character is a solo (2) or group (1) act.
     * @var int
     */
    public int $act;

    /**
     * Who's gunning for the rockerboy?
     * @var int
     */
    public int $gunning;

    /**
     * Where does the rockerboy perform?
     * @var int
     */
    public int $perform;

    /**
     * What kind of Rockerboy is the character?
     * @var int
     */
    public int $type;

    /**
     * Constructor.
     * @param array<string, int> $role
     */
    public function __construct(array $role)
    {
        $this->act = $role['act'];
        $this->gunning = $role['gunning'];
        $this->perform = $role['perform'];
        $this->type = $role['type'];
    }

    /**
     * Return the name of the role.
     * @return string
     */
    public function __toString(): string
    {
        return 'Rockerboy';
    }

    /**
     * Return the type of act the rockerboy performs in.
     * @return string
     * @throws \OutOfBoundsException
     */
    public function getAct(): string
    {
        switch ($this->act) {
            case self::ACT_SOLO:
                return 'solo';
            case self::ACT_GROUP:
                return 'group';
        }
        throw new \OutOfBoundsException();
    }

    /**
     * Return a description of who's gunning for you.
     * @return string
     * @throws \OutOfBoundsException
     */
    public function getWhosGunning(): string
    {
        switch ($this->gunning) {
            case self::GUNNING_OLD_GROUP_MEMBER:
                return 'Old group member who thinks you did them dirty.';
        }
        throw new \OutOfBoundsException();
    }

    /**
     * Return the type of rockerboy.
     * @return string
     * @throws \OutOfBoundsException
     */
    public function getType(): string
    {
        switch ($this->type) {
            case self::TYPE_MUSICIAN:
                return 'musician';
            case self::TYPE_SLAM_POET:
                return 'slam poet';
            case self::TYPE_STREET_ARTIST:
                return 'street artist';
        }
        throw new \OutOfBoundsException();
    }
}
