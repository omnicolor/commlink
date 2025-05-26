<?php

declare(strict_types=1);

namespace Modules\Expanse\Models;

use App\Casts\AsEmail;
use App\Models\Character as BaseCharacter;
use App\ValueObjects\Email;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Support\Facades\Log;
use Modules\Expanse\Database\Factories\CharacterFactory;
use Override;
use RuntimeException;
use Stringable;

/**
 * Representation of an Expanse character.
 * @property int $accuracy
 * @property ?int $age
 * @property string $appearance
 * @property Background $background
 * @property ?string $campaign
 * @property int $communication
 * @property int $constitution
 * @property int $dexterity
 * @property string $downfall
 * @property string $drive
 * @property int $experience
 * @property int $fighting
 * @property array<int, array{id: string, level?: int}> $focuses
 * @property string $gender
 * @property string $id
 * @property int $intelligence
 * @property int $level
 * @property string $name
 * @property Origin $origin
 * @property Email $owner
 * @property int $perception
 * @property string $profession
 * @property string $quality
 * @property SocialClass $socialClass
 * @property int $speed
 * @property int $strength
 * @property string $system
 * @property array<int, array<string, int|string>> $talents
 * @property int $toughness
 * @property int $willpower
 */
class Character extends BaseCharacter implements Stringable
{
    use HasFactory;

    /** @var array<string, mixed> */
    protected $attributes = [
        'system' => 'expanse',
    ];

    /** @var array<string, string> */
    protected $casts = [
        'owner' => AsEmail::class,
    ];

    /** @var list<string> */
    protected $fillable = [
        'accuracy',
        'age',
        'appearance',
        'background',
        'campaign',
        'communication',
        'constitution',
        'dexterity',
        'downfall',
        'drive',
        'experience',
        'fighting',
        'focuses',
        'gender',
        'intelligence',
        'level',
        'name',
        'origin',
        'owner',
        'perception',
        'profession',
        'quality',
        'socialClass',
        'strength',
        'talents',
        'toughness',
        'willpower',
    ];

    /** @var list<string> */
    protected $hidden = [
        '_id',
        'abilities',
        'type',
    ];

    /**
     * Collection of Focus objects.
     */
    protected FocusArray $focusArray;

    #[Override]
    public function __toString(): string
    {
        return $this->name ?? 'Unnamed Character';
    }

    /**
     * Force this model to only load for Expanse characters.
     */
    #[Override]
    protected static function booted(): void
    {
        static::addGlobalScope(
            'expanse',
            function (Builder $builder): void {
                $builder->where('system', 'expanse');
            }
        );
    }

    /**
     * @throws RuntimeException
     */
    public function getBackgroundAttribute(): Background
    {
        return new Background($this->attributes['background']);
    }

    public function getFocuses(): FocusArray
    {
        if (!isset($this->focusArray)) {
            $this->focusArray = new FocusArray();
            foreach ($this->focuses ?? [] as $focus) {
                try {
                    $this->focusArray[] = new Focus(
                        $focus['id'],
                        $focus['level'] ?? 1
                    );
                } catch (RuntimeException) {
                    Log::warning(
                        'Expanse character "{name}" ({id}) has invalid focus "{focus}"',
                        [
                            'name' => $this->name,
                            'id' => $this->id,
                            'focus' => $focus['id'],
                        ]
                    );
                }
            }
        }

        return $this->focusArray;
    }

    public function getOriginAttribute(): Origin
    {
        return Origin::factory($this->attributes['origin']);
    }

    /**
     * @throws RuntimeException
     */
    public function getSocialClassAttribute(): SocialClass
    {
        return new SocialClass($this->attributes['socialClass']);
    }

    public function getTalents(): TalentArray
    {
        $talents = new TalentArray();
        foreach ($this->talents ?? [] as $talent) {
            try {
                $talents[] = new Talent(
                    (string)$talent['name'],
                    (int)($talent['level'] ?? Talent::NOVICE)
                );
            } catch (RuntimeException) {
                Log::warning(
                    'Expanse character "{name}" ({id}) has invalid talent "{talent}"',
                    [
                        'name' => $this->name,
                        'id' => $this->id,
                        'talent' => $talent['name'],
                    ]
                );
            }
        }
        return $talents;
    }

    /**
     * Return whether the character has a given focus.
     */
    public function hasFocus(Focus $focus): bool
    {
        $focuses = $this->getFocuses();
        foreach ($focuses as $potentialMatch) {
            if ($focus->id === $potentialMatch->id) {
                return true;
            }
        }
        return false;
    }

    #[Override]
    protected static function newFactory(): Factory
    {
        return CharacterFactory::new();
    }
}
