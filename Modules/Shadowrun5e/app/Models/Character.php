<?php

declare(strict_types=1);

namespace Modules\Shadowrun5e\Models;

use App\Casts\AsEmail;
use App\Models\Character as BaseCharacter;
use App\ValueObjects\Email;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Facades\Log;
use Modules\Shadowrun5e\Database\Factories\CharacterFactory;
use Modules\Shadowrun5e\Enums\AugmentationType;
use Override;
use RuntimeException;
use Stringable;

use function array_key_exists;
use function array_merge;
use function assert;
use function ceil;
use function is_int;
use function max;
use function str_replace;
use function strtolower;
use function ucwords;

/**
 * Representation of a Shadowrun 5E character.
 * @property int $agility
 * @property ?array<int, array<string, mixed>> $armor
 * @property-read int $astral_limit
 * @property ?array<int, array<string, mixed>> $augmentations
 * @property ?array<string, mixed> $background
 * @property int $body
 * @property int $charisma
 * @property ?array<int, string> $complexForms
 * @property-read int $composure
 * @property ?array<int, array<string, string|int>> $contacts
 * @property int $damageOverflow
 * @property int $damagePhysical
 * @property int $damageStun
 * @property-read int $defense_melee
 * @property int $edge
 * @property int $edgeCurrent
 * @property ?array<int, array<string, mixed>> $gear
 * @property ?string $gender
 * @property string $handle
 * @property-read string $id
 * @property ?array<int, array<string, mixed>> $identities
 * @property int $initiative_dice
 * @property int $initiative_score
 * @property int $intuition
 * @property int $judge_intentions
 * @property int $karma
 * @property int $karmaCurrent
 * @property ?array<int, array<string, string|int>> $karmaLog
 * @property ?array<int, array<string, string|int|null>> $knowledgeSkills
 * @property int $lift_carry
 * @property int $logic
 * @property ?array<string, array<int, string>> $martialArts
 * @property int $memory
 * @property-read int $melee_defense
 * @property-read int $mental_limit
 * @property-read int $overflow_monitor
 * @property ?array<string, bool|null|string> $priorities
 * @property ?int $magic
 * @property ?array<string, mixed> $magics
 * @property int $nuyen
 * @property Email $owner
 * @property-read int $physical_limit
 * @property-read int $physical_monitor
 * @property ?array<int, array<string, mixed>> $qualities
 * @property-read int $ranged_defense
 * @property int $reaction
 * @property ?string $realName
 * @property ?int $resonance
 * @property ?array<int, array<string, mixed>> $skills
 * @property ?array<string, ?int> $skillGroups
 * @property-read int $soak
 * @property-read int $social_limit
 * @property int $strength
 * @property-read int $stun_monitor
 * @property ?array<string, mixed> $technomancer
 * @property ?array<int, array<string, mixed>> $vehicles
 * @property ?array<int, array<string, mixed>> $weapons
 * @property int $willpower
 */
class Character extends BaseCharacter implements Stringable
{
    use HasFactory;
    use Notifiable;

    /** @var array<string, mixed> */
    protected $attributes = [
        'system' => 'shadowrun5e',
    ];

    /** @var array<string, string> */
    protected $casts = [
        'agility' => 'integer',
        'body' => 'integer',
        'charisma' => 'integer',
        'edge' => 'integer',
        'intuition' => 'integer',
        'logic' => 'integer',
        'magic' => 'integer',
        'owner' => AsEmail::class,
        'reaction' => 'integer',
        'resonance' => 'integer',
        'strength' => 'integer',
        'willpower' => 'integer',
    ];

    /** @var list<string> */
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
        'damageOverflow',
        'damagePhysical',
        'damageStun',
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

    /** @var list<string> */
    protected $hidden = [
        '_id',
    ];

    #[Override]
    public function __toString(): string
    {
        return $this->handle ?? 'Unnamed Character';
    }

    /**
     * Force this model to only load for Shadowrun 5E characters.
     */
    #[Override]
    protected static function booted(): void
    {
        static::addGlobalScope(
            'shadowrun5e',
            static function (Builder $builder): void {
                $builder->where('system', 'shadowrun5e');
            }
        );
    }

    /**
     * Change a dashed ID to a camel case ID.
     */
    protected function dashedToCamel(string $string): string
    {
        if ('' === $string) {
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
                Log::warning(
                    'Shadowrun 5E character "{name}" ({id}) has invalid adept power "{power}"',
                    [
                        'name' => $this->handle,
                        'id' => $this->id,
                        'power' => $power,
                    ]
                );
            }
        }
        return $powers;
    }

    /**
     * Return the character's armor.
     */
    public function getArmor(): ArmorArray
    {
        $armor = new ArmorArray();
        if (null === $this->armor) {
            return $armor;
        }
        foreach ($this->armor as $raw_armor) {
            try {
                $armor[] = Armor::build($raw_armor);
            } catch (RuntimeException) {
                Log::warning(
                    'Shadowrun 5E character "{name}" ({id}) has invalid armor "{armor}"',
                    [
                        'handle' => $this->handle,
                        'id' => $this->id,
                        'armor' => $raw_armor['id'],
                    ]
                );
            }
        }
        return $armor;
    }

    /**
     * Return the character's armor value.
     */
    public function getArmorValue(): int
    {
        $armor_value = 0;
        $stack_value = 0;
        foreach ($this->getArmor() as $armor) {
            if (!$armor->active) {
                continue;
            }
            if (null !== $armor->stackRating) {
                $stack_value += $armor->stackRating;
            }
            $armor_value = max($armor->getModifiedRating(), $armor_value);
        }
        return $armor_value + $stack_value;
    }

    /**
     * Return the character's astral limit if they have one.
     */
    public function astralLimit(): Attribute
    {
        return Attribute::make(
            get: function (): int {
                if (!(bool)$this->magic) {
                    return 0;
                }
                return max($this->mental_limit, $this->social_limit);
            },
        );
    }

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
                Log::warning(
                    'Shadowrun 5E character "{name}" ({id}) has invalid augmentation "{augmentation}"',
                    [
                        'name' => $this->handle,
                        'id' => $this->id,
                        'augmentation' => $augmentation['id'],
                    ]
                );
            }
        }
        return $augmentations;
    }

    public function getContacts(): ContactArray
    {
        $contacts = new ContactArray();
        foreach ($this->contacts ?? [] as $contact) {
            $contacts[] = new Contact($contact);
        }
        return $contacts;
    }

    public function getComplexForms(): ComplexFormArray
    {
        $forms = new ComplexFormArray();
        foreach ($this->complexForms ?? [] as $form) {
            try {
                $forms[] = new ComplexForm($form);
            } catch (RuntimeException) {
                Log::warning(
                    'Shadowrun 5E character "{name}" ({id}) has invalid complex form "{form}"',
                    [
                        'name' => $this->handle,
                        'id' => $this->id,
                        'form' => $form,
                    ]
                );
            }
        }
        return $forms;
    }

    /**
     * Get the character's composure derived stat.
     */
    public function composure(): Attribute
    {
        return Attribute::make(
            get: function (): int {
                return $this->getModifiedAttribute('charisma') +
                    $this->getModifiedAttribute('willpower');
            },
        );
    }

    /**
     * Return the character's effective essence.
     */
    public function getEssenceAttribute(): float
    {
        $essence = 6;
        $modifier_bioware = 1;
        $modifier_cyberware = 1;
        foreach ($this->getQualities() as $quality) {
            if (isset($quality->effects['cyberware-essence-multiplier'])) {
                $modifier_cyberware *= $quality->effects['cyberware-essence-multiplier'];
            }
            if (isset($quality->effects['bioware-essence-multiplier'])) {
                $modifier_bioware *= $quality->effects['bioware-essence-multiplier'];
            }
        }

        foreach ($this->getAugmentations() as $augmentation) {
            if (AugmentationType::Cyberware === $augmentation->type) {
                $essence -= $augmentation->essence * $modifier_cyberware;
                continue;
            }
            $essence -= $augmentation->essence * $modifier_bioware;
        }
        return $essence;
    }

    public function getGear(): GearArray
    {
        $gear = new GearArray();
        foreach ($this->gear ?? [] as $item) {
            try {
                $gear[] = Gear::build($item);
            } catch (RuntimeException) {
                Log::warning(
                    'Shadowrun 5E character "{name}" ({id}) has invalid item "{item}"',
                    [
                        'name' => $this->handle,
                        'id' => $this->id,
                        'item' => $item['id'],
                    ]
                );
            }
        }
        return $gear;
    }

    public function getIdentities(): IdentityArray
    {
        $identities = new IdentityArray();
        foreach ($this->identities ?? [] as $identity) {
            $identities[] = Identity::fromArray($identity);
        }
        return $identities;
    }

    /**
     * Return the character's real-world initiative.
     */
    public function initiativeScore(): Attribute
    {
        return Attribute::make(
            get: function (): int {
                return $this->getModifiedAttribute('reaction')
                    + $this->getModifiedAttribute('intuition')
                    + $this->getModifiedAttribute('initiative');
            },
        );
    }

    public function initiativeDice(): Attribute
    {
        return Attribute::make(
            get: function (): int {
                return 1 + $this->getModifiedAttribute('initiative-dice');
            },
        );
    }

    /**
     * Get the character's judge intentions derived stat.
     */
    public function judgeIntentions(): Attribute
    {
        return Attribute::make(
            get: function (): int {
                return $this->getModifiedAttribute('intuition') +
                    $this->getModifiedAttribute('charisma');
            },
        );
    }

    /**
     * Return the character's karma log (record of changes in karma).
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
     * @param bool|null $only_languages Only include languages
     * @param bool|null $only_knowledges Only include non-language knowledge skills
     */
    public function getKnowledgeSkills(
        ?bool $only_languages = null,
        ?bool $only_knowledges = null,
    ): SkillArray {
        $skills = new SkillArray();
        if (null === $this->knowledgeSkills) {
            return $skills;
        }
        foreach ($this->knowledgeSkills as $skill) {
            if (true === $only_languages && 'language' !== $skill['category']) {
                continue;
            }
            if (true === $only_knowledges && 'language' === $skill['category']) {
                continue;
            }

            try {
                $specialization = $skill['specialization'] ?? null;
                if (null !== $specialization) {
                    $specialization = (string)$specialization;
                }
                $skills[] = new KnowledgeSkill(
                    (string)$skill['name'],
                    (string)$skill['category'],
                    $skill['level'] ?? 'N',
                    $specialization,
                );
            } catch (RuntimeException) {
                Log::warning(
                    'Shadowrun 5E character "{name}" ({id}) has invalid skill category "{category}"',
                    [
                        'name' => $this->handle,
                        'id' => $this->id,
                        'category' => $skill['category'],
                    ]
                );
            }
        }
        return $skills;
    }

    /**
     * Return the character's lift/carry derived stat.
     */
    public function liftCarry(): Attribute
    {
        return Attribute::make(
            get: function (): int {
                return $this->getModifiedAttribute('body')
                    + $this->getModifiedAttribute('strength');
            },
        );
    }

    public function getMartialArtsStyles(): MartialArtsStyleArray
    {
        $styles = new MartialArtsStyleArray();
        if (!isset($this->martialArts['styles'])) {
            return $styles;
        }
        foreach ($this->martialArts['styles'] as $style) {
            try {
                $styles[] = new MartialArtsStyle($style);
            } catch (RuntimeException) {
                Log::warning(
                    'Shadowrun 5E character "{name}" ({id}) has invalid martial arts style "{style}"',
                    [
                        'name' => $this->handle,
                        'id' => $this->id,
                        'style' => $style,
                    ]
                );
            }
        }
        return $styles;
    }

    public function getMartialArtsTechniques(): MartialArtsTechniqueArray
    {
        $techniques = new MartialArtsTechniqueArray();
        if (!isset($this->martialArts['techniques'])) {
            return $techniques;
        }
        foreach ($this->martialArts['techniques'] as $technique) {
            try {
                $techniques[] = new MartialArtsTechnique($technique);
            } catch (RuntimeException) {
                Log::warning(
                    'Shadowrun 5E character "{name}" ({id}) has invalid martial arts technique "{technique}"',
                    [
                        'name' => $this->handle,
                        'id' => $this->id,
                        'technique' => $technique,
                    ]
                );
            }
        }
        return $techniques;
    }

    /**
     * Get the character's memory derived stat.
     */
    public function getMemoryAttribute(): int
    {
        return $this->getModifiedAttribute('logic') +
            $this->getModifiedAttribute('willpower');
    }

    public function mentalLimit(): Attribute
    {
        return Attribute::make(
            get: function (): int {
                return (int)ceil(
                    (
                        $this->getModifiedAttribute('logic') * 2
                        + $this->getModifiedAttribute('intuition')
                        + $this->getModifiedAttribute('willpower')
                    ) / 3
                ) + $this->getModifiedAttribute('mental-limit');
            },
        );
    }

    /**
     * Return the character's mentor spirit (if they have one).
     */
    public function getMentorSpirit(): ?MentorSpirit
    {
        if (
            !isset($this->magics)
            || !array_key_exists('mentorSpirit', $this->magics)
        ) {
            return null;
        }
        try {
            return new MentorSpirit($this->magics['mentorSpirit']);
        } catch (RuntimeException) {
            Log::warning(
                'Shadowrun 5E character "{name}" ({id}) has invalid mentor spirit "{spirit}"',
                [
                    'name' => $this->handle,
                    'id' => $this->id,
                    'spirit' => $this->magics['mentorSpirit'],
                ]
            );
        }
        return null;
    }

    public function getMetatypeAttribute(): string
    {
        if (isset($this->priorities['metatype'])) {
            return (string)$this->priorities['metatype'];
        }
        return 'unknown';
    }

    /**
     * Return an attribute's value with all modifiers taken into account.
     * @param string $attribute Attribute to return
     */
    public function getModifiedAttribute(string $attribute): int
    {
        $clean_attribute_name = $this->dashedToCamel($attribute);
        $modified_attribute = $this->attributes[$clean_attribute_name] ?? 0;
        /** @var array<int, AdeptPower|Augmentation|Quality> $modifiers */
        $modifiers = array_merge(
            (array)$this->getAugmentations(),
            (array)$this->getQualities(),
            (array)$this->getAdeptPowers(),
        );

        foreach ($modifiers as $modifier) {
            foreach ($modifier->effects as $effect => $value) {
                if (is_int($effect)) {
                    continue;
                }
                if (
                    $attribute !== $effect
                    && $clean_attribute_name !== $this->dashedToCamel($effect)
                ) {
                    continue;
                }
                $modified_attribute += (int)$value;
            }
        }
        foreach ($this->getArmor() as $armor) {
            if (!$armor->active) {
                // Armor only counts if it's active.
                continue;
            }
            foreach ($armor->modifications as $mod) {
                if ([] === $mod->effects) {
                    continue;
                }
                if (!isset($mod->effects[$clean_attribute_name])) {
                    continue;
                }
                $modified_attribute += $mod->effects[$clean_attribute_name];
            }
            if ([] === $armor->effects) {
                // Armor has no effects.
                continue;
            }
            if (!isset($armor->effects[$clean_attribute_name])) {
                continue;
            }
            $modified_attribute += $armor->effects[$clean_attribute_name];
        }
        return (int)$modified_attribute;
    }

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
                Log::warning(
                    'Shadowrun 5E character "{name}" ({id}) has invalid quality "{quality}"',
                    [
                        'name' => $this->handle,
                        'id' => $this->id,
                        'quality' => $quality['id'],
                    ]
                );
            }
        }
        return $qualities;
    }

    public function getSkillLimit(Skill $skill): string
    {
        $limit_modifier = $this->getSkillLimitModifierFromQualities($skill);
        return match ($skill->limit) {
            'astral' => (string)($this->astral_limit + $limit_modifier),
            'force' => 'F',
            'handling' => 'H',
            'level' => 'L',
            'matrix' => 'M',
            'mental' => (string)($this->mental_limit + $limit_modifier),
            'physical' => (string)($this->physical_limit + $limit_modifier),
            'social' => (string)($this->social_limit + $limit_modifier),
            'weapon' => 'W',
            default => '?',
        };
    }

    /**
     * Some qualities modify limits for particular skills.
     */
    protected function getSkillLimitModifierFromQualities(Skill $skill): int
    {
        // Ignore knowledge skills.
        if ($skill instanceof KnowledgeSkill) {
            return 0;
        }
        assert($skill instanceof ActiveSkill);

        $limit_modifier = 0;
        foreach ($this->getQualities() as $quality) {
            foreach ($quality->effects as $effect => $value) {
                if ($effect === 'limit-' . $skill->id) {
                    $limit_modifier += $value;
                }
            }
        }
        return (int)$limit_modifier;
    }

    /**
     * Return the character's active skills.
     */
    public function getSkills(): SkillArray
    {
        $skills = new SkillArray();
        if (null === $this->skills) {
            return $skills;
        }
        foreach ($this->skills as $skill) {
            try {
                $skills[$skill['id']] = new ActiveSkill(
                    $skill['id'],
                    $skill['level'],
                    $skill['specialization'] ?? null
                );
            } catch (RuntimeException) {
                Log::warning(
                    'Shadowrun 5E character "{name}" ({id}) has invalid skill "{skill}"',
                    [
                        'name' => $this->handle,
                        'id' => $this->id,
                        'skill' => $skill['id'],
                    ]
                );
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
                Log::warning(
                    'Shadowrun 5E character "{name}" ({id}) has invalid skill group "{group}"',
                    [
                        'name' => $this->handle,
                        'id' => $this->id,
                        'group' => $group,
                    ]
                );
            }
        }
        return $groups;
    }

    /**
     * Return the character's soak dice pool.
     */
    public function getSoakAttribute(): int
    {
        $modifier = 0;
        foreach ($this->getAugmentations() as $augmentation) {
            if (!isset($augmentation->effects['damage-resistance'])) {
                continue;
            }
            $modifier += $augmentation->effects['damage-resistance'];
        }
        $mentor = $this->getMentorSpirit();
        if ($mentor instanceof MentorSpirit) {
            $modifier += $mentor->effects['damage-resistance'] ?? 0;
        }
        return $this->getModifiedAttribute('body') + $modifier
            + $this->getArmorValue();
    }

    public function socialLimit(): Attribute
    {
        return Attribute::make(
            get: function (): int {
                return (int)ceil(
                    (
                        ceil($this->getEssenceAttribute())
                        + $this->getModifiedAttribute('willpower')
                        + ($this->getModifiedAttribute('charisma') * 2)
                    ) / 3
                ) + $this->getModifiedAttribute('social-limit');
            },
        );
    }

    public function getSpells(): SpellArray
    {
        $spells = new SpellArray();
        if (!isset($this->magics['spells'])) {
            return $spells;
        }
        foreach ($this->magics['spells'] as $spell) {
            try {
                $spells[] = new Spell($spell);
            } catch (RuntimeException) {
                Log::warning(
                    'Shadowrun 5E character "{name}" ({id}) has invalid spell "{spell}"',
                    [
                        'name' => $this->handle,
                        'id' => $this->id,
                        'spell' => $spell,
                    ]
                );
            }
        }
        return $spells;
    }

    public function getSpirits(): SpiritArray
    {
        $spirits = new SpiritArray();
        if (!isset($this->magics['spirits'])) {
            return $spirits;
        }
        foreach ($this->magics['spirits'] as $spirit) {
            try {
                $spirits[] = new Spirit(
                    $spirit['id'],
                    $spirit['force'] ?? null
                );
            } catch (RuntimeException) {
                Log::warning(
                    'Shadowrun 5E character "{name}" ({id}) has invalid spirit "{spirit}"',
                    [
                        'name' => $this->handle,
                        'id' => $this->id,
                        'spirit' => $spirit['id'],
                    ]
                );
            }
        }
        return $spirits;
    }

    public function getSprites(): SpriteArray
    {
        $sprites = new SpriteArray();
        if (!isset($this->technomancer['sprites'])) {
            return $sprites;
        }
        foreach ($this->technomancer['sprites'] as $sprite) {
            try {
                $sprites[] = new Sprite($sprite);
            } catch (RuntimeException) {
                Log::warning(
                    'Shadowrun 5E character "{name}" ({id}) has invalid sprite "{sprite}"',
                    [
                        'name' => $this->handle,
                        'id' => $this->id,
                        'sprite' => $sprite,
                    ]
                );
            }
        }
        return $sprites;
    }

    /**
     * Return the character's magical tradition, if they're magical.
     */
    public function getTradition(): ?Tradition
    {
        if (!isset($this->magics['tradition'])) {
            return null;
        }
        try {
            return new Tradition($this->magics['tradition']);
        } catch (RuntimeException) {
            Log::warning(
                'Shadowrun 5E character "{name}" ({id}) has invalid tradition "{tradition}"',
                [
                    'name' => $this->handle,
                    'id' => $this->id,
                    'tradition' => $this->magics['tradition'],
                ]
            );
        }
        return null;
    }

    public function getVehicles(): VehicleArray
    {
        $vehicles = new VehicleArray();
        foreach ($this->vehicles ?? [] as $vehicle) {
            try {
                $vehicles[] = new Vehicle($vehicle);
            } catch (RuntimeException $ex) {
                Log::warning(
                    'Shadowrun 5E character "{name}" ({id}) has invalid vehicle "{vehicle}"',
                    [
                        'name' => $this->handle,
                        'id' => $this->id,
                        'vehicle' => $vehicle['id'],
                        'exception' => $ex->getMessage(),
                    ]
                );
            }
        }
        return $vehicles;
    }

    public function getWeapons(): WeaponArray
    {
        $weapons = new WeaponArray();
        foreach ($this->weapons ?? [] as $weapon) {
            try {
                $weapons[] = Weapon::buildWeapon($weapon);
            } catch (RuntimeException) {
                Log::warning(
                    'Shadowrun 5E character "{name}" ({id}) has invalid weapon "{weapon}"',
                    [
                        'name' => $this->handle,
                        'id' => $this->id,
                        'weapon' => $weapon['id'],
                    ]
                );
            }
        }
        return $weapons;
    }

    public function meleeDefense(): Attribute
    {
        return Attribute::make(
            get: function (): int {
                return $this->getModifiedAttribute('reaction')
                    + $this->getModifiedAttribute('intuition')
                    + $this->getModifiedAttribute('melee-defense');
            },
        );
    }

    #[Override]
    protected static function newFactory(): Factory
    {
        return CharacterFactory::new();
    }

    public function physicalLimit(): Attribute
    {
        return Attribute::make(
            get: function (): int {
                return (int)ceil(
                    (
                        $this->getModifiedAttribute('strength') * 2
                        + $this->getModifiedAttribute('body')
                        + $this->getModifiedAttribute('reaction')
                    ) / 3
                ) + $this->getModifiedAttribute('physical-limit');
            },
        );
    }

    public function overflowMonitor(): Attribute
    {
        return Attribute::make(
            get: function (): int {
                return $this->getModifiedAttribute('body')
                    + $this->getModifiedAttribute('damage-overflow');
            },
        );
    }

    public function physicalMonitor(): Attribute
    {
        return Attribute::make(
            get: function (): int {
                return (int)ceil($this->getModifiedAttribute('body') / 2) + 8;
            },
        );
    }

    public function rangedDefense(): Attribute
    {
        return Attribute::make(
            get: function (): int {
                return $this->getModifiedAttribute('reaction')
                    + $this->getModifiedAttribute('intuition')
                    + $this->getModifiedAttribute('range-defense');
            },
        );
    }

    public function stunMonitor(): Attribute
    {
        return Attribute::make(
            get: function (): int {
                return (int)ceil($this->getModifiedAttribute('willpower') / 2) + 8;
            },
        );
    }
}
