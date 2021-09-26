<?php

declare(strict_types=1);

namespace App\Models\Shadowrun5E;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use RuntimeException;

/**
 * Representation of a Shadowrun 5E character.
 * @property int $agility
 * @property ?array<int, array<string, mixed>> $armor
 * @property ?array<int, array<string, mixed>> $augmentations
 * @property ?array<string, mixed> $background
 * @property int $body
 * @property int $charisma
 * @property ?array<int, string> $complexForms
 * @property ?array<int, array<string, string|int>> $contacts
 * @property int $edge
 * @property int $edgeCurrent
 * @property ?array<int, array<string, mixed>> $gear
 * @property ?string $gender
 * @property string $handle
 * @property string $id
 * @property ?array<int, array<string, mixed>> $identities
 * @property int $intuition
 * @property int $karma
 * @property int $karmaCurrent
 * @property ?array<int, array<string, string|int>> $karmaLog
 * @property ?array<int, array<string, string|int|null>> $knowledgeSkills
 * @property int $logic
 * @property ?array<string, array<int, string>> $martialArts
 * @property ?array<string, ?string> $priorities
 * @property int $magic
 * @property ?array<string, mixed> $magics
 * @property int $nuyen
 * @property ?array<int, array<string, mixed>> $qualities
 * @property int $reaction
 * @property ?string $realName
 * @property ?int $resonance
 * @property ?array<int, array<string, mixed>> $skills
 * @property ?array<string, ?int> $skillGroups
 * @property int $strength
 * @property ?array<string, mixed> $technomancer
 * @property ?array<int, array<string, mixed>> $vehicles
 * @property ?array<int, array<string, mixed>> $weapons
 * @property int $willpower
 */
class Character extends \App\Models\Character
{
    use HasFactory;

    /**
     * @var array<string, mixed>
     */
    protected $attributes = [
        'system' => 'shadowrun5e',
    ];

    /**
     * Attributes that need to be cast to a type.
     * @var array<string, string>
     */
    protected $casts = [
        'agility' => 'integer',
        'body' => 'integer',
        'charisma' => 'integer',
        'edge' => 'integer',
        'intuition' => 'integer',
        'logic' => 'integer',
        'magic' => 'integer',
        'reaction' => 'integer',
        'resonance' => 'integer',
        'strength' => 'integer',
        'willpower' => 'integer',
    ];

    /**
     * @var array<int, string>
     */
    protected $fillable = [
        'agility',
        'armor',
        'augmentations',
        'background',
        'birthdate',
        'birthplace',
        'body',
        'campaign',
        'charisma',
        'complexForms',
        'contacts',
        'edge',
        'edgeCurrent',
        'eyes',
        'gear',
        'gender',
        'handle',
        'height',
        'identities',
        'intuition',
        'karma',
        'karmaCurrent',
        'karmaLog',
        'knowledgeSkills',
        'logic',
        'magic',
        'magics',
        'martialArts',
        'notoriety',
        'nuyen',
        'owner',
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
        'technomancer',
        'vehicles',
        'weapons',
        'weight',
        'willpower',
    ];

    /**
     * @var array<int, string>
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
        return $this->handle ?? 'Unnamed Character';
    }

    /**
     * Force this model to only load for Shadowrun characters.
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
        $string = \str_replace('-', ' ', $string);
        $string = \ucwords($string);
        $string = \str_replace(' ', '', $string);
        $string[0] = \strtolower($string[0]);
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
            } catch (RuntimeException) {
                \Log::warning(\sprintf(
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
            } catch (RuntimeException) {
                \Log::warning(\sprintf(
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
     * Return the character's astral limit if they have one.
     * @return int
     */
    public function getAstralLimit(): int
    {
        if (!(bool)$this->magic) {
            return 0;
        }
        return \max(
            $this->getMentalLimit(),
            $this->getSocialLimit()
        );
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
            } catch (RuntimeException) {
                \Log::warning(\sprintf(
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
     * Return the character's contacts.
     * @return ContactArray
     */
    public function getContacts(): ContactArray
    {
        $contacts = new ContactArray();
        foreach ($this->contacts ?? [] as $contact) {
            $contacts[] = new Contact($contact);
        }
        return $contacts;
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
            } catch (RuntimeException) {
                \Log::warning(\sprintf(
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
     * Get the character's composure derived stat.
     * @return int
     */
    public function getComposureAttribute(): int
    {
        return $this->getModifiedAttribute('charisma') +
            $this->getModifiedAttribute('willpower');
    }

    /**
     * Return the character's effective essence.
     * @return float
     */
    public function getEssence(): float
    {
        $essence = 6;
        foreach ($this->getAugmentations() as $augmentation) {
            $essence -= $augmentation->essence;
        }
        return $essence;
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
            } catch (RuntimeException) {
                \Log::warning(\sprintf(
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
     * Return the character's identities.
     * @return IdentityArray
     */
    public function getIdentities(): IdentityArray
    {
        $identities = new IdentityArray();
        foreach ($this->identities ?? [] as $identity) {
            $identities[] = Identity::fromArray($identity);
        }
        return $identities;
    }

    /**
     * Get the character's judge intentions derived stat.
     * @return int
     */
    public function getJudgeIntentionsAttribute(): int
    {
        return $this->getModifiedAttribute('intuition') +
            $this->getModifiedAttribute('charisma');
    }

    /**
     * Return the character's karma log.
     * @return KarmaLog
     */
    public function getKarmaLog(): KarmaLog
    {
        $log = new KarmaLog();
        // New characters haven't saved a karma log to the data store.
        if (null === $this->karmaLog) {
            return $log->initialize($this);
        }
        return $log->fromArray($this->karmaLog);
    }

    /**
     * Return the character's knowledge skills.
     * @return SkillArray
     */
    public function getKnowledgeSkills(): SkillArray
    {
        $skills = new SkillArray();
        if (null === $this->knowledgeSkills) {
            return $skills;
        }
        foreach ($this->knowledgeSkills as $skill) {
            try {
                $skills[] = new KnowledgeSkill(
                    (string)$skill['name'],
                    (string)$skill['category'],
                    // @phpstan-ignore-next-line
                    $skill['level'],
                    // @phpstan-ignore-next-line
                    $skill['specialization'] ?? null
                );
            } catch (RuntimeException) {
                \Log::warning(\sprintf(
                    'Shadowrun5E character "%s" (%s) has invalid skill category "%s"',
                    $this->handle,
                    $this->_id,
                    $skill['category']
                ));
            }
        }
        return $skills;
    }

    /**
     * Return the character's lift/carry derived stat.
     * @return int
     */
    public function getLiftCarryAttribute(): int
    {
        return $this->getModifiedAttribute('body') +
            $this->getModifiedAttribute('strength');
    }

    /**
     * Return the character's martial arts styles.
     * @return MartialArtsStyleArray
     */
    public function getMartialArtsStyles(): MartialArtsStyleArray
    {
        $styles = new MartialArtsStyleArray();
        if (!isset($this->martialArts, $this->martialArts['styles'])) {
            return $styles;
        }
        foreach ($this->martialArts['styles'] as $style) {
            try {
                $styles[] = new MartialArtsStyle($style);
            } catch (RuntimeException) {
                \Log::warning(\sprintf(
                    'Shadowrun5E character "%s" (%s) has invalid martial arts style "%s"',
                    $this->handle,
                    $this->_id,
                    $style
                ));
            }
        }
        return $styles;
    }

    /**
     * Return the character's martial arts techniques.
     * @return MartialArtsTechniqueArray
     */
    public function getMartialArtsTechniques(): MartialArtsTechniqueArray
    {
        $techniques = new MartialArtsTechniqueArray();
        if (!isset($this->martialArts, $this->martialArts['techniques'])) {
            return $techniques;
        }
        foreach ($this->martialArts['techniques'] as $technique) {
            try {
                $techniques[] = new MartialArtsTechnique($technique);
            } catch (RuntimeException) {
                \Log::warning(\sprintf(
                    'Shadowrun5E character "%s" (%s) has invalid martial arts technique "%s"',
                    $this->handle,
                    $this->_id,
                    $technique
                ));
            }
        }
        return $techniques;
    }

    /**
     * Get the character's memory derived stat.
     * @return int
     */
    public function getMemoryAttribute(): int
    {
        return $this->getModifiedAttribute('logic') +
            $this->getModifiedAttribute('willpower');
    }

    /**
     * Return the character's mental limit.
     * @return int
     */
    public function getMentalLimit(): int
    {
        return (int)\ceil(
            (
                $this->getModifiedAttribute('logic') * 2
                + $this->getModifiedAttribute('intuition')
                + $this->getModifiedAttribute('willpower')
            ) / 3
        ) + $this->getModifiedAttribute('mental-limit');
    }

    /**
     * Return the character's mentor spirit (if they have one).
     * @return ?MentorSpirit
     */
    public function getMentorSpirit(): ?MentorSpirit
    {
        if (
            !isset($this->magics)
            || !\array_key_exists('mentorSpirit', $this->magics)
        ) {
            return null;
        }
        try {
            return new MentorSpirit($this->magics['mentorSpirit']);
        } catch (RuntimeException) {
            \Log::warning(\sprintf(
                'Shadowrun5E character "%s" (%s) has invalid mentor spirit "%s"',
                $this->handle,
                $this->_id,
                $this->magics['mentorSpirit']
            ));
        }
        return null;
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
        $modifiers = \array_merge(
            (array)$this->getAugmentations(),
            (array)$this->getQualities()
        );

        foreach ($modifiers as $modifier) {
            foreach ($modifier->effects as $effect => $value) {
                if (\is_int($effect)) {
                    continue;
                }
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
                if (0 === \count($mod->effects)) {
                    continue;
                }
                if (!isset($mod->effects[$cleanAttributeName])) {
                    continue;
                }
                $modifiedAttribute += $mod->effects[$cleanAttributeName];
            }
            if (0 === \count($armor->effects)) {
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
     * Return the character's physical limit.
     * @return int
     */
    public function getPhysicalLimit(): int
    {
        return (int)\ceil(
            (
                $this->getModifiedAttribute('strength') * 2
                + $this->getModifiedAttribute('body')
                + $this->getModifiedAttribute('reaction')
            ) / 3
        ) + $this->getModifiedAttribute('physical-limit');
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
            } catch (RuntimeException) {
                \Log::warning(\sprintf(
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
     * Get a limit for a particular skill.
     * @param Skill $skill
     * @return string
     */
    public function getSkillLimit(Skill $skill): string
    {
        $limitModifier = $this->getSkillLimitModifierFromQualities($skill);
        switch ($skill->limit) {
            case 'astral':
                return (string)($this->getAstralLimit() + $limitModifier);
            case 'force':
                return 'F';
            case 'handling':
                return 'H';
            case 'level':
                return 'L';
            case 'matrix':
                return 'M';
            case 'mental':
                return (string)($this->getMentalLimit() + $limitModifier);
            case 'physical':
                return (string)($this->getPhysicalLimit() + $limitModifier);
            case 'social':
                return (string)($this->getSocialLimit() + $limitModifier);
            case 'weapon':
                return 'W';
            default:
                return '?';
        }
    }

    /**
     * Some qualities modify limits for particular skills.
     * @param Skill $skill
     * @return int
     */
    protected function getSkillLimitModifierFromQualities(Skill $skill): int
    {
        if (!isset($skill->id)) {
            return 0;
        }
        $limitModifier = 0;
        foreach ($this->getQualities() as $quality) {
            foreach ($quality->effects as $effect => $value) {
                if ($effect === 'limit-' . $skill->id) {
                    $limitModifier += $value;
                }
            }
        }
        return (int)$limitModifier;
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
            } catch (RuntimeException) {
                \Log::warning(\sprintf(
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
            } catch (RuntimeException) {
                \Log::warning(\sprintf(
                    'Shadowrun5E character "%s" (%s) has invalid skill group "%s"',
                    $this->handle,
                    $this->_id,
                    $group
                ));
            }
        }
        return $groups;
    }

    /**
     * Return the character's social limit.
     * @return int
     */
    public function getSocialLimit(): int
    {
        return (int)\ceil(
            (
                \ceil($this->getEssence())
                + $this->getModifiedAttribute('willpower')
                + ($this->getModifiedAttribute('charisma') * 2)
            ) / 3
        ) + $this->getModifiedAttribute('social-limit');
    }

    /**
     * Return the character's spells.
     * @return SpellArray
     */
    public function getSpells(): SpellArray
    {
        $spells = new SpellArray();
        if (!isset($this->magics, $this->magics['spells'])) {
            return $spells;
        }
        foreach ($this->magics['spells'] as $spell) {
            try {
                $spells[] = new Spell($spell);
            } catch (RuntimeException) {
                \Log::warning(\sprintf(
                    'Shadowrun5E character "%s" (%s) has invalid spell "%s"',
                    $this->handle,
                    $this->_id,
                    $spell
                ));
            }
        }
        return $spells;
    }

    /**
     * Return the character's spirits.
     * @return SpiritArray
     */
    public function getSpirits(): SpiritArray
    {
        $spirits = new SpiritArray();
        if (!isset($this->magics, $this->magics['spirits'])) {
            return $spirits;
        }
        foreach ($this->magics['spirits'] as $spirit) {
            try {
                $spirits[] = new Spirit(
                    $spirit['id'],
                    $spirit['force'] ?? null
                );
            } catch (RuntimeException) {
                \Log::warning(\sprintf(
                    'Shadowrun5E character "%s" (%s) has invalid spirit "%s"',
                    $this->handle,
                    $this->_id,
                    $spirit['id']
                ));
            }
        }
        return $spirits;
    }

    /**
     * Return the character's sprites.
     * @return SpriteArray
     */
    public function getSprites(): SpriteArray
    {
        $sprites = new SpriteArray();
        if (!isset($this->technomancer, $this->technomancer['sprites'])) {
            return $sprites;
        }
        foreach ($this->technomancer['sprites'] as $sprite) {
            try {
                $sprites[] = new Sprite($sprite);
            } catch (RuntimeException) {
                \Log::warning(\sprintf(
                    'Shadowrun5E character "%s" (%s) has invalid sprite "%s"',
                    $this->handle,
                    $this->_id,
                    $sprite
                ));
            }
        }
        return $sprites;
    }

    /**
     * Return the character's magical tradition, if they're magical.
     * @return ?Tradition
     */
    public function getTradition(): ?Tradition
    {
        if (!isset($this->magics, $this->magics['tradition'])) {
            return null;
        }
        try {
            return new Tradition($this->magics['tradition']);
        } catch (RuntimeException) {
            \Log::warning(\sprintf(
                'Shadowrun5E character "%s" (%s) has invalid tradition "%s"',
                $this->handle,
                $this->_id,
                $this->magics['tradition']
            ));
        }
        return null;
    }

    /**
     * Return the character's vehicles.
     * @return VehicleArray
     */
    public function getVehicles(): VehicleArray
    {
        $vehicles = new VehicleArray();
        foreach ($this->vehicles ?? [] as $vehicle) {
            try {
                $vehicles[] = new Vehicle($vehicle);
            } catch (RuntimeException) {
                \Log::warning(\sprintf(
                    'Shadowrun5E character "%s" (%s) has invalid vehicle "%s"',
                    $this->handle,
                    $this->_id,
                    $vehicle['id']
                ));
            }
        }
        return $vehicles;
    }

    /**
     * Return the character's weapons.
     * @return WeaponArray
     */
    public function getWeapons(): WeaponArray
    {
        $weapons = new WeaponArray();
        foreach ($this->weapons ?? [] as $weapon) {
            try {
                $weapons[] = Weapon::buildWeapon($weapon);
            } catch (RuntimeException) {
                \Log::warning(\sprintf(
                    'Shadowrun5E character "%s" (%s) has invalid weapon "%s"',
                    $this->handle,
                    $this->_id,
                    $weapon['id']
                ));
            }
        }
        return $weapons;
    }
}
