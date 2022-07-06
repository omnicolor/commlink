<?php

declare(strict_types=1);

namespace App\Models\Shadowrun5e;

use BadMethodCallException;
use Illuminate\Support\Facades\Log;
use RuntimeException;

/**
 * Class representing a spirit in Shadowrun.
 * @method int getAgility()
 * @method int getBody()
 * @method int getCharisma()
 * @method int getEdge()
 * @method float getEssence()
 * @method int getIntuition()
 * @method int getLogic()
 * @method int getMagic()
 * @method int getReaction()
 * @method int getStrength()
 * @method int getWillpower()
 * @method int getResonance()
 */
class Spirit
{
    use ForceTrait;

    /**
     * Spirit's agility.
     * @var string
     */
    public string $agility;

    /**
     * Spirit's body.
     * @var string
     */
    public string $body;

    /**
     * Whether the spirit has been bound by the caster.
     * @var bool
     */
    public bool $bound = false;

    /**
     * Spirit's charisma.
     * @var string
     */
    public string $charisma;

    /**
     * Spirit's edge.
     * @var string
     */
    public string $edge;

    /**
     * Spirit's essence.
     * @var string
     */
    public string $essence;

    /**
     * Spirit's ID.
     * @var string
     */
    public string $id;

    /**
     * Spirit's astral initiative.
     * @var string
     */
    public string $initiativeAstral;

    /**
     * Spirit's meat-space initiative.
     * @var string
     */
    public string $initiative;

    /**
     * Spirit's intuition.
     * @var string
     */
    public string $intuition;

    /**
     * Spirit's logic.
     * @var string
     */
    public string $logic;

    /**
     a *Spirit's name.
     * @var string
     */
    public string $name;

    /**
     * Spirit's magic rating.
     * @var string
     */
    public string $magic;

    /**
     * Page number the spirit type was introduced on.
     * @var int
     */
    public int $page;

    /**
     * Powers the spirit innately has.
     * @var array<int, string>
     */
    public array $powers = [];

    /**
     * Collection of powers the spirit can choose.
     * @var array<int, string>
     */
    public array $powersOptional = [];

    /**
     * Spirit's reaction.
     * @var string
     */
    public string $reaction;

    /**
     * Ruleset the spirit type was introduced in.
     * @var string
     */
    public string $ruleset;

    /**
     * Services the spirit owes the conjuror.
     * @var int
     */
    public int $services;

    /**
     * Collection of the spirit's skills.
     * @var string[]
     */
    public array $skills = [];

    /**
     * Any special information about the spirit.
     * @var ?string
     */
    public ?string $special;

    /**
     * Spirit's strength.
     * @var string
     */
    public string $strength;

    /**
     * Spirit's willpower.
     * @var string
     */
    public string $willpower;

    /**
     * List of all spirits.
     * @var ?array<mixed>
     */
    public static ?array $spirits;

    /**
     * Constructor.
     * @param string $id ID to load
     * @param ?int $force Force of the spirit
     * @param array<int, string> $powersChosen Optional powers chosen
     * @throws RuntimeException if the ID is invalid
     */
    public function __construct(
        string $id,
        public ?int $force = null,
        public array $powersChosen = []
    ) {
        $filename = config('app.data_path.shadowrun5e') . 'spirits.php';
        self::$spirits ??= require $filename;

        $id = \strtolower($id);
        if (!isset(self::$spirits[$id])) {
            throw new RuntimeException(
                \sprintf('Spirit ID "%s" is invalid', $id)
            );
        }

        $spirit = self::$spirits[$id];
        $this->agility = $spirit['agility'];
        $this->body = $spirit['body'];
        $this->charisma = $spirit['charisma'];
        $this->edge = $spirit['edge'];
        $this->essence = $spirit['essence'];
        $this->id = $id;
        $this->initiativeAstral = $spirit['initiative-astral'];
        $this->initiative = $spirit['initiative'];
        $this->intuition = $spirit['intuition'];
        $this->logic = $spirit['logic'];
        $this->name = $spirit['name'];
        $this->magic = $spirit['magic'];
        $this->page = $spirit['page'];
        $this->powers = $spirit['powers'];
        $this->powersOptional = $spirit['powers-optional'];
        $this->reaction = $spirit['reaction'];
        $this->ruleset = $spirit['ruleset'];
        $this->skills = $spirit['skills'];
        $this->special = $spirit['special'] ?? null;
        $this->strength = $spirit['strength'];
        $this->willpower = $spirit['willpower'];
    }

    /**
     * Return an attribute with the force taken into account.
     * @param string $name Name of the method: getAgility, getBody, etc
     * @param array<mixed> $arguments Unused
     * @returns int
     * @throws BadMethodCallException
     * @throws RuntimeException
     */
    public function __call(string $name, array $arguments): int
    {
        $attribute = \strtolower(\str_replace('get', '', $name));
        $attributes = [
            'agility',
            'body',
            'charisma',
            'edge',
            'essence',
            'intuition',
            'logic',
            'magic',
            'reaction',
            'strength',
            'willpower',
        ];
        if (!\in_array($attribute, $attributes, true)) {
            throw new BadMethodCallException(\sprintf(
                '%s is not an attribute of spirits',
                \ucfirst($attribute)
            ));
        }
        if (null === $this->force) {
            throw new RuntimeException('Force has not been set');
        }
        return $this->convertFormula(
            // @phpstan-ignore-next-line
            $this->$attribute,
            'F',
            $this->force
        );
    }

    /**
     * Return the name of the spirit.
     * @return string
     */
    public function __toString(): string
    {
        return $this->name;
    }

    /**
     * Convert an initiative string like (F*2+4)+2d6 into [init, dice].
     * @param string $initiative
     * @return array<int, string|int> [base initiative, initiative dice]
     */
    protected function convertInitiative(string $initiative): array
    {
        \preg_match('/\((F.*)\)\+(\d)d6/', $initiative, $matches);
        \array_shift($matches);
        return $matches;
    }

    /**
     * Get the spirit's astral initiative.
     * @return int[] [base initiative, initiative dice]
     */
    public function getAstralInitiative(): array
    {
        list($init, $dice) = $this->convertInitiative($this->initiativeAstral);
        $init = $this->convertFormula((string)$init, 'F', (int)$this->force);
        return [$init, (int)$dice];
    }

    /**
     * Get the spirit's normal initiative.
     * @return int[] [base initiative, initiative dice]
     */
    public function getInitiative(): array
    {
        list($init, $dice) = $this->convertInitiative($this->initiative);
        $init = $this->convertFormula((string)$init, 'F', (int)$this->force);
        return [$init, (int)$dice];
    }

    /**
     * Return all of the spirit's powers.
     * @return array<int, CritterPower>
     */
    public function getPowers(): array
    {
        $powers = [];
        foreach (array_merge($this->powers, $this->powersChosen) as $power) {
            try {
                $powers[] = new CritterPower($power);
                // @codeCoverageIgnoreStart
            } catch (RuntimeException) {
                Log::error(\sprintf(
                    'Spirit "%s" has invalid power "%s"',
                    $this->id,
                    $power,
                ));
                // @codeCoverageIgnoreEnd
            }
        }
        return $powers;
    }

    /**
     * Set the force of the spirit.
     * @param int $force
     * @return Spirit
     */
    public function setForce(int $force): Spirit
    {
        $this->force = $force;
        return $this;
    }
}
