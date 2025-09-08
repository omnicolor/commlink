<?php

declare(strict_types=1);

namespace Modules\Shadowrun5e\Models;

use RuntimeException;

use function is_array;
use function is_numeric;
use function str_starts_with;

/**
 * Gear factory, returns an appropriate gear object.
 */
final class GearFactory
{
    /**
     * Return a Gear object.
     * @param string|array<string, mixed> $gear
     * @throws RuntimeException
     */
    public static function get(array|string $gear): Gear
    {
        if (is_array($gear)) {
            return self::getGearFromArray($gear);
        }
        return self::getGearFromId($gear);
    }

    /**
     * Return a gear item from an array.
     * @param array<string, mixed> $gear
     * @throws RuntimeException
     */
    private static function getGearFromArray(array $gear): Gear | Commlink
    {
        $quantity = 1;
        if (isset($gear['quantity']) && is_numeric($gear['quantity'])) {
            $quantity = (int)$gear['quantity'];
        }
        if (
            str_starts_with((string) $gear['id'], 'cyberdeck-')
            || str_starts_with((string) $gear['id'], 'commlink-')
            || str_starts_with((string) $gear['id'], 'rcc-')
        ) {
            $commlink = new Commlink($gear['id'], $quantity);
            if (isset($gear['sin'])) {
                $commlink->sin = $gear['sin'];
            }
            return $commlink;
        }
        return new Gear($gear['id'], $quantity);
    }

    /**
     * Return a gear item given its ID.
     * @throws RuntimeException
     */
    private static function getGearFromId(string $id): Gear
    {
        return new Gear($id, 1);
    }
}
