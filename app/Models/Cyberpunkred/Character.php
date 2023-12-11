<?php

declare(strict_types=1);

namespace App\Models\Cyberpunkred;

use App\Models\Character as BaseCharacter;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Support\Facades\Log;
use RuntimeException;

use function array_key_exists;
use function array_keys;
use function ceil;
use function floor;
use function ksort;

/**
 * Representation of a Cyberpunk Red character sheet.
 * @property-read array<string, ?Armor> $armor
 * @property-write array<string, null|string|Armor> $armor
 * @property int $body
 * @property int $cool
 * @property-read int $death_save
 * @property int $dexterity
 * @property int $empathy
 * @property int $empathy_current
 * @property string $handle
 * @property int $hit_points_current
 * @property-read int $hit_points_max
 * @property-read int $humanity
 * @property int $humanity_current
 * @property-read string $id
 * @property int $improvement_points
 * @property int $improvement_points_current
 * @property int $intelligence
 * @property array<string, array<string, int>> $lifepath
 * @property int $luck
 * @property int $luck_current
 * @property int $movement
 * @property int $reflexes
 * @property int $reputation
 * @property array<int, array<string, int|string>> $roles
 * @property array<string, int> $skills
 * @property array<int, array<string, int|string>> $skills_custom
 * @property int $technique
 * @property array $weapons
 * @property int $willpower
 */
class Character extends BaseCharacter
{
    use HasFactory;

    /**
     * @var array<string, mixed>
     */
    protected $attributes = [
        'system' => 'cyberpunkred',
    ];

    /**
     * @var array<string, string>
     */
    protected $casts = [
        'body' => 'integer',
        'cool' => 'integer',
        'dexterity' => 'integer',
        'empathy' => 'integer',
        'intelligence' => 'integer',
        'luck' => 'integer',
        'movement' => 'integer',
        'reflexes' => 'integer',
        'technique' => 'integer',
        'willpower' => 'integer',
    ];

    /**
     * @var array<int, string>
     */
    protected $fillable = [
        'armor',
        'body',
        'cool',
        'dexterity',
        'empathy',
        'empathy_current',
        'handle',
        'hit_points_current',
        'improvement_points',
        'improvement_points_current',
        'intelligence',
        'lifepath',
        'luck',
        'luck_current',
        'movement',
        'owner',
        'reflexes',
        'reputation',
        'roles',
        'skills',
        'technique',
        'weapons',
        'willpower',
    ];

    /**
     * @var array<int, string>
     */
    protected $hidden = [
        '_id',
    ];

    /**
     * Return the character's name.
     */
    public function __toString(): string
    {
        return $this->handle ?? 'Unnamed character';
    }

    /**
     * Force this model to only load for Cyberpunk Red characters.
     */
    protected static function booted(): void
    {
        static::addGlobalScope(
            'cyberpunkred',
            function (Builder $builder): void {
                $builder->where('system', 'cyberpunkred');
            }
        );
    }

    /**
     * @psalm-suppress PossiblyUnusedMethod
     */
    public function armor(): Attribute
    {
        return Attribute::make(
            get: function (?array $armor): array {
                $head = $body = $shield = null;
                $unworn = [];
                if (isset($armor['head'])) {
                    $head = new Armor($armor['head']);
                }
                if (isset($armor['body'])) {
                    $body = new Armor($armor['body']);
                }
                if (isset($armor['shield'])) {
                    $shield = new Armor($armor['shield']);
                }
                foreach ($armor['unworn'] ?? [] as $item) {
                    $unworn[] = new Armor($item);
                }
                return [
                    'head' => $head,
                    'body' => $body,
                    'shield' => $shield,
                    'unworn' => $unworn,
                ];
            },
        );
    }

    /**
     * Return the character's death save attribute.
     * @psalm-suppress PossiblyUnusedMethod
     */
    public function deathSave(): Attribute
    {
        return Attribute::make(
            get: function (): int {
                return (int)$this->attributes['body'];
            },
        );
    }

    /**
     * Return the character's calculated empathy.
     * @psalm-suppress PossiblyUnusedMethod
     */
    public function empathy(): Attribute
    {
        return Attribute::make(
            get: function (): int {
                return (int)floor($this->humanity / 10);
            },
        );
    }

    /**
     * Return the character's original empathy.
     * @psalm-suppress PossiblyUnusedMethod
     */
    public function getEmpathyOriginalAttribute(): int
    {
        return (int)($this->attributes['empathy'] ?? 0);
    }

    /**
     * Return the character's maximum hit points.
     */
    public function getHitPointsMaxAttribute(): int
    {
        return 10 + 5 * (int)ceil(
            (int)(
                ($this->attributes['body'] ?? 0)
                + ($this->attributes['willpower'] ?? 0)
            ) / 2
        );
    }

    /**
     * Return the character's remaining humanity.
     * @psalm-suppress PossiblyUnusedMethod
     */
    public function getHumanityAttribute(): int
    {
        return (int)($this->attributes['empathy'] ?? 0) * 10;
    }

    /**
     * Get the character's roles.
     * @psalm-suppress PossiblyUnusedMethod
     */
    public function getRoles(): RoleArray
    {
        $roles = new RoleArray();
        foreach ($this->roles ?? [] as $role) {
            try {
                $roles[] = Role::fromArray($role);
            } catch (RuntimeException) {
                Log::warning(
                    'Cyberpunk Red character "{name}" ({id}) has invalid role "{role}"',
                    [
                        'name' => $this->handle,
                        'id' => $this->id,
                        'role' => (string)$role['role'],
                    ]
                );
            }
        }
        return $roles;
    }

    /**
     * Get the character's seriously wounded threshold.
     * @psalm-suppress PossiblyUnusedMethod
     */
    public function getSeriouslyWoundedThresholdAttribute(): int
    {
        return (int)ceil($this->getHitPointsMaxAttribute() / 2);
    }

    /**
     * Get the skills the character has ranks in.
     * @psalm-suppress PossiblyUnusedMethod
     */
    public function getSkills(): SkillArray
    {
        $skills = new SkillArray();
        foreach ($this->skills ?? [] as $skill => $level) {
            try {
                $skills[] = new Skill($skill, $level);
            } catch (RuntimeException $ex) {
                Log::warning(
                    'Cyberpunk Red character "{name}" ({id}) has invalid skill "{skill}"',
                    [
                        'name' => $this->handle,
                        'id' => $this->id,
                        'skill' => $skill,
                    ]
                );
            }
        }
        return $skills;
    }

    /**
     * Get all skills available, whether the character has levels or not.
     */
    public function getAllSkills(): SkillArray
    {
        $filename = config('app.data_path.cyberpunkred') . 'skills.php';
        $rawSkills = require $filename;
        $skills = new SkillArray();
        /** @var string $id */
        foreach (array_keys($rawSkills) as $id) {
            if (array_key_exists($id, $this->skills ?? [])) {
                $skills[$id] = new Skill($id, $this->skills[$id]);
                continue;
            }
            $skills[$id] = new Skill($id);
        }
        return $skills;
    }

    /**
     * Get skills grouped by category.
     * @psalm-suppress PossiblyUnusedMethod
     * @return array<string, SkillArray>
     */
    public function getSkillsByCategory(): array
    {
        $allSkills = $this->getAllSkills();
        $skills = [];
        foreach ($allSkills as $skill) {
            if (!array_key_exists($skill->category, $skills)) {
                $skills[$skill->category] = new SkillArray();
            }
            $skills[$skill->category][] = $skill;
        }
        ksort($skills);
        return $skills;
    }

    /**
     * Return the character's weapons.
     * @psalm-suppress PossiblyUnusedMethod
     */
    public function getWeapons(?string $type = null): WeaponArray
    {
        if (
            null !== $type
            && Weapon::TYPE_MELEE !== $type
            && Weapon::TYPE_RANGED !== $type
        ) {
            throw new RuntimeException('Invalid Weapon Type');
        }

        $weapons = new WeaponArray();
        foreach ($this->attributes['weapons'] ?? [] as $rawWeapon) {
            try {
                $weapon = Weapon::build($rawWeapon);
                if (null === $type) {
                    $weapons[] = $weapon;
                    continue;
                }
                if (Weapon::TYPE_RANGED === $type && $weapon instanceof RangedWeapon) {
                    $weapons[] = $weapon;
                    continue;
                }
                if (Weapon::TYPE_MELEE === $type && $weapon instanceof MeleeWeapon) {
                    $weapons[] = $weapon;
                    continue;
                }
            } catch (RuntimeException $ex) {
                Log::warning(
                    'Cyberpunk Red character "{name}" ({id}) has invalid weapon ID "{weapon}"',
                    [
                        'name' => $this->name,
                        'id' => $this->id,
                        'weapon' => $rawWeapon['id'],
                    ]
                );
            }
        }
        return $weapons;
    }
}
