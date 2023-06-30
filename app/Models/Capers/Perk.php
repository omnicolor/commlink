<?php

declare(strict_types=1);

namespace App\Models\Capers;

use RuntimeException;

class Perk
{
    /** @psalm-suppress PossiblyUnusedProperty */
    public string $description;
    public string $id;
    public string $name;
    /** @psalm-suppress PossiblyUnusedProperty */
    public ?string $skillId = null;

    /**
     * @var array<string, array<string, string>>
     */
    public static ?array $perks;

    /**
     * Constructor.
     * @param string $id
     * @param array<string, string> $rawPerk
     */
    public function __construct(string $id, array $rawPerk)
    {
        $filename = config('app.data_path.capers') . 'perks.php';
        self::$perks ??= require $filename;

        $this->id = \strtolower($id);
        if (!isset(self::$perks[$this->id])) {
            throw new RuntimeException(
                \sprintf('Perks ID "%s" is invalid', $id)
            );
        }

        $perk = self::$perks[$this->id];
        $this->description = $perk['description'];
        $this->name = $perk['name'];

        if ('specialty-skill' !== $this->id) {
            return;
        }
        $this->skillId = $rawPerk['skill'];
    }

    public function __toString(): string
    {
        return $this->name;
    }
}
