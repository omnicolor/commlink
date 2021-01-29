<?php

declare(strict_types=1);

namespace App\Models\Shadowrun5E;

use Illuminate\Database\Eloquent\Builder;

/**
 * Representation of a Shadowrun 5E character.
 * @property ?array<int, array<string, mixed>> $armor
 * @property ?array<int, array<string, mixed>> $augmentations
 * @property string $handle
 * @property string $id
 * @property ?array<string, mixed> $magics
 * @property ?array<int, array<string, mixed>> $qualities
 * @property ?array<int, array<string, mixed>> $skills
 */
class Character extends \App\Models\Character
{
    /**
     * @var array<string, mixed>
     */
    protected $attributes = [
        'type' => 'shadowrun5e',
    ];

    /**
     * @var string[]
     */
    protected $fillable = [
        'armor',
        'augmentations',
        'handle',
        'magics',
        'qualities',
        'skills',
    ];

    /**
     * @var string[]
     */
    protected $hidden = [
        '_id',
    ];

    /**
     * Return the character's handle.
     * @return string
     */
    public function __toString(): string
    {
        return $this->handle;
    }

    /**
     * The "booted" method of the model.
     */
    protected static function booted(): void
    {
        static::addGlobalScope(
            'shadowrun5e',
            function (Builder $builder): void {
                $builder->where('type', 'shadowrun5e');
            }
        );
    }

    /**
     * Return the character's adept powers.
     * @return AdeptPowerArray
     */
    public function getAdeptPowers(): AdeptPowerArray
    {
        $powers = new AdeptPowerArray();
        if (null === $this->magics || !isset($this->magics['powers'])) {
            return $powers;
        }
        foreach ($this->magics['powers'] as $power) {
            try {
                $powers[] = new AdeptPower($power);
            } catch (\RuntimeException $ex) {
                \Log::warning(sprintf(
                    'Shadowrun5E character "%s" (%s) has invalid adept power "%s"',
                    $this->handle,
                    $this->_id,
                    $power
                ));
            }
        }
        return $powers;
    }

    /**
     * Return the character's armor.
     * @return ArmorArray
     */
    public function getArmor(): ArmorArray
    {
        $armor = new ArmorArray();
        if (null === $this->armor) {
            return $armor;
        }
        foreach ($this->armor as $rawArmor) {
            try {
                $armor[] = Armor::build($rawArmor);
            } catch (\RuntimeException $ex) {
                \Log::warning(sprintf(
                    'Shadowrun5E character "%s" (%s) has invalid armor "%s"',
                    $this->handle,
                    $this->_id,
                    $rawArmor['id']
                ));
            }
        }
        return $armor;
    }

    /**
     * Return the character's augmentations.
     * @return AugmentationArray
     */
    public function getAugmentations(): AugmentationArray
    {
        $augmentations = new AugmentationArray();
        if (null === $this->augmentations) {
            return $augmentations;
        }
        foreach ($this->augmentations as $augmentation) {
            try {
                $augmentations[] = Augmentation::build($augmentation);
            } catch (\RuntimeException $ex) {
                \Log::warning(sprintf(
                    'Shadowrun5E character "%s" (%s) has invalid augmentation "%s"',
                    $this->handle,
                    $this->_id,
                    $augmentation['id']
                ));
            }
        }
        return $augmentations;
    }

    /**
     * Return the character's qualities (if they have any).
     * @return QualityArray
     */
    public function getQualities(): QualityArray
    {
        $qualities = new QualityArray();
        if (null === $this->qualities) {
            return $qualities;
        }
        foreach ($this->qualities as $quality) {
            try {
                $qualities[] = new Quality($quality['id'], $quality);
            } catch (\RuntimeException $ex) {
                \Log::warning(sprintf(
                    'Shadowrun5E character "%s" (%s) has invalid quality "%s"',
                    $this->handle,
                    $this->_id,
                    $quality['id']
                ));
            }
        }
        return $qualities;
    }

    /**
     * Return the character's skills.
     * @return SkillArray
     */
    public function getSkills(): SkillArray
    {
        $skills = new SkillArray();
        if (null === $this->skills) {
            return $skills;
        }
        foreach ($this->skills as $skill) {
            try {
                $skills[] = new ActiveSkill(
                    $skill['id'],
                    $skill['level'],
                    $skill['specialization'] ?? null
                );
            } catch (\RuntimeException $ex) {
                \Log::warning(sprintf(
                    'Shadowrun5E character "%s" (%s) has invalid skill "%s"',
                    $this->handle,
                    $this->_id,
                    $skill['id']
                ));
            }
        }
        return $skills;
    }
}
