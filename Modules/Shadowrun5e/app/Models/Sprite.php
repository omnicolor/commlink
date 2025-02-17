<?php

declare(strict_types=1);

namespace Modules\Shadowrun5e\Models;

use App\Traits\FormulaConverter;
use BadMethodCallException;
use Illuminate\Support\Facades\Log;
use Override;
use RuntimeException;
use Stringable;

use function config;
use function lcfirst;
use function sprintf;
use function str_replace;
use function strtolower;
use function ucfirst;

/**
 * Representation of a Shadowrun sprite.
 * @method int getAttack()
 * @method int getDataProcessing()
 * @method int getFirewall()
 * @method int getInitiative()
 * @method int getResonance()
 * @method int getSleaze()
 */
class Sprite implements Stringable
{
    use FormulaConverter;

    /**
     * Sprite's attack rating formula.
     */
    public readonly string $attack;

    /**
     * Sprite's data processing rating formula.
     */
    public readonly string $dataProcessing;
    public readonly string $description;

    /**
     * Sprite's firewall rating formula.
     */
    public readonly string $firewall;

    /**
     * Sprites formula for calculating initiative.
     */
    public readonly string $initiative;
    public readonly string $name;
    public readonly int $page;

    /**
     * Collection of powers the sprite has.
     * @var array<int, string>
     */
    public array $powers = [];

    /**
     * Sprite's resonance formula.
     */
    public readonly string $resonance;

    /**
     * Whether the sprite is registered.
     */
    public bool $registered = false;
    public readonly string $ruleset;

    /**
     * Collection of skills the sprite has.
     * @var array<int, string>
     */
    public array $skills = [];

    /**
     * Sprite's sleaze formula.
     */
    public readonly string $sleaze;

    /**
     * Number of tasks remaining for the sprite.
     */
    public int $tasks = 0;

    /**
     * List of all sprites.
     * @var array<string, array<string, mixed>>
     */
    public static array $sprites;

    /**
     * Constructor.
     * @throws RuntimeException if the ID is not found
     */
    public function __construct(public readonly string $id, public ?int $level = null)
    {
        $filename = config('shadowrun5e.data_path') . 'sprites.php';
        self::$sprites = require $filename;
        $id = strtolower($id);
        if (!isset(self::$sprites[$id])) {
            throw new RuntimeException(sprintf(
                'Sprite ID "%s" is invalid',
                $id
            ));
        }

        $sprite = self::$sprites[$id];
        $this->attack = $sprite['attack'];
        $this->dataProcessing = $sprite['data-processing'];
        $this->description = $sprite['description'];
        $this->firewall = $sprite['firewall'];
        $this->initiative = $sprite['initiative'];
        $this->name = $sprite['name'];
        $this->page = $sprite['page'];
        $this->powers = $sprite['powers'];
        $this->resonance = $sprite['resonance'];
        $this->ruleset = $sprite['ruleset'];
        $this->skills = $sprite['skills'];
        $this->sleaze = $sprite['sleaze'];
    }

    #[Override]
    public function __toString(): string
    {
        return $this->name;
    }

    /**
     * Return an attribute with the level taken into account.
     * @param array<mixed, mixed> $_arguments Unused
     * @throws BadMethodCallException
     * @throws RuntimeException
     */
    public function __call(string $name, array $_arguments): int
    {
        $attribute = lcfirst(str_replace('get', '', $name));
        $attribute = match ($attribute) {
            'attack' => $this->attack,
            'dataProcessing' => $this->dataProcessing,
            'firewall' => $this->firewall,
            'initiative' => $this->initiative,
            'resonance' => $this->resonance,
            'sleaze' => $this->sleaze,
            default => throw new BadMethodCallException(sprintf(
                '%s is not an attribute of sprites',
                ucfirst($attribute)
            )),
        };
        if (null === $this->level) {
            throw new RuntimeException('Level has not been set');
        }
        $formula = str_replace(
            ['L', '(', ')'],
            [(string)$this->level, '', ''],
            $attribute
        );
        return self::convertFormula($formula, 'L', $this->level);
    }

    public function setLevel(int $level): Sprite
    {
        $this->level = $level;
        return $this;
    }

    /**
     * Return a collection of the sprite's powers.
     * @return array<int, SpritePower>
     */
    public function getPowers(): array
    {
        $powers = [];
        foreach ($this->powers as $power) {
            try {
                $powers[] = new SpritePower($power);
                // @codeCoverageIgnoreStart
            } catch (RuntimeException) {
                Log::error(
                    'Shadowrun 5E Sprite "{sprite}" has invalid power "{power}"',
                    [
                        'sprite' => $this->id,
                        'power' => $power,
                    ]
                );
                // @codeCoverageIgnoreEnd
            }
        }
        return $powers;
    }

    /**
     * If the sprite has a level, return initialized skills instead of IDs.
     * @throws RuntimeException if the level isn't set
     */
    public function getSkills(): SkillArray
    {
        if (!isset($this->level)) {
            throw new RuntimeException('Level is not set');
        }
        $skills = new SkillArray();
        foreach ($this->skills as $skill) {
            $skills[] = new ActiveSkill($skill, $this->level);
        }
        return $skills;
    }
}
