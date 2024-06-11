<?php

declare(strict_types=1);

namespace App\Models\Shadowrun5e;

use RuntimeException;
use Stringable;

use function array_key_exists;
use function config;
use function sprintf;
use function strtolower;

/**
 * Representation of a neighborhood's lifestyle zone.
 * @psalm-suppress PossiblyUnusedProperty
 */
class LifestyleZone implements Stringable
{
    /**
     * Description of the Zone.
     */
    public string $description;

    /**
     * Zone code.
     */
    public string $name;

    /**
     * Response time for first responders.
     */
    public string $response_time;

    /**
     * List of all zones.
     * @var array<string, array<string, string>>
     */
    public static ?array $zones;

    /**
     * @throws RuntimeException if the ID is invalid.
     */
    public function __construct(public string $id)
    {
        $filename = config('app.data_path.shadowrun5e') . 'lifestyle-zones.php';
        self::$zones ??= require $filename;

        $id = strtolower($id);
        if (!array_key_exists($id, self::$zones)) {
            throw new RuntimeException(
                sprintf('Lifestyle Zone ID "%s" is invalid', $id)
            );
        }

        $zone = self::$zones[$id];
        $this->description = $zone['description'];
        $this->name = $zone['name'];
        $this->response_time = $zone['response_time'];
    }

    public function __toString(): string
    {
        return $this->name;
    }
}
