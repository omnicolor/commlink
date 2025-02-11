<?php

declare(strict_types=1);

namespace Modules\Shadowrun5e\Models;

use Override;
use RuntimeException;
use Stringable;

use function array_key_exists;
use function array_keys;
use function config;
use function sprintf;
use function strtolower;

/**
 * Representation of a Shadowrun 5E grunt.
 */
final class Grunt implements Stringable
{
    public AdeptPowerArray|null $adept_powers = null;
    public readonly int $agility;
    public ArmorArray $armor;
    public AugmentationArray $augmentations;
    public readonly int $body;
    public readonly int $charisma;
    public ComplexFormArray|null $complex_forms = null;
    public int $condition_monitor;
    public readonly string $description;
    public float $essence = 6.0;
    public GearArray $gear;
    public readonly int|null $initiate_grade;
    public readonly int $initiative_base;
    public readonly int $initiative_dice;
    public readonly int $intuition;
    public SkillArray $knowledge;
    public readonly int $logic;
    public readonly int|null $magic;
    public readonly string $name;
    public readonly int $page;
    public readonly int $professional_rating;
    public QualityArray $qualities;
    public readonly int $reaction;
    public readonly int|null $resonance;
    public readonly string $ruleset;
    public SkillArray $skills;
    public SpellArray|null $spells = null;
    public readonly int $strength;
    public WeaponArray $weapons;
    public readonly int $willpower;

    /**
     * List of all grunts.
     * @var ?array<string, mixed>
     */
    public static ?array $grunts;

    public function __construct(public readonly string $id)
    {
        $filename = config('shadowrun5e.data_path') . 'grunts.php';
        self::$grunts ??= require $filename;

        $id = strtolower($id);
        if (!isset(self::$grunts[$this->id])) {
            throw new RuntimeException(sprintf(
                'Grunt ID "%s" was not found',
                $id,
            ));
        }

        $grunt = self::$grunts[$this->id];
        $this->agility = $grunt['agility'];
        $this->body = $grunt['body'];
        $this->charisma = $grunt['charisma'];
        $this->condition_monitor = $grunt['condition_monitor'];
        $this->description = $grunt['description'];
        $this->essence = $grunt['essence'];
        $this->initiate_grade = $grunt['initiate_grade'] ?? null;
        $this->initiative_base = $grunt['initiative_base'] ?? null;
        $this->initiative_dice = $grunt['initiative_dice'] ?? 1;
        $this->intuition = $grunt['intuition'];
        $this->logic = $grunt['logic'];
        $this->magic = $grunt['magic'] ?? null;
        $this->name = $grunt['name'];
        $this->page = $grunt['page'];
        $this->professional_rating = $grunt['professional_rating'];
        $this->reaction = $grunt['reaction'];
        $this->resonance = $grunt['resonance'] ?? null;
        $this->ruleset = $grunt['ruleset'];
        $this->strength = $grunt['strength'];
        $this->willpower = $grunt['willpower'];

        if (array_key_exists('adept_powers', $grunt)) {
            $this->adept_powers = new AdeptPowerArray();
            foreach ($grunt['adept_powers'] as $power) {
                try {
                    // @phpstan-ignore assign.propertyType
                    $this->adept_powers[] = new AdeptPower($power);
                } catch (RuntimeException) {
                    // Ignore.
                }
            }
        }

        $this->armor = new ArmorArray();
        foreach ($grunt['armor'] ?? [] as $armor) {
            try {
                $this->armor[] = Armor::build($armor);
            } catch (RuntimeException) {
                // Ignore.
            }
        }

        $this->augmentations = new AugmentationArray();
        if (null !== $this->resonance) {
            $this->complex_forms = new ComplexFormArray();
            foreach ($grunt['complex-forms'] ?? [] as $form) {
                try {
                    // @phpstan-ignore assign.propertyType
                    $this->complex_forms[] = new ComplexForm($form);
                } catch (RuntimeException) {
                    // Ignore.
                }
            }
        }

        $this->gear = new GearArray();
        foreach ($grunt['gear'] ?? [] as $gear) {
            try {
                $this->gear[] = GearFactory::get($gear);
            } catch (RuntimeException) {
                // Ignore.
            }
        }

        $this->knowledge = new SkillArray();
        foreach ($grunt['knowledge'] ?? [] as $skill) {
            try {
                $this->knowledge[] = new KnowledgeSkill(
                    $skill['name'],
                    $skill['category'],
                    $skill['level'],
                    $skill['specialization'] ?? null
                );
            } catch (RuntimeException) { // @codeCoverageIgnore
                // Ignore.
            }
        }

        $this->qualities = new QualityArray();
        foreach ($grunt['qualities'] ?? [] as $quality) {
            try {
                $this->qualities[] = new Quality($quality['id'], $quality);
            } catch (RuntimeException) { // @codeCoverageIgnore
                // Ignore.
            }
        }

        $this->skills = new SkillArray();
        foreach ($grunt['skills'] ?? [] as $skill) {
            try {
                $this->skills[] = new ActiveSkill(
                    $skill['id'],
                    $skill['level'],
                    $skill['specialization'] ?? null
                );
            } catch (RuntimeException) {
                // Ignore
            }
        }

        $this->weapons = new WeaponArray();
        foreach ($grunt['weapons'] as $weapon) {
            try {
                $this->weapons[] = Weapon::buildWeapon($weapon);
            } catch (RuntimeException) {
                // Ignore.
            }
        }
    }

    #[Override]
    public function __toString(): string
    {
        return $this->name;
    }

    public function getArmorValue(): int
    {
        $rating = 0;
        foreach ($this->armor as $armor) {
            $rating += $armor->getModifiedRating();
        }

        return $rating;
    }

    /**
     * @return array<int, Grunt>
     */
    public static function all(): array
    {
        $filename = config('shadowrun5e.data_path') . 'grunts.php';
        self::$grunts ??= require $filename;

        $grunts = [];
        /** @var string $id */
        foreach (array_keys(self::$grunts ?? []) as $id) {
            $grunts[] = new Grunt($id);
        }
        return $grunts;
    }
}
