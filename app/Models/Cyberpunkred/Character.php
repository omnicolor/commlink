<?php

declare(strict_types=1);

namespace App\Models\Cyberpunkred;

use App\Models\Character as BaseCharacter;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Support\Facades\Log;
use RuntimeException;

/**
 * Representation of a Cyberpunk Red character sheet.
 * @property int $body
 * @property int $cool
 * @property int $dexterity
 * @property int $empathy
 * @property string $handle
 * @property int $hitPointsCurrent
 * @property-read int $hitPointsMax
 * @property-read int $humanity
 * @property-read string $id
 * @property int $intelligence
 * @property array<string, array<string, int>> $lifepath
 * @property int $luck
 * @property int $movement
 * @property int $reflexes
 * @property array<int, array<string, int|string>> $roles
 * @property array<string, int> $skills
 * @property int $technique
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
        'body',
        'cool',
        'dexterity',
        'empathy',
        'handle',
        'hitPointsCurrent',
        'intelligence',
        'lifepath',
        'luck',
        'movement',
        'owner',
        'reflexes',
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
     * @return string
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
     * Return the character's death save attribute.
     * @return int
     */
    public function getDeathSaveAttribute(): int
    {
        return (int)$this->attributes['body'];
    }

    /**
     * Return the character's calculated empathy.
     * @return int
     */
    public function getEmpathyAttribute(): int
    {
        return (int)\floor($this->humanity / 10);
    }

    /**
     * Return the character's original empathy.
     * @return int
     */
    public function getEmpathyOriginalAttribute(): int
    {
        return (int)($this->attributes['empathy'] ?? 0);
    }

    /**
     * Return the character's maximum hit points.
     * @return int
     */
    public function getHitPointsMaxAttribute(): int
    {
        return 10 + 5 * (int)\ceil(
            (int)(
                ($this->attributes['body'] ?? 0)
                + ($this->attributes['willpower'] ?? 0)
            ) / 2
        );
    }

    /**
     * Return the character's remaining humanity.
     * @return int
     */
    public function getHumanityAttribute(): int
    {
        return (int)($this->attributes['empathy'] ?? 0) * 10;
    }

    /**
     * Get the character's roles.
     * @return RoleArray
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
     * @return int
     */
    public function getSeriouslyWoundedThresholdAttribute(): int
    {
        return (int)\ceil($this->getHitPointsMaxAttribute() / 2);
    }

    /**
     * Get the skills the character has ranks in.
     * @return SkillArray
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
     * @return SkillArray
     */
    public function getAllSkills(): SkillArray
    {
        $filename = config('app.data_path.cyberpunkred') . 'skills.php';
        $rawSkills = require $filename;
        $skills = new SkillArray();
        /** @var string $id */
        foreach (array_keys($rawSkills) as $id) {
            if (\array_key_exists($id, $this->skills ?? [])) {
                $skills[$id] = new Skill($id, $this->skills[$id]);
                continue;
            }
            $skills[$id] = new Skill($id);
        }
        return $skills;
    }

    /**
     * Get skills grouped by category.
     * @return array<string, SkillArray>
     */
    public function getSkillsByCategory(): array
    {
        $allSkills = $this->getAllSkills();
        $skills = [];
        foreach ($allSkills as $skill) {
            if (!\array_key_exists($skill->category, $skills)) {
                $skills[$skill->category] = new SkillArray();
            }
            $skills[$skill->category][] = $skill;
        }
        \ksort($skills);
        return $skills;
    }

    /**
     * Return the character's weapons.
     * @psalm-suppress PossiblyUnusedMethod
     * @return WeaponArray
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
