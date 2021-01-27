<?php

declare(strict_types=1);

namespace App\Models\Shadowrun5E;

use Illuminate\Database\Eloquent\Builder;

/**
 * Representation of a Shadowrun 5E character.
 * @property string $handle
 * @property string $id
 * @property array<int, array<string, mixed>> $qualities
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
        'handle',
        'qualities',
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
     * Return the character's qualities (if they have any).
     * @return QualityArray
     */
    public function getQualities(): QualityArray
    {
        $qualities = new QualityArray();
        if (null === $this->qualities) {
            return $qualities;
        }
        foreach ($this->qualities as $rawQuality) {
            try {
                $qualities[] = new Quality($rawQuality['id'], $rawQuality);
            } catch (\RuntimeException $ex) {
                \Log::warning(sprintf(
                    'Shadowrun5E character "%s" (%s) has invalid quality "%s"',
                    $this->handle,
                    $this->_id,
                    $rawQuality['id']
                ));
            }
        }
        return $qualities;
    }
}
