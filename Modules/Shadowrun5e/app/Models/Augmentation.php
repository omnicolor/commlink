<?php

declare(strict_types=1);

namespace Modules\Shadowrun5e\Models;

use Override;
use RuntimeException;
use Stringable;

use function config;
use function sprintf;
use function strtolower;
use function ucfirst;

/**
 * An augmentation (either cyberware or bioware) that the character can spend
 * essence on.
 */
final class Augmentation implements Stringable
{
    public const string TYPE_BIOWARE = 'bioware';
    public const string TYPE_CYBERWARE = 'cyberware';

    /**
     * Whether the augmentation is currently active.
     */
    public bool $active = true;

    /**
     * Availability code for the augmentation.
     */
    public readonly string $availability;

    /**
     * Base cost of the augmentation.
     */
    public int $cost;

    /**
     * Description of the augmentation.
     */
    public readonly string $description;

    /**
     * List of effects the augmentation has.
     * @var array<string, int>
     */
    public array $effects = [];

    /**
     * Base essence cost of the augmentation.
     */
    public float $essence;

    public readonly AugmentationGrade $grade;

    /**
     * List of augmentations this one is incompatible with.
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
    public readonly string $name;

    public int|null|string $rating;

    /**
     * Type of augmentation, which should be either cyberware or bioware.
     */
    public readonly string $type;

    /**
     * Loaded know- or skill- softs for Skilljacks.
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
    public function __construct(
        public readonly string $id,
        AugmentationGrade|null|string $grade = null,
    ) {
        $filename = config('shadowrun5e.data_path') . 'cyberware.php';
        self::$augmentations ??= require $filename;

        $id = strtolower($id);
        if (!isset(self::$augmentations[$id])) {
            throw new RuntimeException(
                sprintf('Augmentation "%s" is invalid', $id)
            );
        }

        if (!($grade instanceof AugmentationGrade)) {
            $grade = AugmentationGrade::tryFrom(ucfirst((string)$grade))
                ?? AugmentationGrade::Standard;
        }
        $this->grade = $grade;
        $augmentation = self::$augmentations[$id];

        $this->availability = (string)$augmentation['availability'];
        $this->cost = $augmentation['cost'];
        $this->description = $augmentation['description'];
        $this->effects = $augmentation['effects'] ?? [];
        $this->essence = $augmentation['essence'] * $this->grade->essenceModifier();
        $this->incompatibilities = $augmentation['incompatibilities'] ?? [];
        $this->modifications = new AugmentationArray();
        foreach ($augmentation['modifications'] ?? [] as $mod) {
            // Built-in modifications are free.
            $aug = new Augmentation($mod, $grade);
            $aug->cost = 0;
            $this->modifications[] = $aug;
        }
        $this->name = $augmentation['name'];
        $this->rating = $augmentation['rating'] ?? null;
        $this->type = $augmentation['type'] ?? self::TYPE_CYBERWARE;
    }

    #[Override]
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
     */
    public function getCost(): int
    {
        $cost = $this->cost * $this->grade->costModifier();
        foreach ($this->modifications as $mod) {
            $cost += $mod->getCost();
        }
        return (int)$cost;
    }
}
