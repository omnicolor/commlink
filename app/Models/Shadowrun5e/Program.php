<?php

declare(strict_types=1);

namespace App\Models\Shadowrun5e;

use RuntimeException;
use Stringable;

use function config;
use function is_array;
use function sprintf;
use function strtolower;

/**
 * Program class, for a program installed on a commlink, 'deck, rcc, etc.
 * @psalm-suppress PossiblyUnusedProperty
 */
class Program implements Stringable
{
    /**
     * List of devices that can run the program.
     * @var array<int, string>
     */
    public array $allowedDevices = [];

    /**
     * Availability code for the program.
     */
    public string $availability;

    /**
     * Cost of the program.
     */
    public int $cost;

    /**
     * Description of the program.
     */
    public string $description;

    /**
     * Collection of effects the program has.
     * @var array<string, int>
     */
    public array $effects = [];

    /**
     * Name of the program.
     */
    public string $name;

    /**
     * Page the program was listed on.
     */
    public ?int $page;

    /**
     * Optional rating for programs (agents) that need one.
     */
    public ?int $rating;

    /**
     * ID of the rules the program was introduced in.
     */
    public ?string $ruleset;

    /**
     * Whether the program is currently running.
     */
    public bool $running;

    /**
     * Specific vehicle if the program is an autosoft.
     */
    public mixed $vehicle;

    /**
     * Specific weapon if the program is an autosoft.
     */
    public mixed $weapon;

    /**
     * List of all programs.
     * @var ?array<string, array<string, mixed>>
     */
    public static ?array $programs;

    /**
     * @throws RuntimeException if the ID isn't found
     */
    public function __construct(public string $id, ?bool $running = null)
    {
        // Lazy load the programs.
        $filename = config('app.data_path.shadowrun5e') . 'programs.php';
        self::$programs ??= require $filename;

        $id = strtolower($id);
        if (!isset(self::$programs[$id])) {
            throw new RuntimeException(sprintf(
                'Program ID "%s" is invalid',
                $id
            ));
        }

        $program = self::$programs[$id];
        $this->allowedDevices = $program['allowedDevices'];
        $this->availability = $program['availability'];
        $this->cost = (int)$program['cost'];
        $this->description = $program['description'];
        $this->effects = $program['effects'] ?? [];
        $this->name = $program['name'];
        $this->page = $program['page'] ?? null;
        $this->rating = $program['rating'] ?? null;
        $this->running = $running ?? false;
        $this->ruleset = $program['ruleset'] ?? 'core';
    }

    public function __toString(): string
    {
        return $this->name;
    }

    public function getCost(): int
    {
        return $this->cost;
    }

    /**
     * Build a program from either its ID or an array.
     * @param array<string, string>|string $rawProgram
     * @throws RuntimeException
     */
    public static function build(
        array | string $rawProgram,
        ProgramArray $running,
    ): Program {
        if (!is_array($rawProgram)) {
            $program = new Program($rawProgram);
            $program->running = $program->isRunning($running);
            return $program;
        }

        $program = new Program($rawProgram['id']);
        $program->running = $program->isRunning($running);
        if (isset($rawProgram['vehicle'])) {
            $program->vehicle = new Vehicle(['id' => $rawProgram['vehicle']]);
            return $program;
        }
        if (isset($rawProgram['weapon'])) {
            $program->weapon = new Weapon($rawProgram['weapon']);
        }
        return $program;
    }

    /**
     * Determine if the program is running based on the array of running
     * programs.
     */
    public function isRunning(ProgramArray $running): bool
    {
        foreach ($running as $potential) {
            if ($potential->id === $this->id) {
                return true;
            }
        }
        return false;
    }
}
