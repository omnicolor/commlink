<?php

declare(strict_types=1);

namespace App\Models\Shadowrun5E;

/**
 * Commlink class.
 */
class Commlink extends Gear
{
    /**
     * Collection of attribute values allowed for the device.
     * @var array<int, ?int>
     */
    public array $attributes = [];

    /**
     * Whether the deck's attributes are configurable.
     * @var bool
     */
    public bool $configurable = false;

    /**
     * Marks the device has on others.
     * @var array<mixed>
     */
    public array $marks = [];

    /**
     * Overwatch score for the device.
     * @var int
     */
    public int $overwatch = 0;

    /**
     * Programs the commlink or deck has availabile.
     * @var ProgramArray
     */
    public ProgramArray $programs;

    /**
     * Number of programs allowed by the deck (not including extras from Virtual
     * Machine program or Program Carrier module).
     * @var int
     */
    public int $programsAllowed = 0;

    /**
     * Collection of programs installed on the deck, though not necessarily
     * running.
     * @var ProgramArray
     */
    public ProgramArray $programsInstalled;

    /**
     * Collection of currently running programs.
     * @var ProgramArray
     */
    public ProgramArray $programsRunning;

    /**
     * Ordered (ASDF) collection of attributes for the device (reconfigured by
     * the user).
     * @var int[]
     */
    public array $setAttributes = [];

    /**
     * Collection of items slaved to this device.
     * @var array<mixed>
     */
    public array $slavedDevices = [];

    /**
     * ID of the SIN the commlink is broadcasting.
     * @var ?int
     */
    public ?int $sin;

    /**
     * Construct a new Commlink object.
     * @param string $id ID to load
     * @param int $quantity Number of items
     * @throws \RuntimeException if ID is invalid
     */
    public function __construct(string $id, int $quantity = 1)
    {
        parent::__construct($id, $quantity);
        // @phpstan-ignore-next-line
        $item = self::$gear[$id];
        $this->programs = new ProgramArray();
        $this->programsInstalled = new ProgramArray();
        $this->programsRunning = new ProgramArray();

        $this->programsAllowed = $item['programs'];
        if (
            isset($item['attributes']) && isset($item['attributes']['firewall'])
        ) {
            $this->attributes = [
                $item['attributes']['attack'] ?? null,
                $item['attributes']['sleaze'] ?? null,
                $item['attributes']['data-processing'] ?? null,
                $item['attributes']['firewall'] ?? null,
            ];
            return;
        }
        if (isset($item['attributes'])) {
            $this->attributes = $item['attributes'];
            $this->configurable = true;
            return;
        }
        $this->attributes = [null, null, $this->rating, $this->rating];
    }

    /**
     * Return the number of boxen in the item's matrix condition monitor.
     * @return int
     */
    public function getConditionMonitor(): int
    {
        if (!isset($this->rating)) {
            return 0;
        }
        return 8 + (int)ceil($this->rating / 2);
    }

    /**
     * Return the cost of the commlink including modifications and programs.
     * @return int
     */
    public function getCost(): int
    {
        $cost = 0;
        foreach ($this->programs as $program) {
            $cost += $program->getCost();
        }
        return $cost + parent::getCost();
    }
}
