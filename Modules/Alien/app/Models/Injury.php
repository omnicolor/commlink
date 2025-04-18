<?php

declare(strict_types=1);

namespace Modules\Alien\Models;

use Override;
use RuntimeException;
use Stringable;

use function array_keys;
use function collect;
use function config;
use function sprintf;
use function strtolower;

class Injury implements Stringable
{
    public ?int $death_roll_modifier;
    /** @var array<string, int> */
    public array $effects;
    public string $effects_text;
    public bool $fatal;
    public ?string $healing_time;
    public string $name;
    public int $roll;
    public ?string $time_limit;

    /** @var ?array<string, array<string, array<string, int>|bool|int|null|string>> */
    public static ?array $injuries;

    public function __construct(public string $id)
    {
        $filename = config('alien.data_path') . 'injuries.php';
        self::$injuries ??= require $filename;

        $id = strtolower($id);
        if (!isset(self::$injuries[$id])) {
            throw new RuntimeException(sprintf(
                'Injury ID "%s" is invalid',
                $id
            ));
        }

        $injury = self::$injuries[$id];
        $this->death_roll_modifier = $injury['dealth_roll_modifier'] ?? null;
        $this->effects = $injury['effects'] ?? [];
        $this->effects_text = $injury['effects_text'];
        $this->fatal = $injury['fatal'];
        $this->healing_time = $injury['healing_time'] ?? null;
        $this->name = $injury['name'];
        $this->roll = $injury['roll'];
        $this->time_limit = $injury['time-limit'] ?? null;
    }

    #[Override]
    public function __toString(): string
    {
        return $this->name;
    }

    /**
     * @return array<int, Injury>
     */
    public static function all(): array
    {
        $filename = config('alien.data_path') . 'injuries.php';
        self::$injuries ??= require $filename;

        $injuries = [];
        /** @var string $id */
        foreach (array_keys(self::$injuries ?? []) as $id) {
            $injuries[] = new Injury($id);
        }
        return $injuries;
    }

    public static function findByRoll(int $roll): ?Injury
    {
        return collect(self::all())->keyBy('roll')->get($roll);
    }
}
