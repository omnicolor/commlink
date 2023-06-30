<?php

declare(strict_types=1);

namespace App\Models\Shadowrun5e;

use RuntimeException;

/**
 * Representation of a neighborhood's lifestyle zone.
 * @psalm-suppress PossiblyUnusedProperty
 */
class LifestyleZone
{
    /**
     * Description of the Zone.
     */
    public string $description;

    /**
     * Zone identifier.
     */
    public string $id;

    /**
     * Zone code.
     */
    public string $name;

    /**
     * Response time for first responders.
     */
    public string $responseTime;

    /**
     * List of all zones.
     * @var array<string, array<string, string>>
     */
    public static ?array $zones;

    /**
     * Construct a new Zone object.
     * @throws RuntimeException if the ID is invalid.
     */
    public function __construct(string $id)
    {
        $filename = config('app.data_path.shadowrun5e') . 'lifestyle-zones.php';
        self::$zones ??= require $filename;

        $id = \strtolower($id);
        if (!\array_key_exists($id, self::$zones)) {
            throw new RuntimeException(
                \sprintf('Lifestyle Zone ID "%s" is invalid', $id)
            );
        }

        $zone = self::$zones[$id];
        $this->description = $zone['description'];
        $this->id = $id;
        $this->name = $zone['name'];
        $this->responseTime = $zone['responseTime'];
    }

    public function __toString(): string
    {
        return $this->name;
    }
}
