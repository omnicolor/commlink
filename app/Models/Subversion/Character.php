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
 * @property int $brawn
 * @property int $charisma
 * @property-read int $grit_starting
 * @property-read ?Lineage $lineage
 * @property-write Lineage|string $lineage
 * @property string $lineage_option
 * @property string $name
 * @property-read ?Origin $origin
 * @property-write Origin|string $origin
 * @property string $owner
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
        'brawn',
        'campaign_id',
        'charisma',
        'lineage',
        'lineage_option',
        'name',
        'origin',
        'owner',
        'system',
        'will',
        'wit',
    ];

    public function __toString(): string
    {
        return $this->attributes['name'] ?? 'Unnamed character';
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
}
