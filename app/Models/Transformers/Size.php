<?php

declare(strict_types=1);

namespace App\Models\Transformers;

enum Size: int
{
    case Object = 0;
    case Human = 1;
    case Small = 2;
    case Standard = 3;
    case Leader = 4;
    case Voyager = 5;
    case Combiner = 6;
    case Citybot = 7;
    case Planet = 8;

    /**
     * Return what additional effects the transformer gets.
     */
    public function action(): ?string
    {
        return match ($this) {
            Size::Object => null,
            Size::Human => 'avoid',
            Size::Small => 'avoid',
            Size::Standard => null,
            Size::Leader => 'size-dmg',
            Size::Voyager => 'size-dmg',
            Size::Combiner => 'size-dmg',
            Size::Citybot => 'size-dmg',
            Size::Planet => 'size-dmg',
        };
    }

    /**
     * Energon modifier based on the character's size.
     */
    public function energon(): int
    {
        return match ($this) {
            Size::Object => 4,
            Size::Human => 3,
            Size::Small => 1,
            Size::Standard => 0,
            Size::Leader => -1,
            Size::Voyager => -3,
            Size::Combiner => -5,
            Size::Citybot => -8,
            Size::Planet => -10,
        };
    }

    /**
     * Hit point modifier based on the character's size.
     */
    public function hp(): int
    {
        return match ($this) {
            Size::Object => -4,
            Size::Human => -3,
            Size::Small => -1,
            Size::Standard => 0,
            Size::Leader => 1,
            Size::Voyager => 2,
            Size::Combiner => 5,
            Size::Citybot => 8,
            Size::Planet => 30,
        };
    }
}
