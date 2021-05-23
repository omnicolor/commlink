<?php

declare(strict_types=1);

namespace App\Models\Shadowrun5E;

/**
 * Representation of a Shadowrun sprite.
 * @method int getAttack()
 * @method int getDataProcessing()
 * @method int getFirewall()
 * @method int getInitiative()
 * @method int getResonance()
 * @method int getSleaze()
 */
class Sprite
{
    use ForceTrait;

    /**
     * Sprite's attack rating formula.
     * @var string
     */
    public string $attack;

    /**
     * Sprite's data processing rating formula.
     * @var string
     */
    public string $dataProcessing;

    /**
     * Description of the sprite.
     * @var string
     */
    public string $description;

    /**
     * Sprite's firewall rating formula.
     * @var string
     */
    public string $firewall;

    /**
     * Sprite's unique ID.
     * @var string
     */
    public string $id;

    /**
     * Sprites formula for calculating initiative.
     * @var string
     */
    public string $initiative;

    /**
     * Sprite's name.
     * @var string
     */
    public string $name;

    /**
     * Page number the sprite was introduced on.
     * @var int
     */
    public int $page;

    /**
     * Collection of powers the sprite has.
     * @var string[]
     */
    public array $powers = [];

    /**
     * Sprite's resonance formula.
     * @var string
     */
    public string $resonance;

    /**
     * Whether the sprite is registered.
     * @var bool
     */
    public bool $registered = false;

    /**
     * ID for the ruleset the sprite was introduced in.
     * @var string
     */
    public string $ruleset;

    /**
     * Collection of skills the sprite has.
     * @var string[]
     */
    public array $skills = [];

    /**
     * Sprite's sleaze formula.
     * @var string
     */
    public string $sleaze;

    /**
     * Number of tasks remaining for the sprite.
     * @var int
     */
    public int $tasks = 0;

    /**
     * List of all sprites.
     * @var array<mixed>
     */
    public static array $sprites;

    /**
     * Constructor.
     * @param string $id ID to load
     * @param ?int $level Level of the sprite
     * @throws \RuntimeException if the ID is not found
     */
    public function __construct(string $id, public ?int $level = null)
    {
        $filename = config('app.data_path.shadowrun5e') . 'sprites.php';
        self::$sprites = require $filename;
        $id = \strtolower($id);
        if (!isset(self::$sprites[$id])) {
            throw new \RuntimeException(\sprintf(
                'Sprite ID "%s" is invalid',
                $id
            ));
        }

        $sprite = self::$sprites[$id];

        $this->attack = $sprite['attack'];
        $this->dataProcessing = $sprite['data-processing'];
        $this->description = $sprite['description'];
        $this->firewall = $sprite['firewall'];
        $this->id = $id;
        $this->initiative = $sprite['initiative'];
        $this->name = $sprite['name'];
        $this->page = $sprite['page'];
        $this->powers = $sprite['powers'];
        $this->resonance = $sprite['resonance'];
        $this->ruleset = $sprite['ruleset'];
        $this->skills = $sprite['skills'];
        $this->sleaze = $sprite['sleaze'];
    }

    /**
     * Return the sprite as a string.
     * @return string
     */
    public function __toString(): string
    {
        return $this->name;
    }

    /**
     * Return an attribute with the level taken into account.
     * @param string $name Name of the method: getFirewall, getSleaze
     * @param array<mixed> $arguments Unused
     * @returns int
     * @throws \BadMethodCallException
     * @throws \RuntimeException
     */
    public function __call(string $name, array $arguments): int
    {
        $attribute = \lcfirst(\str_replace('get', '', $name));
        $attributes = [
            'attack',
            'dataProcessing',
            'firewall',
            'initiative',
            'resonance',
            'sleaze',
        ];
        if (!\in_array($attribute, $attributes, true)) {
            throw new \BadMethodCallException(\sprintf(
                '%s is not an attribute of sprites',
                \ucfirst($attribute)
            ));
        }
        if (null === $this->level) {
            throw new \RuntimeException('Level has not been set');
        }
        $formula = \str_replace(
            ['L', '(', ')'],
            [(string)$this->level, '', ''],
            // @phpstan-ignore-next-line
            (string)$this->$attribute
        );
        return $this->convertFormula($formula, 'L', $this->level);
    }

    /**
     * Set the sprite's level.
     * @param int $level Level to set for the sprite.
     * @return Sprite
     */
    public function setLevel(int $level): Sprite
    {
        $this->level = $level;
        return $this;
    }

    /**
     * If the sprite has a level, return initialized skills instead of IDs.
     * @return SkillArray
     * @throws \RuntimeException if the level isn't set
     */
    public function getSkills(): SkillArray
    {
        if (!isset($this->level)) {
            throw new \RuntimeException('Level is not set');
        }
        $skills = new SkillArray();
        foreach ($this->skills as $skill) {
            $skills[] = new ActiveSkill($skill, $this->level);
        }
        return $skills;
    }
}
