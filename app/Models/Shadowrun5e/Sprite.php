<?php

declare(strict_types=1);

namespace App\Models\Shadowrun5e;

use BadMethodCallException;
use Illuminate\Support\Facades\Log;
use RuntimeException;
use Stringable;

use function config;
use function in_array;
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
 * @psalm-suppress PossiblyUnusedProperty
 */
class Sprite implements Stringable
{
    use ForceTrait;

    /**
     * Sprite's attack rating formula.
     */
    public string $attack;

    /**
     * Sprite's data processing rating formula.
     */
    public string $dataProcessing;

    /**
     * Description of the sprite.
     */
    public string $description;

    /**
     * Sprite's firewall rating formula.
     */
    public string $firewall;

    /**
     * Sprites formula for calculating initiative.
     */
    public string $initiative;

    /**
     * Sprite's name.
     */
    public string $name;

    /**
     * Page number the sprite was introduced on.
     */
    public int $page;

    /**
     * Collection of powers the sprite has.
     * @var array<int, string>
     */
    public array $powers = [];

    /**
     * Sprite's resonance formula.
     */
    public string $resonance;

    /**
     * Whether the sprite is registered.
     */
    public bool $registered = false;

    /**
     * ID for the ruleset the sprite was introduced in.
     */
    public string $ruleset;

    /**
     * Collection of skills the sprite has.
     * @var array<int, string>
     */
    public array $skills = [];

    /**
     * Sprite's sleaze formula.
     */
    public string $sleaze;

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
    public function __construct(public string $id, public ?int $level = null)
    {
        $filename = config('app.data_path.shadowrun5e') . 'sprites.php';
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

    public function __toString(): string
    {
        return $this->name;
    }

    /**
     * Return an attribute with the level taken into account.
     * @param array<mixed, mixed> $_arguments Unused
     * @psalm-suppress PossiblyUnusedMethod
     * @throws BadMethodCallException
     * @throws RuntimeException
     */
    public function __call(string $name, array $_arguments): int
    {
        $attribute = lcfirst(str_replace('get', '', $name));
        $attributes = [
            'attack',
            'dataProcessing',
            'firewall',
            'initiative',
            'resonance',
            'sleaze',
        ];
        if (!in_array($attribute, $attributes, true)) {
            throw new BadMethodCallException(sprintf(
                '%s is not an attribute of sprites',
                ucfirst($attribute)
            ));
        }
        if (null === $this->level) {
            throw new RuntimeException('Level has not been set');
        }
        $formula = str_replace(
            ['L', '(', ')'],
            [(string)$this->level, '', ''],
            // @phpstan-ignore-next-line
            (string)$this->$attribute
        );
        return self::convertFormula($formula, 'L', $this->level);
    }

    /**
     * @psalm-suppress PossiblyUnusedMethod
     */
    public function setLevel(int $level): Sprite
    {
        $this->level = $level;
        return $this;
    }

    /**
     * Return a collection of the sprite's powers.
     * @psalm-suppress PossiblyUnusedMethod
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
     * @psalm-suppress PossiblyUnusedMethod
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
