<?php

declare(strict_types=1);

namespace App\Models\Shadowrun5E;

/**
 * Representation of a neighborhood's lifestyle zone.
 */
class LifestyleZone
{
    /**
     * Description of the Zone.
     * @var string
     */
    public string $description;

    /**
     * Zone identifier.
     * @var string
     */
    public string $id;

    /**
     * Zone code.
     * @var string
     */
    public string $name;

    /**
     * Response time for first responders.
     * @var string
     */
    public string $responseTime;

    /**
     * List of all zones.
     * @var array<string, array<string, string>>
     */
    public static ?array $zones;

    /**
     * Construct a new Zone object.
     * @param string $id
     * @throws \RuntimeException if the ID is invalid.
     */
    public function __construct(string $id)
    {
        $filename = config('app.data_path.shadowrun5e') . 'lifestyle-zones.php';
        self::$zones ??= require $filename;

        $id = strtolower($id);
        if (!array_key_exists($id, self::$zones)) {
            throw new \RuntimeException(
                sprintf('Lifestyle Zone ID "%s" is invalid', $id)
            );
        }

        $zone = self::$zones[$id];
        $this->description = $zone['description'];
        $this->id = $id;
        $this->name = $zone['name'];
        $this->responseTime = $zone['responseTime'];
    }

    /**
     * Return the name of the zone.
     * @return string
     */
    public function __toString(): string
    {
        return $this->name;
    }
}
