<?php

declare(strict_types=1);

namespace Modules\Shadowrun5e\Models;

use Illuminate\Support\Facades\Log;
use Override;
use RuntimeException;
use Stringable;

use function config;
use function sprintf;
use function strtolower;

/**
 * Representation of a Shadowrun 5E critter.
 */
final class Critter implements Stringable
{
    public readonly int $agility;
    public readonly int $armor;
    public readonly int $body;
    public readonly int $charisma;
    public int $condition_physical;
    public int $condition_stun;
    public readonly string $description;
    public readonly int $edge;
    public readonly float $essence;
    public readonly null|string $habitat;
    public readonly int $initiative_base;
    public readonly int $initiative_dice;
    public readonly int $intuition;
    public readonly int $logic;
    public readonly null|int $magic;
    public readonly string $name;
    public readonly int $page;
    public readonly int $reaction;
    public readonly null|int $resonance;
    public readonly string $ruleset;
    public SkillArray $skills;
    public readonly int $strength;
    public readonly int $willpower;

    /**
     * Collection of critter's powers.
     * @var array<int, CritterPower>
     */
    public array $powers = [];

    /**
     * List of weakness the critter has.
     * @var array<int, CritterWeakness>
     */
    public array $weaknesses = [];

    /**
     * Collection of all critters.
     * @var ?array<string, array<string, array<string, string>|int|string>>
     */
    public static ?array $critters;

    /**
     * Constructor.
     * @throws RuntimeException
     */
    public function __construct(public readonly string $id)
    {
        $filename = config('shadowrun5e.data_path') . 'critters.php';
        self::$critters ??= require $filename;

        $id = strtolower($id);
        if (!isset(self::$critters[$id])) {
            throw new RuntimeException(sprintf(
                'Critter ID "%s" is invalid',
                $this->id
            ));
        }

        $this->skills = new SkillArray();
        $critter = self::$critters[$this->id];
        $this->agility = $critter['agility'];
        $this->armor = $critter['armor'];
        $this->body = $critter['body'];
        $this->charisma = $critter['charisma'];
        $this->condition_physical = $critter['condition_physical'];
        $this->condition_stun = $critter['condition_stun'];
        $this->description = $critter['description'];
        $this->edge = $critter['edge'];
        $this->essence = $critter['essence'];
        $this->habitat = $critter['habitat'] ?? null;
        $this->initiative_base = $critter['initiative_base'];
        $this->initiative_dice = $critter['initiative_dice'];
        $this->intuition = $critter['intuition'];
        $this->logic = $critter['logic'];
        $this->magic = $critter['magic'] ?? null;
        $this->name = $critter['name'];
        $this->page = $critter['page'];
        $this->reaction = $critter['reaction'];
        $this->resonance = $critter['resonance'] ?? null;
        $this->ruleset = $critter['ruleset'];
        $this->strength = $critter['strength'];
        $this->willpower = $critter['willpower'];

        foreach ($critter['powers'] as $power) {
            try {
                $this->powers[] = new CritterPower(
                    $power['id'],
                    $power['subname'] ?? null
                );
            } catch (RuntimeException) {
                Log::warning(
                    'Shadowrun 5E Critter "{critter}" has invalid power "{power}"',
                    [
                        'critter' => $this->id,
                        'power' => $power['id'],
                    ]
                );
            }
        }
        foreach ($critter['skills'] as $skill) {
            try {
                $this->skills[] = new ActiveSkill(
                    $skill['id'],
                    $skill['level'],
                    $skill['specialization'] ?? null
                );
            } catch (RuntimeException) {
                Log::warning(
                    'Shadowrun 5E Critter "{critter}" has invalid skill "{skill}"',
                    [
                        'critter' => $this->id,
                        'skill' => $skill['id'],
                    ]
                );
            }
        }
        foreach ($critter['weaknesses'] ?? [] as $weakness) {
            try {
                $this->weaknesses[] = new CritterWeakness(
                    $weakness['id'],
                    $weakness['subname'] ?? null
                );
                // @codeCoverageIgnoreStart
            } catch (RuntimeException) {
                Log::warning(
                    'Shadowrun 5E Critter "{critter}" has invalid weakness "{weakness}"',
                    [
                        'critter' => $this->id,
                        'weakness' => $weakness['id'],
                    ]
                );
                // @codeCoverageIgnoreEnd
            }
        }
    }

    #[Override]
    public function __toString(): string
    {
        return $this->name;
    }
}
