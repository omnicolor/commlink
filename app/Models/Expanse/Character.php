<?php

declare(strict_types=1);

namespace App\Models\Expanse;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use RuntimeException;

/**
 * Representation of an Expanse character.
 * @property int $accuracy
 * @property ?int $age
 * @property Background $background
 * @property ?string $campaign
 * @property int $communication
 * @property int $dexterity
 * @property string $downfall
 * @property string $drive
 * @property int $fighting
 * @property array $focuses
 * @property string $id
 * @property int $intelligence
 * @property int $level
 * @property string $name
 * @property Origin $origin
 * @property string $owner
 * @property int $perception
 * @property string $profession
 * @property string $quality
 * @property SocialClass $socialClass
 * @property int $speed
 * @property int $strength
 * @property string $system
 * @property array<int, array<string, int|string>> $talents
 * @property int $willpower
 */
class Character extends \App\Models\Character
{
    use HasFactory;

    /**
     * @var array<string, mixed>
     */
    protected $attributes = [
        'system' => 'expanse',
    ];

    /**
     * @var string[]
     */
    protected $fillable = [
        'accuracy',
        'age',
        'background',
        'campaign',
        'communication',
        'dexterity',
        'downfall',
        'drive',
        'fighting',
        'focuses',
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
        'willpower',
    ];

    /**
     * @var string[]
     */
    protected $hidden = [
        '_id',
        'abilities',
        'type',
    ];

    /**
     * Collection of Focus objects.
     * @var FocusArray
     */
    protected FocusArray $focusArray;

    /**
     * Return the character's name.
     * @return string
     */
    public function __toString(): string
    {
        return $this->name ?? 'Unnamed Character';
    }

    /**
     * Force this model to only load for Expanse characters.
     */
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
     * Return the character's background.
     * @return Background
     * @throws RuntimeException
     */
    public function getBackgroundAttribute(): Background
    {
        return new Background($this->attributes['background']);
    }

    /**
     * Return the character's focuses.
     * @return FocusArray
     */
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
                    \Log::warning(\sprintf(
                        'Expanse character "%s" (%s) has invalid focus "%s"',
                        $this->name,
                        $this->_id,
                        $focus['id']
                    ));
                }
            }
        }

        return $this->focusArray;
    }

    /**
     * Return the character's origin.
     * @return Origin
     */
    public function getOriginAttribute(): Origin
    {
        return Origin::factory($this->attributes['origin']);
    }

    /**
     * Return the character's social class.
     * @return SocialClass
     * @throws RuntimeException
     */
    public function getSocialClassAttribute(): SocialClass
    {
        return new SocialClass($this->attributes['socialClass']);
    }

    /**
     * Return the character's talents.
     * @return TalentArray
     */
    public function getTalents(): TalentArray
    {
        $talents = new TalentArray();
        foreach ($this->talents ?? [] as $talent) {
            try {
                $talents[] = new Talent(
                    (string)$talent['name'],
                    (int)($talent['level'] ?? Talent::NOVICE)
                );
            } catch (RuntimeException $ex) {
                \Log::warning(\sprintf(
                    'Expanse character "%s" (%s) has invalid talent "%s"',
                    $this->name,
                    $this->_id,
                    $talent['name']
                ));
            }
        }
        return $talents;
    }

    /**
     * Return whether the character has a given focus.
     * @param Focus $focus
     * @return bool
     */
    public function hasFocus(Focus $focus): bool
    {
        $focuses = $this->getFocuses();
        foreach ($focuses as $potentialMatch) {
            if ($focus == $potentialMatch) {
                return true;
            }
        }
        return false;
    }
}
