<?php

declare(strict_types=1);

namespace App\Models\Shadowrun5E;

use Illuminate\Database\Eloquent\Builder;

/**
 * Representation of a Shadowrun 5E character.
 * @property ?array<int, array<string, mixed>> $armor
 * @property ?array<int, array<string, mixed>> $augmentations
 * @property ?array<int, string> $complexForms
 * @property ?array<int, array<string, mixed>> $gear
 * @property string $handle
 * @property string $id
 * @property ?array<string, string> $priorities
 * @property ?array<string, mixed> $magics
 * @property ?array<int, array<string, mixed>> $qualities
 * @property ?array<int, array<string, mixed>> $skills
 * @property ?array<string, ?int> $skillGroups
 */
class Character extends \App\Models\Character
{
    /**
     * @var array<string, mixed>
     */
    protected $attributes = [
        'system' => 'shadowrun5e',
    ];

    /**
     * @var string[]
     */
    protected $fillable = [
        'agility',
        'armor',
        'augmentations',
        'birthdate',
        'birthplace',
        'body',
        'campaign',
        'charisma',
        'complexForms',
        'edge',
        'edgeCurrent',
        'eyes',
        'gear',
        'hair',
        'handle',
        'height',
        'intuition',
        'karma',
        'karmaCurrent',
        'logic',
        'magic',
        'magics',
        'nuyen',
        'priorities',
        'qualities',
        'reaction',
        'realName',
        'resonance',
        'sex',
        'skills',
        'skillGroups',
        'streetCred',
        'strength',
        'weight',
        'willpower',
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
                $builder->where('system', 'shadowrun5e');
            }
        );
    }

    /**
     * Change a dashed ID to a camel case ID.
     * @param string $string ID to convert
     * @return string Converted ID
     */
    protected function dashedToCamel(string $string): string
    {
        if ('' == $string) {
            return '';
        }
        $string = str_replace('-', ' ', $string);
        $string = ucwords($string);
        $string = str_replace(' ', '', $string);
        $string[0] = strtolower($string[0]);
        return $string;
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
     * Return the character's complex form.
     * @return ComplexFormArray
     */
    public function getComplexForms(): ComplexFormArray
    {
        $forms = new ComplexFormArray();
        foreach ($this->complexForms ?? [] as $form) {
            try {
                $forms[] = new ComplexForm($form);
            } catch (\RuntimeException $ex) {
                \Log::warning(sprintf(
                    'Shadowrun5E character "%s" (%s) has invalid complex form "%s"',
                    $this->handle,
                    $this->_id,
                    $form
                ));
            }
        }
        return $forms;
    }

    /**
     * Return the character's gear.
     * @return GearArray
     */
    public function getGear(): GearArray
    {
        $gear = new GearArray();
        foreach ($this->gear ?? [] as $item) {
            try {
                $gear[] = Gear::build($item);
            } catch (\RuntimeException $e) {
                \Log::warning(sprintf(
                    'Shadowrun5E character "%s" (%s) has invalid item "%s"',
                    $this->handle,
                    $this->_id,
                    $item['id']
                ));
            }
        }
        return $gear;
    }

    /**
     * Return the character's metatype.
     * @return string
     */
    public function getMetatypeAttribute(): string
    {
        if (isset($this->priorities['metatype'])) {
            return $this->priorities['metatype'];
        }
        return 'unknown';
    }

    /**
     * Return an attribute's value with all modifiers taken into account.
     * @param string $attribute Attribute to return
     * @return int Attribute's value
     */
    public function getModifiedAttribute(string $attribute): int
    {
        $cleanAttributeName = $this->dashedToCamel($attribute);
        // @phpstan-ignore-next-line
        $modifiedAttribute = $this->$cleanAttributeName ?? 0;
        $modifiers = array_merge(
            (array)$this->getAugmentations(),
            (array)$this->getQualities()
        );

        foreach ($modifiers as $modifier) {
            foreach ($modifier->effects as $effect => $value) {
                if (
                    $attribute !== $effect
                    && $cleanAttributeName !== $this->dashedToCamel($effect)
                ) {
                    continue;
                }
                $modifiedAttribute += (int)$value;
            }
        }
        foreach ($this->getArmor() as $armor) {
            if (!$armor->active) {
                // Armor only counts if it's active.
                continue;
            }
            foreach ($armor->modifications as $mod) {
                if (0 === count($mod->effects)) {
                    continue;
                }
                if (!isset($mod->effects[$cleanAttributeName])) {
                    continue;
                }
                $modifiedAttribute += $mod->effects[$cleanAttributeName];
            }
            if (0 === count($armor->effects)) {
                // Armor has no effects.
                continue;
            }
            if (!isset($armor->effects[$cleanAttributeName])) {
                continue;
            }
            $modifiedAttribute += $armor->effects[$cleanAttributeName];
        }
        return (int)$modifiedAttribute;
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

    /**
     * Return the character's skill groups.
     * @return array<int, SkillGroup>
     */
    public function getSkillGroups(): array
    {
        $groups = [];
        foreach ($this->skillGroups ?? [] as $group => $level) {
            try {
                $groups[] = new SkillGroup($group, (int)$level);
            } catch (\RuntimeException $ex) {
                \Log::warning(sprintf(
                    'Shadowrun5E character "%s" (%s) has invalid skill group "%s"',
                    $this->handle,
                    $this->_id,
                    $group
                ));
            }
        }
        return $groups;
    }
}
