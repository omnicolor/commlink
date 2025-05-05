<?php

declare(strict_types=1);

namespace Modules\Shadowrun5e\Models;

use App\Traits\FormulaConverter;
use BadMethodCallException;
use Illuminate\Support\Facades\Log;
use Override;
use RuntimeException;
use Stringable;

use function array_merge;
use function array_shift;
use function config;
use function preg_match;
use function sprintf;
use function str_replace;
use function strtolower;
use function ucfirst;

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
class Spirit implements Stringable
{
    use FormulaConverter;

    public string $agility;
    public readonly string $body;

    /**
     * Whether the spirit has been bound by the caster.
     */
    public bool $bound = false;
    public readonly string $charisma;
    public readonly string $edge;
    public readonly string $essence;

    /**
     * Spirit's astral initiative.
     */
    public readonly string $initiativeAstral;

    /**
     * Spirit's meat-space initiative.
     */
    public readonly string $initiative;
    public readonly string $intuition;
    public readonly string $logic;
    public readonly string $name;

    /**
     * Spirit's magic rating.
     */
    public string $magic;
    public readonly int $page;

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
    public readonly string $reaction;
    public readonly string $ruleset;

    /**
     * Services the spirit owes the conjuror.
     */
    public int $services;

    /**
     * Collection of the spirit's skills.
     * @var array<int, string>
     */
    public array $skills = [];

    /**
     * Any special information about the spirit.
     */
    public readonly null|string $special;
    public readonly string $strength;
    public readonly string $willpower;

    /**
     * List of all spirits.
     * @var ?array<string, array<string, mixed>>
     */
    public static ?array $spirits;

    /**
     * Constructor.
     * @param ?int $force Force of the spirit
     * @param array<int, string> $powersChosen Optional powers chosen
     * @throws RuntimeException if the ID is invalid
     */
    public function __construct(
        public string $id,
        public ?int $force = null,
        public array $powersChosen = [],
    ) {
        $filename = config('shadowrun5e.data_path') . 'spirits.php';
        self::$spirits ??= require $filename;

        $id = strtolower($id);
        if (!isset(self::$spirits[$id])) {
            throw new RuntimeException(
                sprintf('Spirit ID "%s" is invalid', $id)
            );
        }

        $spirit = self::$spirits[$id];
        $this->agility = $spirit['agility'];
        $this->body = $spirit['body'];
        $this->charisma = $spirit['charisma'];
        $this->edge = $spirit['edge'];
        $this->essence = $spirit['essence'];
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
     * @param array<mixed, mixed> $arguments Unused
     * @throws BadMethodCallException
     * @throws RuntimeException
     */
    public function __call(string $name, array $arguments): int
    {
        $attribute = strtolower(str_replace('get', '', $name));
        $attribute = match ($attribute) {
            'agility' => $this->agility,
            'body' => $this->body,
            'charisma' => $this->charisma,
            'edge' => $this->edge,
            'essence' => $this->essence,
            'intuition' => $this->intuition,
            'logic' => $this->logic,
            'magic' => $this->magic,
            'reaction' => $this->reaction,
            'strength' => $this->strength,
            'willpower' => $this->willpower,
            default => throw new BadMethodCallException(sprintf(
                '%s is not an attribute of spirits',
                ucfirst($attribute)
            )),
        };
        if (null === $this->force) {
            throw new RuntimeException('Force has not been set');
        }
        return self::convertFormula($attribute, 'F', $this->force);
    }

    #[Override]
    public function __toString(): string
    {
        return $this->name;
    }

    /**
     * Convert an initiative string like (F*2+4)+2d6 into [init, dice].
     * @return array<int, string|int> [base initiative, initiative dice]
     */
    protected function convertInitiative(string $initiative): array
    {
        preg_match('/\((F.*)\)\+(\d)d6/', $initiative, $matches);
        array_shift($matches);
        return $matches;
    }

    /**
     * Get the spirit's astral initiative.
     * @return array<int, int> [base initiative, initiative dice]
     */
    public function getAstralInitiative(): array
    {
        [$init, $dice] = $this->convertInitiative($this->initiativeAstral);
        $init = self::convertFormula((string)$init, 'F', (int)$this->force);
        return [$init, (int)$dice];
    }

    /**
     * Get the spirit's normal initiative.
     * @return array<int, int> [base initiative, initiative dice]
     */
    public function getInitiative(): array
    {
        [$init, $dice] = $this->convertInitiative($this->initiative);
        $init = self::convertFormula((string)$init, 'F', (int)$this->force);
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
                Log::error(
                    'Shadowrun 5E spirit "{spirit}" has invalid power "{power}"',
                    [
                        'spirit' => $this->id,
                        'power' => $power,
                    ]
                );
                // @codeCoverageIgnoreEnd
            }
        }
        return $powers;
    }

    /**
     * Set the force of the spirit.
     */
    public function setForce(int $force): Spirit
    {
        $this->force = $force;
        return $this;
    }
}
