<?php

declare(strict_types=1);

namespace App\Models\Shadowrun5E;

/**
 * Program class, for a program installed on a commlink, 'deck, rcc, etc.
 */
class Program
{
    /**
     * List of devices that can run the program.
     * @var string[]
     */
    public array $allowedDevices = [];

    /**
     * Availability code for the program.
     * @var string
     */
    public string $availability;

    /**
     * Cost of the program.
     * @var int
     */
    public int $cost;

    /**
     * Cost of the program.
     * @var string Description of the program
     */
    public string $description;

    /**
     * Collection of effects the program has.
     * @var array<string, int>
     */
    public array $effects = [];

    /**
     * Unique ID for the program.
     * @var string
     */
    public string $id;

    /**
     * Name of the program.
     * @var string
     */
    public string $name;

    /**
     * Page the program was listed on.
     * @var ?int
     */
    public ?int $page;

    /**
     * Optional rating for programs (agents) that need one.
     * @var ?int
     */
    public ?int $rating;

    /**
     * ID of the rules the program was introduced in.
     * @var ?string
     */
    public ?string $ruleset;

    /**
     * Whether the program is currently running.
     * @var bool
     */
    public bool $running;

    /**
     * Specific vehicle if the program is an autosoft.
     * @var mixed
     */
    public $vehicle;

    /**
     * Specific weapon if the program is an autosoft.
     * @var mixed
     */
    public $weapon;

    /**
     * List of all programs.
     * @var ?array<mixed>
     */
    public static ?array $programs;

    /**
     * Construct a new program object.
     * @param string $id ID of the program
     * @param ?bool $running Whether the program is running or not
     * @throws \RuntimeException if the ID isn't found
     */
    public function __construct(string $id, ?bool $running = null)
    {
        // Lazy load the programs.
        $filename = config('app.data_path.shadowrun5e') . 'programs.php';
        self::$programs ??= require $filename;

        $id = \strtolower($id);
        if (!isset(self::$programs[$id])) {
            throw new \RuntimeException(\sprintf(
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
        $this->id = $id;
        $this->name = $program['name'];
        $this->page = $program['page'] ?? null;
        $this->rating = $program['rating'] ?? null;
        $this->running = $running ?? false;
        $this->ruleset = $program['ruleset'] ?? 'core';
    }

    /**
     * Return the name of the program.
     * @return string
     */
    public function __toString(): string
    {
        return $this->name;
    }

    /**
     * Return the cost of the program.
     * @return int
     */
    public function getCost(): int
    {
        return $this->cost;
    }

    /**
     * Build a program from either its ID or an array.
     * @param array<string, string>|string $rawProgram
     * @param ProgramArray $running
     * @return Program
     * @throws \RuntimeException
     */
    public static function build(
        array | string $rawProgram,
        ProgramArray $running,
    ): Program {
        if (!\is_array($rawProgram)) {
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
     * @param ProgramArray $running
     * @return bool
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
