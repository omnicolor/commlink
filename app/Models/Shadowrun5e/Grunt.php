<?php

declare(strict_types=1);

namespace App\Models\Shadowrun5e;

use RuntimeException;

/**
 * Representation of a Shadowrun 5E grunt.
 * @psalm-suppress PossiblyUnusedProperty
 */
class Grunt
{
    public ?AdeptPowerArray $adept_powers = null;
    public int $agility;
    public ArmorArray $armor;
    public AugmentationArray $augmentations;
    public int $body;
    public int $charisma;
    public ?ComplexFormArray $complex_forms = null;
    public int $condition_monitor;
    public string $description;
    public float $essence = 6.0;
    public GearArray $gear;
    public string $id;
    public ?int $initiate_grade = null;
    public int $initiative_base;
    public int $initiative_dice = 1;
    public int $intuition;
    public SkillArray $knowledge;
    public int $logic;
    public ?int $magic = null;
    public string $name;
    public int $page;
    public int $professional_rating;
    public QualityArray $qualities;
    public int $reaction;
    public ?int $resonance = null;
    public string $ruleset;
    public SkillArray $skills;
    public ?SpellArray $spells = null;
    public int $strength;
    public WeaponArray $weapons;
    public int $willpower;

    /**
     * List of all grunts.
     * @var ?array<string, mixed>
     */
    public static ?array $grunts;

    public function __construct(string $id)
    {
        $filename = config('app.data_path.shadowrun5e') . 'grunts.php';
        self::$grunts ??= require $filename;

        $this->id = \strtolower($id);
        if (!isset(self::$grunts[$this->id])) {
            throw new RuntimeException(\sprintf(
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
        $this->initiative_base = $grunt['initiative_base'];
        $this->initiative_dice = $grunt['initiative_dice'];
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
                    // @phpstan-ignore-next-line
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
                    // @phpstan-ignore-next-line
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

    public function __toString(): string
    {
        return $this->name;
    }

    /**
     * @psalm-suppress PossiblyUnusedMethod
     */
    public function getArmorValue(): int
    {
        $rating = 0;
        foreach ($this->armor ?? [] as $armor) {
            $rating += $armor->getModifiedRating();
        }

        return $rating;
    }

    /**
     * @psalm-suppress PossiblyUnusedMethod
     * @return array<int, Grunt>
     */
    public static function all(): array
    {
        $filename = config('app.data_path.shadowrun5e') . 'grunts.php';
        self::$grunts ??= require $filename;

        $grunts = [];
        /** @var string $id */
        foreach (array_keys(self::$grunts ?? []) as $id) {
            $grunts[] = new Grunt($id);
        }
        return $grunts;
    }
}
