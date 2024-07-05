<?php

declare(strict_types=1);

namespace Modules\Shadowrun5e\Models;

use RuntimeException;
use Stringable;

use function ceil;
use function config;
use function explode;
use function min;
use function sprintf;
use function strtolower;
use function trim;

/**
 * Class representing automated matrix defense: ICE.
 * @psalm-suppress UnusedClass
 */
class IntrusionCountermeasure implements Stringable
{
    public string $defense;
    public string $description;
    public int $initiative_base;
    public int $initiative_dice = 4;
    public string $name;
    public int $page;
    public string $ruleset;

    /**
     * Collection of all available ICE.
     * @var array<string, array<string, int|string>>
     */
    public static ?array $ice = null;

    /**
     * @psalm-suppress UnusedVariable
     * @throws RuntimeException
     */
    public function __construct(
        public string $id,
        public ?int $attack = null,
        public ?int $data_processing = null,
        public ?int $firewall = null,
        public ?int $sleaze = null,
    ) {
        // Lazy load the intrusion countermeasures.
        $filename = config('shadowrun5e.data_path')
            . 'intrusion-countermeasures.php';
        /** @psalm-suppress UnresolvableInclude */
        self::$ice ??= require $filename;

        $id = strtolower($id);
        if (!isset(self::$ice[$id])) {
            throw new RuntimeException(sprintf(
                'Intrusion countermeasure ID "%s" is invalid',
                $id
            ));
        }

        $ice = self::$ice[$id];
        $this->defense = $ice['defense'];
        $this->description = $ice['description'];
        $this->initiative_base = (int)$this->data_processing;
        $this->name = $ice['name'];
        $this->page = $ice['page'];
        $this->ruleset = $ice['ruleset'];
    }

    public function __toString(): string
    {
        return $this->name;
    }

    /**
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
        $defense = explode('+', $this->defense);
        return [trim($defense[0]), trim($defense[1])];
    }

    /**
     * Return the host rating that the ICE belongs to.
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
