<?php

declare(strict_types=1);

namespace Modules\Expanse\Models;

enum ShipSize: string
{
    case Tiny = 'tiny';
    case Small = 'small';
    case Medium = 'medium';
    case Large = 'large';
    case Huge = 'huge';
    case Gigantic = 'gigantic';
    case Colossal = 'colossal';
    case Titanic = 'titanic';

    public function length(): string
    {
        return match ($this) {
            ShipSize::Tiny => '5m',
            ShipSize::Small => '10m',
            ShipSize::Medium => '25m',
            ShipSize::Large => '50m',
            ShipSize::Huge => '100m',
            ShipSize::Gigantic => '250m',
            ShipSize::Colossal => '500m',
            ShipSize::Titanic => '1km+',
        };
    }

    /**
     * @psalm-suppress PossiblyUnusedMethod
     */
    public function hull(): string
    {
        return match ($this) {
            ShipSize::Tiny => '1d1',
            ShipSize::Small => '1d3',
            ShipSize::Medium => '1d6',
            ShipSize::Large => '2d6',
            ShipSize::Huge => '3d6',
            ShipSize::Gigantic => '4d6',
            ShipSize::Colossal => '5d6',
            ShipSize::Titanic => '6d6',
        };
    }

    public function crewMin(): int
    {
        return match ($this) {
            ShipSize::Tiny => 1,
            ShipSize::Small => 1,
            ShipSize::Medium => 2,
            ShipSize::Large => 4,
            ShipSize::Huge => 16,
            ShipSize::Gigantic => 64,
            ShipSize::Colossal => 256,
            ShipSize::Titanic => 1024,
        };
    }

    public function crewStandard(): int
    {
        return match ($this) {
            ShipSize::Tiny => 2,
            ShipSize::Small => 2,
            ShipSize::Medium => 4,
            ShipSize::Large => 16,
            ShipSize::Huge => 64,
            ShipSize::Gigantic => 512,
            ShipSize::Colossal => 2048,
            ShipSize::Titanic => 8192,
        };
    }
}
