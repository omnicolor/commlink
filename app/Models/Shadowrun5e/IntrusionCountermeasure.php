<?php

declare(strict_types=1);

namespace App\Models\Shadowrun5e;

use RuntimeException;

/**
 * Class representing automated matrix defense: ICE.
 */
class IntrusionCountermeasure
{
    public ?int $attack;
    public ?int $data_processing;
    public string $defense;
    public string $description;
    public ?int $firewall;
    public string $id;
    public int $initiative_base;
    public int $initiative_dice = 4;
    public string $name;
    public int $page;
    public string $ruleset;
    public ?int $sleaze;

    /**
     * Collection of all available ICE.
     * @var array<string, array<string, int|string>>
     */
    public static ?array $ice;

    /**
     * Construct a new ICE object.
     * @param string $id ID of the ICE
     * @param ?int $attack
     * @param ?int $dataProcessing
     * @param ?int $firewall
     * @param ?int $sleaze
     * @throws RuntimeException
     */
    public function __construct(
        string $id,
        ?int $attack = null,
        ?int $dataProcessing = null,
        ?int $firewall = null,
        ?int $sleaze = null,
    ) {
        // Lazy load the intrusion countermeasures.
        $filename = config('app.data_path.shadowrun5e')
            . 'intrusion-countermeasures.php';
        self::$ice ??= require $filename;

        $this->id = \strtolower($id);
        if (!isset(self::$ice[$this->id])) {
            throw new RuntimeException(\sprintf(
                'Intrusion countermeasure ID "%s" is invalid',
                $this->id
            ));
        }

        $this->attack = $attack;
        $this->data_processing = $dataProcessing;
        $this->firewall = $firewall;
        $this->sleaze = $sleaze;

        $ice = self::$ice[$this->id];
        $this->defense = $ice['defense'];
        $this->description = $ice['description'];
        $this->initiative_base = (int)$dataProcessing;
        $this->name = $ice['name'];
        $this->page = $ice['page'];
        $this->ruleset = $ice['ruleset'];
    }

    public function __toString(): string
    {
        return $this->name;
    }

    /**
     * @return int
     * @throws RuntimeException if all attributes haven't been set
     */
    public function getConditionMonitor(): int
    {
        return 8 + (int)ceil($this->getHostRating() / 2);
    }

    /**
     * Return an array with the two attributes an unlucky decker or technomancer
     * should roll to defend against an attack.
     * @return array<int, string> Two defense attributes
     */
    public function getDefenseAttributes(): array
    {
        $defense = \explode('+', $this->defense);
        return [\trim($defense[0]), \trim($defense[1])];
    }

    /**
     * Return the host rating that the ICE belongs to.
     * @return int
     * @throws RuntimeException if all attributes haven't been set
     */
    public function getHostRating(): int
    {
        if (
            null === $this->attack
            || null === $this->data_processing
            || null === $this->firewall
            || null === $this->sleaze
        ) {
            throw new RuntimeException(
                'Host rating requires ASDF attributes to be set'
            );
        }
        return min(
            $this->attack,
            $this->data_processing,
            $this->firewall,
            $this->sleaze,
        );
    }
}
