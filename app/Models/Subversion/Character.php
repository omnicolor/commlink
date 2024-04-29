<?php

declare(strict_types=1);

namespace App\Models\Subversion;

use App\Models\Character as BaseCharacter;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;

/**
 * @property string $_id
 * @property int $agility
 * @property int $arts
 * @property int $awareness
 * @property-read ?Background $background
 * @property-write Background|string $background
 * @property int $brawn
 * @property-read ?Caste $caste
 * @property-write Caste|string $caste
 * @property int $charisma
 * @property int $dulled
 * @property-read int $grit_starting
 * @property-read ?Ideology $ideology
 * @property-write Ideology|string $ideology
 * @property-read ?Impulse $impulse
 * @property-write Impulse|string $impulse
 * @property-read ?Lineage $lineage
 * @property-write Lineage|string $lineage
 * @property string $lineage_option
 * @property string $name
 * @property-read ?Origin $origin
 * @property-write Origin|string $origin
 * @property string $owner
 * @property-read array<string, Skill> $skills
 * @property-write array<int|string, Skill|array<string, int|string>> $skills
 * @property string $system
 * @property int $will
 * @property int $wit
 */
class Character extends BaseCharacter
{
    use HasFactory;

    /**
     * @var array<string, mixed>
     */
    protected $attributes = [
        'system' => 'subversion',
    ];

    protected $casts = [
        'agility' => 'int',
        'awareness' => 'int',
        'brawn' => 'int',
        'charisma' => 'int',
        'will' => 'int',
        'wit' => 'int',
    ];

    /**
     * @var array<int, string>
     */
    protected $fillable = [
        'agility',
        'arts',
        'awareness',
        'background',
        'brawn',
        'campaign_id',
        'caste',
        'charisma',
        'dulled',
        'ideology',
        'impulse',
        'lineage',
        'lineage_option',
        'name',
        'origin',
        'owner',
        'skills',
        'system',
        'will',
        'wit',
    ];

    public function __toString(): string
    {
        return $this->attributes['name'] ?? 'Unnamed character';
    }

    /**
     * @psalm-suppress PossiblyUnusedMethod
     */
    public function background(): Attribute
    {
        return Attribute::make(
            get: function (): ?Background {
                if (!isset($this->attributes['background'])) {
                    return null;
                }
                return new Background($this->attributes['background']);
            },
            set: function (Background|string $background): string {
                if ($background instanceof Background) {
                    $this->attributes['background'] = $background->id;
                    return $background->id;
                }
                $this->attributes['background'] = $background;
                return $background;
            },
        );
    }

    /**
     * Force this model to only load for Subversion characters.
     */
    protected static function booted(): void
    {
        static::addGlobalScope(
            'subversion',
            function (Builder $builder): void {
                $builder->where('system', 'subversion');
            }
        );
    }

    /**
     * @psalm-suppress PossiblyUnusedMethod
     */
    public function caste(): Attribute
    {
        return Attribute::make(
            get: function (): ?Caste {
                if (!isset($this->attributes['caste'])) {
                    return null;
                }
                return new Caste($this->attributes['caste']);
            },
            set: function (Caste|string $caste): string {
                if ($caste instanceof Caste) {
                    $this->attributes['caste'] = $caste->id;
                    return $caste->id;
                }
                $this->attributes['caste'] = $caste;
                return $caste;
            },
        );
    }

    /**
     * @psalm-suppress PossiblyUnusedMethod
     */
    public function gritStarting(): Attribute
    {
        return Attribute::make(
            get: function (): int {
                return $this->will + 6;
            },
        );
    }

    /**
     * @psalm-suppress PossiblyUnusedMethod
     */
    public function ideology(): Attribute
    {
        return Attribute::make(
            get: function (): ?Ideology {
                if (!isset($this->attributes['ideology'])) {
                    return null;
                }
                return new Ideology($this->attributes['ideology']);
            },
            set: function (Ideology|string $ideology): string {
                if ($ideology instanceof Ideology) {
                    $this->attributes['ideology'] = $ideology->id;
                    return $ideology->id;
                }
                $this->attributes['ideology'] = $ideology;
                return $ideology;
            },
        );
    }

    /**
     * @psalm-suppress PossiblyUnusedMethod
     */
    public function impulse(): Attribute
    {
        return Attribute::make(
            get: function (): ?Impulse {
                if (!isset($this->attributes['impulse'])) {
                    return null;
                }
                return new Impulse($this->attributes['impulse']);
            },
            set: function (Impulse|string $impulse): string {
                if ($impulse instanceof Impulse) {
                    $this->attributes['impulse'] = $impulse->id;
                    return $impulse->id;
                }
                $this->attributes['impulse'] = $impulse;
                return $impulse;
            },
        );
    }

    /**
     * @psalm-suppress PossiblyUnusedMethod
     */
    public function lineage(): Attribute
    {
        return Attribute::make(
            get: function (): ?Lineage {
                if (!isset($this->attributes['lineage'])) {
                    return null;
                }
                return new Lineage(
                    $this->attributes['lineage'],
                    $this->attributes['lineage_option'] ?? null,
                );
            },
            set: function (Lineage|string $lineage): string {
                if ($lineage instanceof Lineage) {
                    $this->attributes['lineage'] = $lineage->id;
                    $this->attributes['lineage_option'] = $lineage->option;
                    return $lineage->id;
                }
                $this->attributes['lineage'] = $lineage;
                return $lineage;
            },
        );
    }

    /**
     * @psalm-suppress PossiblyUnusedMethod
     */
    public function origin(): Attribute
    {
        return Attribute::make(
            get: function (): ?Origin {
                if (!isset($this->attributes['origin'])) {
                    return null;
                }
                return new Origin($this->attributes['origin']);
            },
            set: function (Origin|string $origin): string {
                if ($origin instanceof Origin) {
                    $this->attributes['origin'] = $origin->id;
                    return $origin->id;
                }
                $this->attributes['origin'] = $origin;
                return $origin;
            },
        );
    }

    /**
     * @psalm-suppress PossiblyUnusedMethod
     */
    public function skills(): Attribute
    {
        return Attribute::make(
            get: function (): array {
                $skills = Skill::all();
                foreach ($this->attributes['skills'] ?? [] as $skill) {
                    $skills[$skill['id']] = new Skill(
                        $skill['id'],
                        $skill['rank']
                    );
                }
                return $skills;
            },
            set: function (array $setSkills): array {
                $skills = [];
                foreach ($setSkills as $skill) {
                    if ($skill instanceof Skill) {
                        if (null !== $skill->rank) {
                            $skills[] = [
                                'id' => $skill->id,
                                'rank' => $skill->rank,
                            ];
                        }
                        continue;
                    }

                    $skills[] = $skill;
                }
                return ['skills' => $skills];
            },
        );
    }
}
