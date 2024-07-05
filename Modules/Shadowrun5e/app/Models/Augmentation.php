<?php

declare(strict_types=1);

namespace Modules\Shadowrun5e\Models;

use RuntimeException;
use Stringable;

use function config;
use function sprintf;
use function strtolower;

/**
 * An augmentation (either cyberware or bioware) that the character can spend
 * essence on.
 */
class Augmentation implements Stringable
{
    public const string GRADE_ALPHA = 'Alpha';
    public const string GRADE_BETA = 'Beta';
    public const string GRADE_DELTA = 'Delta';
    public const string GRADE_GAMMA = 'Gamma';
    public const string GRADE_OMEGA = 'Omega';
    public const string GRADE_STANDARD = 'Standard';
    public const string GRADE_USED = 'Used';

    public const string TYPE_BIOWARE = 'bioware';
    public const string TYPE_CYBERWARE = 'cyberware';

    /**
     * Whether the augmentation is currently active.
     * @psalm-suppress PossiblyUnusedProperty
     */
    public bool $active = true;

    /**
     * Availability code for the augmentation.
     * @psalm-suppress PossiblyUnusedProperty
     */
    public string $availability;

    /**
     * Base cost of the augmentation.
     */
    public ?int $cost;

    /**
     * Description of the augmentation.
     * @psalm-suppress PossiblyUnusedProperty
     */
    public string $description;

    /**
     * List of effects the augmentation has.
     * @var array<string, int>
     */
    public array $effects = [];

    /**
     * Base essence cost of the augmentation.
     */
    public float $essence;

    /**
     * Grade of the augmentation.
     */
    public ?string $grade;

    /**
     * List of augmentations this one is incompatible with.
     * @psalm-suppress PossiblyUnusedProperty
     * @var array<int, string>
     */
    public array $incompatibilities = [];

    /**
     * List of modifications to this augmentation.
     */
    public AugmentationArray $modifications;

    /**
     * Name of the augmentation.
     */
    public string $name;

    /**
     * Rating of the augmentation.
     * @psalm-suppress PossiblyUnusedProperty
     * @var int|string|null
     */
    public $rating;

    /**
     * Type of augmentation, which should be either cyberware or bioware.
     * @psalm-suppress PossiblyUnusedProperty
     */
    public string $type;

    /**
     * Loaded know- or skill- softs for Skilljacks.
     * @psalm-suppress PossiblyUnusedProperty
     * @var ?array<mixed>
     */
    public ?array $softs;

    /**
     * List of all augmentations.
     * @var ?array<mixed>
     */
    public static ?array $augmentations;

    /**
     * @throws RuntimeException If the augmentation isn't valid
     */
    public function __construct(public string $id, ?string $grade = null)
    {
        $filename = config('shadowrun5e.data_path') . 'cyberware.php';
        /** @psalm-suppress UnresolvableInclude */
        self::$augmentations ??= require $filename;

        $id = strtolower($id);
        if (!isset(self::$augmentations[$id])) {
            throw new RuntimeException(
                sprintf('Augmentation "%s" is invalid', $id)
            );
        }

        $augmentation = self::$augmentations[$id];

        $this->availability = (string)$augmentation['availability'];
        $this->cost = $augmentation['cost'];
        $this->description = $augmentation['description'];
        $this->effects = $augmentation['effects'] ?? [];
        $this->essence = $augmentation['essence'];
        $this->incompatibilities = $augmentation['incompatibilities'] ?? [];
        $this->modifications = new AugmentationArray();
        foreach ($augmentation['modifications'] ?? [] as $mod) {
            // Built-in modifications are free
            $aug = new Augmentation($mod, $grade);
            $aug->cost = null;
            $this->modifications[] = $aug;
        }
        $this->name = $augmentation['name'];
        $this->rating = $augmentation['rating'] ?? null;
        $this->type = $augmentation['type'] ?? self::TYPE_CYBERWARE;

        $this->grade = $grade;
        switch ($grade) {
            case self::GRADE_ALPHA:
                $this->essence *= 0.8;
                break;
            case self::GRADE_BETA:
                $this->essence *= 0.7;
                break;
            case self::GRADE_DELTA:
                $this->essence *= 0.5;
                break;
            case self::GRADE_USED:
                $this->essence *= 1.25;
                break;
        }
    }

    public function __toString(): string
    {
        return $this->name;
    }

    /**
     * Build an augmentation from a Mongo array.
     * @param array<string, mixed> $augmentation
     * @throws RuntimeException If the augmentation isn't valid
     */
    public static function build(array $augmentation): Augmentation
    {
        $aug = new self($augmentation['id'], $augmentation['grade'] ?? null);
        foreach ($augmentation['modifications'] ?? [] as $mod) {
            $aug->modifications[] = new Augmentation(
                $mod,
                $augmentation['grade'] ?? null
            );
        }
        if (isset($augmentation['essence'])) {
            // Handle things like Prototype Transhuman giving
            // augmentations without essence cost.
            $aug->essence = $augmentation['essence'];
        }
        if ('Skilljack' === $aug->name) {
            $aug->softs = $augmentation['softs'] ?? [];
            $aug->active = $augmentation['active'] ?? false;
        }
        return $aug;
    }

    /**
     * Try to find an augmentation by its name and optional rating.
     * @throws RuntimeException
     */
    public static function findByName(
        string $name,
        int|null|string $rating = null,
    ): Augmentation {
        $filename = config('shadowrun5e.data_path') . 'cyberware.php';
        /** @psalm-suppress UnresolvableInclude */
        self::$augmentations ??= require $filename;

        foreach (self::$augmentations as $aug) {
            if (strtolower((string)$aug['name']) !== strtolower($name)) {
                continue;
            }

            if (null === $rating) {
                return new self($aug['id']);
            }

            if (is_int($rating) && $rating === $aug['rating']) {
                return new self($aug['id']);
            }

            if (strtolower((string)$rating) === strtolower((string)$aug['rating'])) {
                return new self($aug['id']);
            }
        }

        throw new RuntimeException(sprintf(
            'Augmentation "%s" was not found',
            $name
        ));
    }

    /**
     * Return the cost of the augmentation, including modifications and grade.
     * @psalm-suppress PossiblyUnusedMethod
     */
    public function getCost(): int
    {
        $cost = $this->cost;
        $cost = match ($this->grade) {
            self::GRADE_ALPHA => (float)$cost * 1.2,
            self::GRADE_BETA => (float)$cost * 1.5,
            self::GRADE_DELTA => (float)$cost * 2.5,
            self::GRADE_USED => (float)$cost * 0.75,
            default => (float)$cost * 1,
        };
        foreach ($this->modifications as $mod) {
            $cost += $mod->getCost();
        }
        return (int)$cost;
    }
}
