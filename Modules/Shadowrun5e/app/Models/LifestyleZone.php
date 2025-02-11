<?php

declare(strict_types=1);

namespace Modules\Shadowrun5e\Models;

use Override;
use RuntimeException;
use Stringable;

use function config;
use function sprintf;
use function strtolower;

/**
 * Representation of a neighborhood's lifestyle zone.
 */
final class LifestyleZone implements Stringable
{
    public readonly string $description;

    /**
     * Zone code.
     */
    public readonly string $name;

    /**
     * Response time for first responders.
     */
    public readonly string $response_time;

    /**
     * List of all zones.
     * @var array<string, array<string, string>>
     */
    public static ?array $zones;

    /**
     * @throws RuntimeException if the ID is invalid.
     */
    public function __construct(public readonly string $id)
    {
        $filename = config('shadowrun5e.data_path') . 'lifestyle-zones.php';
        self::$zones ??= require $filename;

        $id = strtolower($id);
        if (!isset(self::$zones[$id])) {
            throw new RuntimeException(
                sprintf('Lifestyle Zone ID "%s" is invalid', $id)
            );
        }

        $zone = self::$zones[$id];
        $this->description = $zone['description'];
        $this->name = $zone['name'];
        $this->response_time = $zone['response_time'];
    }

    #[Override]
    public function __toString(): string
    {
        return $this->name;
    }
}
